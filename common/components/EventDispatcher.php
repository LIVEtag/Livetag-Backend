<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components;

use common\models\Chat\PostMessageAttachment;
use common\models\Shop\Shop;
use common\observers\Shop\DeleteShopObserver;
use Yii;
use yii\base\BaseObject;
use yii\base\BootstrapInterface;
use yii\base\Event;

/**
 * Class EventDispatcher
 *
 * @package common\components
 */
class EventDispatcher extends BaseObject implements BootstrapInterface
{

    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        # PostMessageAttachment events
        Event::on(PostMessageAttachment::class, Shop::EVENT_AFTER_DELETE, [
            Yii::createObject(DeleteShopObserver::class),
            'execute'
        ]);
    }
}
