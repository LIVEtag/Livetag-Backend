<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\test\ActiveFixture;
use common\models\Analytics\StreamSessionStatistic;

/**
 * Class StreamSessionProductFixture
 */
class StreamSessionStatisticFixture extends ActiveFixture
{
    const STATISTIC_SESSION_1 = 1;
    const STATISTIC_SESSION_2 = 2;
    const STATISTIC_SESSION_3 = 3;
    const STATISTIC_SESSION_4 = 4;

    public $modelClass = StreamSessionStatistic::class;
    public $depends = [
        StreamSessionFixture::class,
    ];

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        return [
            'viewsCount' => $this->generator->randomNumber(2),
            'addToCartCount' => $this->generator->randomNumber(2),
        ];
    }
}
