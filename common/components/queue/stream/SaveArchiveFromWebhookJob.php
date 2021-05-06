<?php
/*
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\queue\stream;

use common\components\FileSystem\media\MediaTypeEnum;
use common\exception\FfmpegException;
use common\exception\RetryJobException;
use common\helpers\FileHelper;
use common\helpers\LogHelper;
use common\models\Stream\StreamSession;
use common\models\Stream\StreamSessionArchive;
use Exception;
use League\Flysystem\Adapter\AbstractAdapter;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Save archive in database
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
    const WEBHOOK_FIELD_DURATION = 'duration';

    /**
     * most important keys, that required in logic
     */
    const REQUIRED_WEBHOOK_KEYS = [
        self::WEBHOOK_FIELD_ARCHIVE_ID,
        self::WEBHOOK_FIELD_SESSION_ID,
        self::WEBHOOK_FIELD_PARTNER_ID,
        self::WEBHOOK_FIELD_SIZE,
        self::WEBHOOK_FIELD_DURATION,
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

    /** @var int */
    protected $duration;

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
     * @SuppressWarnings(PHPMD.NPathComplexity) | note: there is few operations with lot of logs records
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
        $originalPath = $this->getArchivePath($this->apiKey, $this->archiveId);
        $url = FileHelper::getUrlWIthoutPrefix($originalPath);
        // in some cases, the video is not immediately accessible via the link (presumably does not have time to load from the toxbox to s3)
        // check video exist (if not - re-create job with delay)
        if (!FileHelper::fileFromUrlExists($url)) {
            $message = 'Video file not exist';
            LogHelper::error(
                $message,
                self::LOG_CATEGORY,
                ['url' => $url, 'webhook' => $this->data],
                [LogHelper::TAG_STREAM_SESSION_ID => $streamSession->id]
            );
            throw new RetryJobException($message);
        }

        //5. Create entity (required to receive relative path)
        $archive = new StreamSessionArchive(['streamSessionId' => $streamSession->getId()]);

        //6. Move video to project folder and rotate if required
        try {
            $path = $this->moveAndRotateVideo($originalPath, $url, $streamSession->rotate, $archive->getRelativePath());
        } catch (Throwable $ex) {
            $extra = LogHelper::extraForException($streamSession, $ex);
            if ($ex instanceof FfmpegException) {
                $extra['command'] = $ex->getCommand();
                $extra['ffmpegErrors'] = $ex->getFfmpegErros();
            }
            LogHelper::error(
                'Failed to rotate video',
                self::LOG_CATEGORY,
                $extra,
                [LogHelper::TAG_STREAM_SESSION_ID => $streamSession->id]
            );
            return;
        }

        // 7. Populate params
        $archive->setAttributes([
            'externalId' => $this->archiveId,
            'path' => $path,
            'type' => MediaTypeEnum::TYPE_VIDEO,
            'status' => StreamSessionArchive::STATUS_NEW,
            'originName' => ArrayHelper::getValue($this->data, self::WEBHOOK_FIELD_NAME) ?: self::ARCHIVE_NAME,
            'size' => $this->size,
            'duration' => $this->duration,
        ]);

        //8. Try to save Archive entity
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
            [
                LogHelper::TAG_STREAM_SESSION_ID => $streamSession->id,
                LogHelper::TAG_ARCHIVE_ID => $archive->id
            ]
        );

        if (!$archive->sendToQueue()) {
            LogHelper::error(
                'Failed to send archive to processing queue',
                self::LOG_CATEGORY,
                [
                    'model' => Json::encode($archive->toArray(), JSON_PRETTY_PRINT),
                    'webhook' => $this->data
                ],
                [LogHelper::TAG_STREAM_SESSION_ID => $streamSession->id]
            );
            return;
        }
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
        return $this->extractApiKey()
            && $this->extractSize()
            && $this->extractDuration()
            && $this->extractArchiveId()
            && $this->extractVonageSessionId();
    }

    /**
     * Extract and check size
     * @return bool
     */
    protected function extractSize(): bool
    {
        $this->size = ArrayHelper::getValue($this->data, self::WEBHOOK_FIELD_SIZE);
        if (!$this->size) {
            LogHelper::error('Archive has zero size', self::LOG_CATEGORY, $this->data);
            return false;
        }
        return true;
    }

    /**
     * Extract and check duration
     * @return bool
     */
    protected function extractDuration(): bool
    {
        $this->duration = ArrayHelper::getValue($this->data, self::WEBHOOK_FIELD_DURATION);
        if (!$this->duration) {
            LogHelper::error('Archive has zero duration', self::LOG_CATEGORY, $this->data);
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

    /**
     * Moove video to project preffix folder
     * If rotation required - rotate and save new file. Remove old file.
     *
     *
     * @param string $path s3 path of file
     * @param string $url full url of video
     * @param string $rotate rotation angle (clockwise)
     * @param string $relativePath relative path to save rotated version of video
     * @return string
     * @throws FfmpegException
     */
    protected function moveAndRotateVideo($path, $url, $rotate, $relativePath): string
    {
        if ($rotate) {
            $ffmpeg = Yii::$app->params['ffmpeg'];
            $tmpVideoPath = Yii::getAlias('@runtime/' . uniqid() . '_' . time() . '.mp4');
            $errors = [];
            $cmd = $ffmpeg . ' -i ' . $url . ' -map_metadata 0 -metadata:s:v rotate="-' . $rotate . '" -threads 4 '
                . '-codec copy -acodec copy ' . $tmpVideoPath . ' -hide_banner -loglevel error 2>&1';
            exec($cmd, $errors);
            //very simple check: if file created - operation successful
            if (!is_file($tmpVideoPath)) {
                throw new FfmpegException('Failed to create m3u8 file', $cmd, $errors);
            }
            try {
                // Upload file to s3
                $newPath = FileHelper::uploadFileToPath($tmpVideoPath, $relativePath, 'mp4');
                // Remove archive
                self::deleteArchive($path);
                return $newPath;
            } finally {
                @unlink($tmpVideoPath); //Remove temp files after s3 upload (or fail)
            }
        }
        //if rotate is zero - simple move file to new location
        return self::moveFromTokboxPreffix($path, $relativePath, 'mp4');
    }

    /**
     * Archive outside preffix, so need remove without it.
     */
    protected static function deleteArchive($path)
    {
        /** @var AbstractAdapter $adapter */
        $adapter = Yii::$app->fs->getAdapter();
        $prefix = Yii::$app->fs->prefix; //save
        $adapter->setPathPrefix(""); //for vonage archive - no preffix (other folder)
        try {
            FileHelper::deleteFileByPath($path);
        } finally {
            $adapter->setPathPrefix($prefix); //restore
        }
    }

    /**
     * Rename file (move to other relative path with random name)
     * note: Vonage file outside default relative path
     * @param string $path
     * @param string $relativePath
     * @param string $extention
     * @return string
     */
    protected static function moveFromTokboxPreffix(string $path, string $relativePath, string $extention): string
    {
        $newPath = FileHelper::genUniqPath($relativePath, $extention);
        /** @var AbstractAdapter $adapter */
        $adapter = Yii::$app->fs->getAdapter();
        $prefix = Yii::$app->fs->prefix; //save
        $adapter->setPathPrefix(""); //for vonage archive - no preffix (other folder)
        try {
            if (!Yii::$app->fs->rename($path, $prefix . '/' . $newPath)) {
                throw new Exception('Failed to move file from path:' . $path);
            }
        } finally {
            $adapter->setPathPrefix($prefix); //restore
        }
        return $newPath;
    }
}
