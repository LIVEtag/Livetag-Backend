<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Stream;

use common\components\behaviors\TimestampBehavior;
use common\components\db\BaseActiveRecord;
use common\components\EventDispatcher;
use common\components\FileSystem\media\MediaInterface;
use common\components\FileSystem\media\MediaTrait;
use common\components\FileSystem\media\MediaTypeEnum;
use common\components\queue\stream\CreatePlaylistJob;
use common\helpers\FileHelper;
use common\models\queries\Stream\StreamSessionArchiveQuery;
use League\Flysystem\Adapter\AbstractAdapter;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%stream_session_archive}}".
 *
 * @property integer $id
 * @property integer $streamSessionId
 * @property string $externalId
 * @property string $path
 * @property string $playlist
 * @property string $originName
 * @property integer $size
 * @property string $type
 * @property integer $duration
 * @property integer $status
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property-read StreamSession $streamSession
 *
 * EVENTS:
 * - EVENT_AFTER_COMMIT_INSERT
 * - EVENT_AFTER_COMMIT_UPDATE
 * @see EventDispatcher
 */
class StreamSessionArchive extends BaseActiveRecord implements MediaInterface
{
    use MediaTrait;

    const ATTR_EXTERNAL_ID = 'externalId';
    const ATTR_PATH = 'path';
    const ATTR_PLAYLIST = 'playlist';
    const ATTR_STATUS = 'status';

    /** @see getStreamSession() */
    const REL_STREAM_SESSION = 'streamSession';

    /**
     * The file is uploaded and available
     */
    const STATUS_NEW = 1;

    /**
     * The file is sent to create a playlist for playing
     */
    const STATUS_QUEUE = 2;

    /**
     * The file is processing now
     */
    const STATUS_PROCESSING = 3;

    /**
     * The file could not be processed for some reason
     */
    const STATUS_FAILED = 4;

    /**
     * Playlist available
     */
    const STATUS_READY = 5;

    /**
     * Status Names
     */
    const STATUSES = [
        self::STATUS_NEW => 'New',
        self::STATUS_QUEUE => 'In the queue for processing',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_FAILED => 'Failed',
        self::STATUS_READY => 'Ready',
    ];

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%stream_session_archive}}';
    }

    /**
     * @inheritdoc
     * @return StreamSessionArchiveQuery the active query used by this AR class.
     */
    public static function find(): StreamSessionArchiveQuery
    {
        return new StreamSessionArchiveQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['streamSessionId', 'path', 'originName', 'size', 'type', 'duration'], 'required'],
            [['streamSessionId', 'status', 'duration'], 'integer'],
            ['size', 'integer', 'min' => 0],
            ['type', 'in', 'range' => self::getMediaTypes()],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            [['externalId', 'path', 'playlist', 'originName'], 'string', 'max' => 255],
            [
                'streamSessionId',
                'exist',
                'skipOnError' => true,
                'targetClass' => StreamSession::class,
                'targetRelation' => self::REL_STREAM_SESSION
            ],
            [
                'file',
                'file',
                'mimeTypes' => self::getMimeTypes(),
                'maxSize' => Yii::$app->params['maxUploadVideoSize'],
            ],
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'streamSessionId' => Yii::t('app', 'Stream Session ID'),
            'externalId' => Yii::t('app', 'External ID'),
            'path' => Yii::t('app', 'Path'),
            'playlist' => Yii::t('app', 'Playlist'),
            'originName' => Yii::t('app', 'Origin Name'),
            'size' => Yii::t('app', 'Size'),
            'type' => Yii::t('app', 'Type'),
            'duration' => Yii::t('app', 'Duration'),
            'status' => Yii::t('app', 'Status'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'playlist' => function () {
                return $this->getPlaylistUrl();
            }
        ];
    }

    /**
     * @return string|null
     */
    public function getPlaylist(): ?string
    {
        return $this->playlist;
    }

    /**
     * @return string|null
     */
    public function getPlaylistUrl(): ?string
    {
        if (!$this->isReady() || !$this->getPlaylist()) {
            return null;
        }
        /** @var AbstractAdapter $adapter */
        $adapter = Yii::$app->fs->getAdapter();
        return $adapter
                ->getClient()
                ->getObjectUrl(Yii::$app->fs->bucket, $adapter->applyPathPrefix($this->getPlaylist()));
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): ?int
    {
        return $this->status ? (int) $this->status : null;
    }

    /**
     * Get status display name
     * @return string|null
     */
    public function getStatusName(): ?string
    {
        return ArrayHelper::getValue(self::STATUSES, $this->getStatus());
    }

    /**
     * Check current archive is new
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->getStatus() === self::STATUS_NEW;
    }

    /**
     * Check current archive is send in queue for processing
     * @return bool
     */
    public function isInQueue(): bool
    {
        return $this->getStatus() === self::STATUS_QUEUE;
    }

    /**
     * Set archive status in queue for processing
     */
    public function setInQueue()
    {
        $this->setAttribute(self::ATTR_STATUS, self::STATUS_QUEUE);
    }

    /**
     * Check current archive is processing
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->getStatus() === self::STATUS_PROCESSING;
    }

    /**
     * Set archive status processing
     */
    public function setProcessing()
    {
        $this->setAttribute(self::ATTR_STATUS, self::STATUS_PROCESSING);
    }

    /**
     * Check current archive is failed
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->getStatus() === self::STATUS_FAILED;
    }

    /**
     * Set archive status failed
     */
    public function setFailed()
    {
        $this->setAttribute(self::ATTR_STATUS, self::STATUS_FAILED);
    }

    /**
     * Check current archive is ready
     * @return bool
     */
    public function isReady(): bool
    {
        return $this->getStatus() === self::STATUS_READY;
    }

    /**
     * Set archive status ready
     */
    public function setReady()
    {
        $this->setAttribute(self::ATTR_STATUS, self::STATUS_READY);
    }

    /**
     * @return ActiveQuery
     */
    public function getStreamSession(): ActiveQuery
    {
        return $this->hasOne(StreamSession::class, ['id' => 'streamSessionId']);
    }

    /**
     * @inheritDoc
     */
    public function getRelativePath(): string
    {
        return 'stream-archive/' . ($this->streamSessionId ?: '0');
    }

    /**
     * @inheritdoc
     */
    public function getDuration(): int
    {
        return (int) $this->duration;
    }

    /**
     * @return string
     */
    public function getFormattedDuration(): string
    {
        return gmdate("H:i:s", $this->getDuration());
    }

    /**
     * @inheritDoc
     */
    public static function getMediaTypes(): array
    {
        return [
            MediaTypeEnum::TYPE_VIDEO,
        ];
    }

    /**
     * Send archive to queue for processing
     * Allow only for status "new"
     * @return bool
     */
    public function sendToQueue(): bool
    {
        if (!$this->isNew()) {
            return false;
        }

        //Create job to create platlist
        $job = new CreatePlaylistJob();
        $job->id = $this->id;
        Yii::$app->queue->push($job);

        //set in queue
        $this->setInQueue();
        return $this->save(false, ['status', 'updatedAt']);
    }

    /**
     * Remove file from s3
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (!$this->deletePlaylist() || !$this->deleteFile()) {
            return false;
        }
        return parent::beforeDelete();
    }

    /**
     * All playlist files stored in separate folder
     * Used format: `{id}-playlist` format
     * For example `stream-archive/37/1-playlist/6082630a9b57c681084333.m3u8`
     * @return bool
     */
    public function deletePlaylist(): bool
    {
        if (!$this->playlist) {
            return true;
        }
        $playlistDirectory = dirname($this->playlist) . '/';
        if (!FileHelper::deleteDirByPath($playlistDirectory)) {
            $this->addError(self::getPathFieldName(), 'Failed to remove playlist files');
            return false;
        }
        return true;
    }
}
