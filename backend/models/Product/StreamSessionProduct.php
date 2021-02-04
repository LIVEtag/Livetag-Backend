<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Product;

use backend\models\Stream\StreamSession;
use common\models\Product\StreamSessionProduct as BaseModel;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Represents the backend version of `common\models\Product\StreamSessionProduct`.
 */
class StreamSessionProduct extends BaseModel
{

    /**
     * @return ActiveQuery
     */
    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'productId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStreamSession(): ActiveQuery
    {
        return $this->hasOne(StreamSession::class, ['id' => 'streamSessionId']);
    }

    /**
     * @return string|null
     */
    public function getStatusName(): ?string
    {
        return ArrayHelper::getValue(self::STATUSES, $this->status);
    }
}
