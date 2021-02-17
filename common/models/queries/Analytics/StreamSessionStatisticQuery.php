<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\queries\Analytics;

use common\models\Analytics\StreamSessionStatistic;
use common\components\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Analytics\StreamSessionStatistic]].
 *
 * @see StreamSessionStatistic
 */
class StreamSessionStatisticQuery extends ActiveQuery
{

    /**
     * @param int $id
     * @return $this
     */
    public function byStreamSessionId(int $id): self
    {
        return $this->andWhere([$this->getFieldName('streamSessionId') => $id]);
    }
}
