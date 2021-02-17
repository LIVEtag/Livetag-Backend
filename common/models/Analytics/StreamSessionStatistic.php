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

/**
 * This is the model class for table "stream_session_statistic".
 *
 * @property integer $id
 * @property integer $streamSessionId
 * @property integer $addToCartCount
 * @property integer $viewsCount
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
     * Add to Cart Clicks
     */
    const ATTR_ADD_TO_CART_COUNT = 'addToCartCount';

    /**
     * Views Count
     */
    const ATTR_VIEWS_COUNT = 'viewsCount';

    /**
     * StreamSession relation key
     */
    const REL_STREAM_SESSION = 'streamSession';

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
            [['streamSessionId', 'addToCartCount', 'viewsCount'], 'integer', 'min' => 0],
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
            'addToCartCount' => Yii::t('app', 'Add To Cart Count'),
            'viewsCount' => Yii::t('app', 'Views Count'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getStreamSession(): ActiveQuery
    {
        return $this->hasOne(StreamSession::class, ['id' => 'streamSessionId']);
    }

    public static function getOrCreateBySessionId($sessionId)
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
        $newValue = null;
        switch ($type) {
            case self::ATTR_ADD_TO_CART_COUNT:
                $newValue = StreamSessionProductEvent::find()
                    ->byStreamSessionId($streamSessionId)
                    ->byType(StreamSessionProductEvent::TYPE_ADD_TO_CART)
                    ->count();
                break;
            case self::ATTR_VIEWS_COUNT:
                $newValue = 0; //TBU
                break;
        }

        $statistic->$type = $newValue;
        if (!$statistic->save()) {
            LogHelper::error('Failed to save Stream Session Statistic', self::LOG_CATEGORY, LogHelper::extraForModelError($statistic));
        }
    }
}
