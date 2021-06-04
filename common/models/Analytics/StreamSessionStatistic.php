<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Analytics;

use common\helpers\LogHelper;
use common\models\queries\Analytics\StreamSessionStatisticQuery;
use common\models\Stream\StreamSession;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "stream_session_statistic".
 *
 * @property integer $id
 * @property integer $streamSessionId
 * @property integer $totalAddToCartCount
 * @property integer $totalViewCount
 * @property float $totalAddToCartRate
 * @property integer $streamAddToCartCount
 * @property integer $streamViewCount
 * @property float $streamAddToCartRate
 * @property integer $archiveAddToCartCount
 * @property integer $archiveViewCount
 * @property float $archiveAddToCartRate
 *
 * @property-read StreamSession $streamSession
 */
class StreamSessionStatistic extends ActiveRecord
{
    /**
     * Category for logs
     */
    const LOG_CATEGORY = 'streamSessionStatistic';

    /**
     * StreamSession relation key
     */
    const REL_STREAM_SESSION = 'streamSession';

    /**
     * Stream Add to Cart Clicks
     */
    const ATTR_STREAM_ADD_TO_CART_COUNT = 'streamAddToCartCount';

    /**
     * Stream Views Count
     */
    const ATTR_STREAM_VIEWS_COUNT = 'streamViewCount';

    /**
     * Stream Add to Cart Rate
     */
    const ATTR_STREAM_ADD_TO_CART_RATE = 'streamAddToCartRate';

    /**
     * Archive Add to Cart Clicks
     */
    const ATTR_ARCHIVE_ADD_TO_CART_COUNT = 'archiveAddToCartCount';

    /**
     * Archive Views Count
     */
    const ATTR_ARCHIVE_VIEWS_COUNT = 'archiveViewCount';

    /**
     * Archive Add to Cart Rate
     */
    const ATTR_ARCHIVE_ADD_TO_CART_RATE = 'archiveAddToCartRate';

    /**
     * Total Add to Cart Clicks
     */
    const ATTR_TOTAL_ADD_TO_CART_COUNT = 'totalAddToCartCount';

    /**
     * Total Views Count
     */
    const ATTR_TOTAL_VIEWS_COUNT = 'totalViewCount';

    /**
     * Total Add to Cart Rate
     */
    const ATTR_TOTAL_ADD_TO_CART_RATE = 'totalAddToCartRate';


    /**
     * streamViewCount |-> streamAddToCartRate
     *                 |-> totalViewCount |-> totalAddToCartRate
     */
    const RELATED_TYPES = [
        self::ATTR_STREAM_VIEWS_COUNT => [
            self::ATTR_STREAM_ADD_TO_CART_RATE,
            self::ATTR_TOTAL_VIEWS_COUNT,
        ],
        self::ATTR_STREAM_ADD_TO_CART_COUNT => [
            self::ATTR_STREAM_ADD_TO_CART_RATE,
            self::ATTR_TOTAL_ADD_TO_CART_COUNT,
        ],
        self::ATTR_ARCHIVE_VIEWS_COUNT => [
            self::ATTR_ARCHIVE_ADD_TO_CART_RATE,
            self::ATTR_TOTAL_VIEWS_COUNT,
        ],
        self::ATTR_ARCHIVE_ADD_TO_CART_COUNT => [
            self::ATTR_ARCHIVE_ADD_TO_CART_RATE,
            self::ATTR_TOTAL_ADD_TO_CART_COUNT,
        ],
        self::ATTR_TOTAL_VIEWS_COUNT => [
            self::ATTR_TOTAL_ADD_TO_CART_RATE
        ],
        self::ATTR_TOTAL_ADD_TO_CART_COUNT => [
            self::ATTR_TOTAL_ADD_TO_CART_RATE
        ],
    ];

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%stream_session_statistic}}';
    }

    /**
     * @inheritdoc
     * @return StreamSessionStatisticQuery the active query used by this AR class.
     */
    public static function find(): StreamSessionStatisticQuery
    {
        return new StreamSessionStatisticQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['streamSessionId', 'required'],
            [
                [
                    'streamSessionId',
                    'streamAddToCartCount',
                    'streamViewCount',
                    'archiveAddToCartCount',
                    'archiveViewCount',
                    'totalAddToCartCount',
                    'totalViewCount',
                ],
                'integer',
                'min' => 0
            ],
            [
                [
                    'streamAddToCartRate',
                    'archiveAddToCartRate',
                    'totalAddToCartRate',
                ],
                'number',
                'min' => 0
            ],
            ['streamSessionId', 'exist', 'skipOnError' => true, 'targetClass' => StreamSession::class, 'targetRelation' => 'streamSession'],
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
            'totalAddToCartCount' => Yii::t('app', 'Total Add To Cart Count'),
            'totalViewCount' => Yii::t('app', 'Total View Count'),
            'totalAddToCartRate' => Yii::t('app', 'Total Add To Cart Rate'),
            'streamAddToCartCount' => Yii::t('app', '“Add to cart” clicks of the livestream'),
            'streamViewCount' => Yii::t('app', 'Number of views of the livestream'),
            'streamAddToCartRate' => Yii::t('app', '“Add to cart” rate of the livestream'),
            'archiveAddToCartCount' => Yii::t('app', '“Add to cart” clicks for the archive'),
            'archiveViewCount' => Yii::t('app', 'Number of views of the archive'),
            'archiveAddToCartRate' => Yii::t('app', '“Add to cart” rate for the archive'),
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
     * @param int $sessionId
     * @return self
     */
    public static function getOrCreateBySessionId(int $sessionId): self
    {
        $model = self::find()->byStreamSessionId($sessionId)->one();
        if (!$model) {
            $model = new self(['streamSessionId' => $sessionId]);
        }
        return $model;
    }

    /**
     * Update statistic value for user
     *
     * @param integer $streamSessionId
     * @param string $type
     * @throws InvalidConfigException
     */
    public static function recalculate(int $streamSessionId, string $type)
    {
        $statistic = self::getOrCreateBySessionId($streamSessionId);
        if (!$statistic->hasAttribute($type)) {
            LogHelper::error('Entity do not have ' . $type . ' property', self::LOG_CATEGORY);
            return; //log and silent exit
        }

        $types = self::getRelatedTypes($type);
        foreach ($types as $type) {
            $statistic->$type = $statistic->calculate($type);
        }
        if (!$statistic->save()) {
            LogHelper::error('Failed to save Stream Session Statistic', self::LOG_CATEGORY, LogHelper::extraForModelError($statistic));
        }
    }

    /**
     * Internal implementation of obtaining quantitative values of counters for a streamSession, depending on the type
     * @param string $type
     * @return int
     * @throws InvalidConfigException
     */
    protected function calculate($type)
    {
        switch ($type) {
            case self::ATTR_STREAM_VIEWS_COUNT:
                $streamSession = $this->streamSession;
                //if new - no statistic for stream no sense to calculate
                //If not active and no stopped timestamp - archive was created without stream
                if (!$streamSession || $streamSession->isNew() || (!$streamSession->isActive() && !$streamSession->getStoppedAt())) {
                    return 0;
                }
                return StreamSessionEvent::getActiveEventsQuery($streamSession)
                        ->byType(StreamSessionEvent::TYPE_VIEW)
                        ->count();
            case self::ATTR_STREAM_ADD_TO_CART_COUNT:
                $streamSession = $this->streamSession;
                //if new - no statistic for stream no sense to calculate
                //If not active and no stopped timestamp - archive was created without stream
                if (!$streamSession || $streamSession->isNew() || (!$streamSession->isActive() && !$streamSession->getStoppedAt())) {
                    return 0;
                }
                return StreamSessionProductEvent::getActiveEventsQuery($streamSession)
                        ->byType(StreamSessionProductEvent::TYPE_ADD_TO_CART)
                        ->count();
            case self::ATTR_STREAM_ADD_TO_CART_RATE:
                return $this->streamViewCount ? $this->streamAddToCartCount / $this->streamViewCount : 0;
            case self::ATTR_ARCHIVE_VIEWS_COUNT:
                $streamSession = $this->streamSession;
                //if new or active - no statistic for archive and no sense to calculate
                if (!$streamSession || $streamSession->isNew() || $streamSession->isActive()) {
                    return 0;
                }
                return StreamSessionEvent::getArchivedEventsQuery($streamSession)
                        ->byType(StreamSessionEvent::TYPE_VIEW)
                        ->count();
            case self::ATTR_ARCHIVE_ADD_TO_CART_COUNT:
                $streamSession = $this->streamSession;
                //if new or active - no statistic for archive and no sense to calculate
                if (!$streamSession || $streamSession->isNew() || $streamSession->isActive()) {
                    return 0;
                }
                return StreamSessionProductEvent::getArchivedEventsQuery($streamSession)
                        ->byType(StreamSessionProductEvent::TYPE_ADD_TO_CART)
                        ->count();
            case self::ATTR_ARCHIVE_ADD_TO_CART_RATE:
                return $this->archiveViewCount ? $this->archiveAddToCartCount / $this->archiveViewCount : 0;
            case self::ATTR_TOTAL_VIEWS_COUNT:
                return $this->streamViewCount + $this->archiveViewCount;
            case self::ATTR_TOTAL_ADD_TO_CART_COUNT:
                return $this->streamAddToCartCount + $this->archiveAddToCartCount;
            case self::ATTR_TOTAL_ADD_TO_CART_RATE:
                return $this->totalViewCount ? $this->totalAddToCartCount / $this->totalViewCount : 0;
            default:
                throw new InvalidConfigException('Incorrect type of counter');
        }
    }

    /**
     * Get array of related types
     * @param string $type
     * @return array
     */
    public static function getRelatedTypes($type): array
    {
        $types = [$type];
        $relatedTypes = ArrayHelper::getValue(self::RELATED_TYPES, $type, []);
        foreach ($relatedTypes as $relatedType) {
            $types = ArrayHelper::merge($types, self::getRelatedTypes($relatedType));
        }
        return $types;
    }
}
