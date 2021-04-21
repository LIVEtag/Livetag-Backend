<?php

namespace common\components\queue\stream;

use common\components\FileSystem\media\MediaTypeEnum;
use common\exception\RetryJobException;
use common\helpers\LogHelper;
use common\models\Stream\StreamSession;
use common\models\Stream\StreamSessionArchive;
use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Create Entity and generate psoter
 * Class CreateVideoFromWebhookJob
 */
class SaveArchiveFromWebhookJob extends BaseObject implements JobInterface, RetryableJobInterface
{
    const ARCHIVE_NAME = 'archive.mp4';
    const WEBHOOK_FIELD_ARCHIVE_ID = 'id';
    const WEBHOOK_FIELD_SESSION_ID = 'sessionId';
    const WEBHOOK_FIELD_PARTNER_ID = 'partnerId';
    const WEBHOOK_FIELD_NAME = 'name';
    const WEBHOOK_FIELD_SIZE = 'size';

    /**
     * most important keys, that required in logic
     */
    const REQUIRED_WEBHOOK_KEYS = [
        self::WEBHOOK_FIELD_ARCHIVE_ID,
        self::WEBHOOK_FIELD_SESSION_ID,
        self::WEBHOOK_FIELD_PARTNER_ID,
        self::WEBHOOK_FIELD_SIZE,
    ];

    /**
     * Delay of webhook processing in seconds
     */
    const JOB_DELAY = 15 * 60; // 15 min

    /**
     * Webhook data
     * @var array
     */
    public $data;

    /** @var string */
    protected $archiveId;

    /** @var string */
    protected $vonageSessionId;

    /** @var string */
    protected $apiKey;

    /** @var int */
    protected $size;

    /**
     * category for logs
     */
    const LOG_CATEGORY = 'archive';

    /**
     * @inheritdoc
     */
    public function getTtr()
    {
        return self::JOB_DELAY;
    }

    /**
     * @inheritdoc
     */
    public function canRetry($attempt, $error)
    {
        return ($attempt < 5) && ($error instanceof RetryJobException);
    }

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        LogHelper::info('Create Archive from webhook', self::LOG_CATEGORY, $this->data);
        $this->saveArchiveFromWebhook();
    }

    /**
     * Generate entity from webhook and return it
     */
    public function saveArchiveFromWebhook()
    {
        // 1. Extract values from webhook
        if (!$this->extractData()) {
            return;
        }
        // 2. Try to find session in database
        $streamSession = StreamSession::find()->byExternalId($this->vonageSessionId)->one();
        if (!$streamSession) {
            LogHelper::error('Session not found', self::LOG_CATEGORY, $this->data);
            return;
        }

        // 3. Check if archive already exists in database (user can upload video manualy)
        // Note: we assume, that there should be only one video file (Our maximum: 3 hours. Vonage maximum - 4 hours)
        if ($streamSession->archive) {
            LogHelper::error(
                'Archive already exists',
                self::LOG_CATEGORY,
                $this->data,
                [LogHelper::TAG_STREAM_SESSION_ID => $streamSession->id]
            );
            return;
        }

        //4. Get file url/path
        // Note: Assumption that archive in same S3 bucket, as manualy uploaded files
        $path = $this->getArchivePath($this->apiKey, $this->archiveId);
        $url = StreamSessionArchive::getUrlWIthoutPrefix($path);
        // in some cases, the video is not immediately accessible via the link (presumably does not have time to load from the toxbox to s3)
        // check video exist (if not - re-create job with delay)
        if (!self::checkUrl($url)) {
            $message = 'Video file not exist';
            LogHelper::error(
                $message,
                self::LOG_CATEGORY,
                ['url' => $url, 'webhook' => $this->data],
                [LogHelper::TAG_STREAM_SESSION_ID => $streamSession->id]
            );
            throw new RetryJobException($message);
        }

        // 5. Create entity and populate params
        $archive = new StreamSessionArchive();
        $archive->setAttributes([
            'externalId' => $this->archiveId,
            'streamSessionId' => $streamSession->getId(),
            'path' => $path,
            'type' => MediaTypeEnum::TYPE_VIDEO,
            'status' => StreamSessionArchive::STATUS_NEW,
            'originName' => ArrayHelper::getValue($this->data, self::WEBHOOK_FIELD_NAME) ?: self::ARCHIVE_NAME,
            'size' => $this->size,
        ]);

        // 6. Try to save Archive entity
        if (!$archive->save()) {
            LogHelper::error(
                'Failed to save Archive',
                self::LOG_CATEGORY,
                [
                    'model' => Json::encode($archive->toArray(), JSON_PRETTY_PRINT),
                    'errors' => $archive->getErrors(),
                    'webhook' => $this->data
                ],
                [LogHelper::TAG_STREAM_SESSION_ID => $streamSession->id]
            );
            return;
        }

        LogHelper::info(
            'Archive saved',
            self::LOG_CATEGORY,
            [
                'model' => Json::encode($archive->toArray(), JSON_PRETTY_PRINT),
                'webhook' => $this->data
            ],
            [LogHelper::TAG_STREAM_SESSION_ID => $streamSession->id]
        );
    }

    /**
     * @param int $projectId
     * @param string $id
     * @return string
     */
    public function getArchivePath($projectId, $id)
    {
        return $projectId . '/' . $id . '/' . self::ARCHIVE_NAME;
    }

    /**
     * Check S3 file status
     */
    public static function checkUrl($url)
    {
        $headers = get_headers($url);
        return (substr($headers[0], 9, 3) === '200');
    }

    /**
     * Extrat and validate data
     * @return bool
     */
    protected function extractData(): bool
    {
        // Validate required keys (in order not to check each key individually)
        $missingFields = array_diff(self::REQUIRED_WEBHOOK_KEYS, array_keys($this->data));
        if ($missingFields) {
            LogHelper::error('Body has missing elements: ' . implode(',', $missingFields), self::LOG_CATEGORY, $this->data);
            return false;
        }
        return $this->extractApiKey() && $this->extractSize() && $this->extractArchiveId() && $this->extractVonageSessionId();
    }

    /**
     * Extract and check size
     * @return bool
     */
    protected function extractSize(): bool
    {
        $this->size = ArrayHelper::getValue($this->data, self::WEBHOOK_FIELD_SIZE);
        if (!$this->size) {
            LogHelper::error(
                'Archive has zero size',
                self::LOG_CATEGORY,
                $this->data,
                [LogHelper::TAG_STREAM_SESSION_ID => $this->streamSession->id]
            );
            return false;
        }
        return true;
    }

    /**
     * Check API KEY just in case wrong config
     * @return bool
     */
    protected function extractApiKey(): bool
    {
        $this->apiKey = ArrayHelper::getValue($this->data, self::WEBHOOK_FIELD_PARTNER_ID);
        if ($this->apiKey != Yii::$app->vonage->apiKey) {
            LogHelper::error('This callback do not belongs to this server', self::LOG_CATEGORY, $this->data);
            return false;
        }
        return true;
    }

    /**
     * Try to extract archive id
     * @return bool
     */
    protected function extractArchiveId(): bool
    {
        $this->archiveId = ArrayHelper::getValue($this->data, self::WEBHOOK_FIELD_ARCHIVE_ID);
        return !!$this->archiveId;
    }

    /**
     * Try to extract vonage session id
     * @return bool
     */
    protected function extractVonageSessionId(): bool
    {
        $this->vonageSessionId = ArrayHelper::getValue($this->data, self::WEBHOOK_FIELD_SESSION_ID);
        return !!$this->vonageSessionId;
    }
}
