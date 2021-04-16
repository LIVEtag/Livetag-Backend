<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\models\Stream;

use common\components\behaviors\TimestampBehavior;
use common\components\FileSystem\media\MediaInterface;
use common\components\FileSystem\media\MediaTrait;
use common\components\FileSystem\media\MediaTypeEnum;
use common\models\queries\Stream\StreamSessionArchiveQuery;
use League\Flysystem\Adapter\AbstractAdapter;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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
 * @property integer $status
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property-read StreamSession $streamSession
 */
class StreamSessionArchive extends ActiveRecord implements MediaInterface
{
    use MediaTrait;

    /** @see getStreamSession() */
    const REL_STREAM_SESSION = 'streamSession';

    /**
     * The file is uploaded and available
     */
    const STATUS_NEW = 1;

    /**
     * The file is sent to create a playlist for playing
     */
    const STATUS_PROCESSING = 2;

    /**
     * The file could not be processed for some reason
     */
    const STATUS_FAILED = 3;

    /**
     * Playlist available
     */
    const STATUS_READY = 4;

    /**
     * Status Names
     */
    const STATUSES = [
        self::STATUS_NEW => 'New',
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
            [['streamSessionId', 'path', 'originName', 'size', 'type'], 'required'],
            [['streamSessionId', 'status'], 'integer'],
            ['size',  'integer', 'min' => 0],
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
     * Check current archive is ready
     * @return bool
     */
    public function isReady(): bool
    {
        return $this->getStatus() === self::STATUS_READY;
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): ?int
    {
        return $this->status ? (int)$this->status : null;
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
        return 'stream-archive';
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
     * Remove file from s3
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (!$this->deleteFile()) {
            return false;
        }
        return parent::beforeDelete();
    }
}
