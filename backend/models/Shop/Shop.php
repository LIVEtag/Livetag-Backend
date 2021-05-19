<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Shop;

use common\models\Shop\Shop as BaseModel;
use yii\helpers\ArrayHelper;

/**
 * Represents the backend version of `common\models\Shop\Shop`.
 */
class Shop extends BaseModel
{

    /**
     * @return string
     */
    public function getStatusName(): ?string
    {
        return ArrayHelper::getValue(self::STATUSES, $this->status);
    }

    /**
     * Get all entities as indexed array
     * @return array [id=>key] array of entities
     */
    public static function getIndexedArray(): array
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }

    /**
     * Get all entities as indexed array
     * @return array [id=>key] array of entities
     */
    public static function getIndexedUriArray(): array
    {
        return self::find()->select(['name', 'uri'])->indexBy('uri')->column();
    }

    /**
     * Get shop analytics for display
     * @return array
     */
    public function getAnalytics(): array
    {
        $likesCount = $this->getLikes()->count();
        $commentsCount = $this->getComments()->count();

        $statisticQuery = $this->getStreamSessionStatistic();
        $totalViewCount = $statisticQuery->sum('totalViewCount') ?: 0;
        $totalAddToCartCount = $statisticQuery->sum('totalAddToCartCount') ?: 0;
        $totalAddToCartRate = $totalViewCount ? round($totalAddToCartCount / $totalViewCount, 2) : 0;

        $uniqueViews = $this->getStreamSessionEvents()->select(['userId'])->distinct()->count();

        return [
            'likesCount' => $likesCount,
            'commentsCount' => $commentsCount,
            'totalViewCount' => $totalViewCount,
            'totalAddToCartCount' => $totalAddToCartCount,
            'totalAddToCartRate' => $totalAddToCartRate,
            'uniqueViews' => $uniqueViews,
        ];
    }
}
