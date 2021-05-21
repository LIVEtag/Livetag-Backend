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
use common\helpers\FileHelper;
use common\models\Analytics\StreamSessionEvent;
use common\models\Analytics\StreamSessionStatistic;
use common\models\Comment\Comment;
use common\models\Product\Product;
use common\models\queries\Analytics\StreamSessionEventQuery;
use common\models\queries\Analytics\StreamSessionStatisticQuery;
use common\models\queries\Comment\CommentQuery;
use common\models\queries\Shop\ShopQuery;
use common\models\queries\Stream\StreamSessionLikeQuery;
use common\models\Stream\StreamSession;
use common\models\Stream\StreamSessionLike;
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
 * @property string $logo
 * @property string $productIcon
 * @property string $iconsTheme
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

    const ICONS_THEME_WHITE = 'white';
    const ICONS_THEME_LIGHT_GRAY = 'light-gray';
    const ICONS_THEME_GRAY = 'gray';
    const ICONS_THEME_DARK_GRAY = 'dark-gray';

    const ICONS_THEMES = [
        self::ICONS_THEME_WHITE => 'White',
        self::ICONS_THEME_LIGHT_GRAY => 'Light Gray',
        self::ICONS_THEME_GRAY => 'Gray',
        self::ICONS_THEME_DARK_GRAY => 'Dark Gray',
    ];

    const PRODUCT_ICON_MAKEUP = 'makeup';
    const PRODUCT_ICON_CLOTHES = 'clothes';
    const PRODUCT_ICON_BAGS = 'bags';
    const PRODUCT_ICON_SHOES = 'shoes';
    const PRODUCT_ICON_CUTLERY = 'cutlery';
    const PRODUCT_ICON_FOOD = 'food';
    const PRODUCT_ICON_COMPUTERS = 'computers';
    const PRODUCT_ICON_DEVICES = 'devices';
    const PRODUCT_ICON_FURNITURE = 'furniture';
    const PRODUCT_ICON_DECOR = 'decor';
    const PRODUCT_ICON_LIGHTING = 'lighting';
    const PRODUCT_ICON_SHOPPING = 'shopping';

    const PRODUCT_ICONS = [
        self::PRODUCT_ICON_MAKEUP => 'Makeup',
        self::PRODUCT_ICON_CLOTHES => 'Clothes',
        self::PRODUCT_ICON_BAGS => 'Bags',
        self::PRODUCT_ICON_SHOES => 'Shoes',
        self::PRODUCT_ICON_CUTLERY => 'Cutlery',
        self::PRODUCT_ICON_FOOD => 'Food',
        self::PRODUCT_ICON_COMPUTERS => 'Computers',
        self::PRODUCT_ICON_DEVICES => 'Devices',
        self::PRODUCT_ICON_FURNITURE => 'Furniture',
        self::PRODUCT_ICON_DECOR => 'Decor',
        self::PRODUCT_ICON_LIGHTING => 'Lighting',
        self::PRODUCT_ICON_SHOPPING => 'Shopping',
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
            // Seller can change only logo, iconsTheme and productIcon for now
            self::SCENARIO_SELLER => ['file', 'iconsTheme', 'productIcon'],
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            'slug' => [
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
            ['iconsTheme', 'default', 'value' => self::ICONS_THEME_WHITE],
            ['productIcon', 'default', 'value' => self::PRODUCT_ICON_SHOPPING],
            ['iconsTheme', 'in', 'range' => array_keys(self::ICONS_THEMES)],
            [['productIcon'], 'in', 'range' => array_keys(self::PRODUCT_ICONS)],
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
            'logo' => Yii::t('app', 'Logo'),
            'productIcon' => Yii::t('app', 'Product icon'),
            'iconsTheme' => Yii::t('app', 'Icons color theme'),
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
            },
            'iconsTheme',
            'productIcon',
        ];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getIconsThemeName(): ?string
    {
        return ArrayHelper::getValue(self::ICONS_THEMES, $this->iconsTheme);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getProductIconName(): ?string
    {
        return ArrayHelper::getValue(self::PRODUCT_ICONS, $this->productIcon);
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
     * @return StreamSessionLikeQuery
     */
    public function getLikes(): StreamSessionLikeQuery
    {
        return $this->hasMany(StreamSessionLike::class, ['streamSessionId' => 'id'])->via(self::REL_STREAM_SESSION);
    }

    /**
     * @return CommentQuery
     */
    public function getComments(): CommentQuery
    {
        return $this->hasMany(Comment::class, ['streamSessionId' => 'id'])->via(self::REL_STREAM_SESSION);
    }

    /**
     * @return StreamSessionStatisticQuery
     */
    public function getStreamSessionStatistic(): StreamSessionStatisticQuery
    {
        return $this->hasOne(StreamSessionStatistic::class, ['streamSessionId' => 'id'])->via(self::REL_STREAM_SESSION);
    }

    /**
     * @return StreamSessionEventQuery
     */
    public function getStreamSessionEvents(): StreamSessionEventQuery
    {
        return $this->hasMany(StreamSessionEvent::class, ['streamSessionId' => 'id'])->via(self::REL_STREAM_SESSION);
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
            FileHelper::deleteFileByPath($changedAttributes['logo']);
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
