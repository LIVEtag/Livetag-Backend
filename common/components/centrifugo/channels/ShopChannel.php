<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\centrifugo\channels;

use common\components\centrifugo\ChannelInterface;
use common\models\Shop\Shop;
use yii\base\BaseObject;

/**
 * Class ShopChannel
 * @package common\components\centrifugo
 */
class ShopChannel extends BaseObject implements ChannelInterface
{
    /**
     * Channel preffix
     */
    const PREFIX = 'shop';

    /** @var Shop */
    protected $shop;

    /**
     * PostChannel constructor.
     * @param Shop $shop
     * @param $config $array
     */
    public function __construct(Shop $shop, $config = [])
    {
        $this->shop = $shop;
        parent::__construct($config);
    }

    /**
     * Get name of channel
     * @return string
     */
    public function getName(): string
    {
        return self::PREFIX . '_' . $this->shop->uri;
    }
}
