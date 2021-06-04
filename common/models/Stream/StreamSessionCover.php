<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Stream;

use common\components\behaviors\TimestampBehavior;
use common\components\FileSystem\media\MediaInterface;
use common\components\FileSystem\media\MediaTrait;
use common\components\FileSystem\media\MediaTypeEnum;
use common\models\queries\Stream\StreamSessionCoverQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "stream_session_cover".
 *
 * @property integer $id
 * @property integer $streamSessionId
 * @property string $path
 * @property string $originName
 * @property string $size
 * @property string $type
 * @property integer $createdAt
 *
 * @property-read StreamSession $streamSession
 */
class StreamSessionCover extends ActiveRecord implements MediaInterface
{
    use MediaTrait;
    /** @see getStreamSession() */
    const REL_STREAM_SESSION = 'streamSession';

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%stream_session_cover}}';
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     * @return StreamSessionCoverQuery the active query used by this AR class.
     */
    public static function find(): StreamSessionCoverQuery
    {
        return new StreamSessionCoverQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['streamSessionId', 'type', 'path'], 'required'],
            ['streamSessionId', 'integer'],
            ['size',  'integer', 'min' => 0],
            ['type', 'in', 'range' => self::getMediaTypes()],
            [['path', 'originName'], 'string', 'max' => 255],
            ['streamSessionId', 'exist', 'skipOnError' => true, 'targetClass' => StreamSession::class, 'targetRelation' => self::REL_STREAM_SESSION],
            [
                'file',
                'file',
                'mimeTypes' => self::getMimeTypes(),
                'maxSize' => Yii::$app->params['maxUploadCoverSize'],
            ],
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
            'path' => Yii::t('app', 'Path'),
            'originName' => Yii::t('app', 'Origin Name'),
            'size' => Yii::t('app', 'Size'),
            'type' => Yii::t('app', 'Type'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'url',
            'type',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getStreamSession(): ActiveQuery
    {
        return $this->hasOne(StreamSession::class, ['id' => 'streamSessionId']);
    }

    /**
     * Get relative path for file store
     * @return string
     */
    public function getRelativePath(): string
    {
        return 'stream-cover';
    }

    /**
     * @inheritdoc
     * @return array
     */
    public static function getMediaTypes(): array
    {
        return [
            MediaTypeEnum::TYPE_IMAGE,
            MediaTypeEnum::TYPE_VIDEO,
        ];
    }

    /**
     * Remove file from s3
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if ($this->path && !$this->deleteFile()) {
            return false;
        }
        return parent::beforeDelete();
    }
}
