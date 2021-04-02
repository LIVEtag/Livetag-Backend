<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Shop;

use common\components\behaviors\TimestampBehavior;
use common\components\EventDispatcher;
use common\components\FileSystem\FileResourceInterface;
use common\components\FileSystem\FileResourceTrait;
use common\models\Product\Product;
use common\models\queries\Shop\ShopQuery;
use common\models\Stream\StreamSession;
use common\models\User;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\UploadedFile;

/**
 * This is the model class for table "shop".
 *
 * @property integer $id
 * @property string $name
 * @property string $uri
 * @property string $website
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property-read Product[] $products
 * @property-read StreamSession[] $streamSessions
 * @property-read User[] $users
 *
 * EVENTS:
 * - EVENT_BEFORE_DELETE
 * @see EventDispatcher
 */
class Shop extends ActiveRecord implements FileResourceInterface
{
    use FileResourceTrait;

    /** @see getProducts() */
    const REL_PRODUCT = 'products';

    /** @see getStreamSessions() */
    const REL_STREAM_SESSION = 'streamSessions';

    /** @see getUsers() */
    const REL_USER = 'users';

    /**
     * Scenario for seller shop update
     */
    const SCENARIO_SELLER = 'seller';

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%shop}}';
    }

    /**
     * @inheritdoc
     * @return ShopQuery the active query used by this AR class.
     */
    public static function find(): ShopQuery
    {
        return new ShopQuery(get_called_class());
    }

     /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function transactions(): array
    {
        $transactions = [];
        foreach ($this->scenarios() as $scenario => $fields) {
            $transactions[$scenario] = self::OP_DELETE;
        }
        return $transactions;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ArrayHelper::getValue(parent::scenarios(), self::SCENARIO_DEFAULT),
            self::SCENARIO_SELLER => ['file'], // Seller can change only logo for now
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'uri',
                'ensureUnique' => true,
                'immutable' => true
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'website'], 'required'],
            [['name', 'uri'], 'string', 'max' => 50],
            [
                'uri',
                'filter',
                'filter' => function ($value) {
                    return Inflector::slug($value, '-', true);
                },
            ],
            ['uri', 'unique'],
            ['website', 'string', 'max' => 255],
            ['website', 'url', 'defaultScheme' => 'https'],

            [
                'file',
                'file',
                'skipOnEmpty' => true,
                'mimeTypes' => [
                    'image/svg',
                    'image/svg+xml',
                ],
                'maxSize' => Yii::$app->params['maxUploadLogoSize'],
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
            'name' => Yii::t('app', 'Name'),
            'uri' => Yii::t('app', 'Livestream URI'),
            'website' => Yii::t('app', 'Website'),
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
            'uri',
            'name',
            'website',
            'logo' => function () {
                return $this->getUrl();
            }
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(Product::class, ['shopId' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'userId'])->viaTable('user_shop', ['shopId' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStreamSessions(): ActiveQuery
    {
        return $this->hasMany(StreamSession::class, ['shopId' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function getId(): ?int
    {
        return $this->id ? (int) $this->id : null;
    }

    /**
     * Remove logo from s3
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if ($this->logo && !$this->deleteFile()) {
            return false;
        }
        return parent::beforeDelete();
    }

    /**
     * Store logo on s3 if new file uploaded
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        if ($this->file) {
            return $this->saveFile();
        }
        return true;
    }

    /**
     * Remove previous logo if new set
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (isset($changedAttributes['logo'])) {
            self::deleteFileByPath($changedAttributes['logo']);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Get name of field, that used for storing file (path)
     * @return string
     */
    public static function getPathFieldName(): string
    {
        return 'logo';
    }

    /**
     * Get relative path for file store
     * @return string
     */
    public function getRelativePath(): string
    {
        return 'logo';
    }
}
