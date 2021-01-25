<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components;

use common\models\Shop\Shop;
use common\models\Stream\StreamSession;
use common\observers\Shop\DeleteShopObserver;
use common\observers\StreamSession\CreateStreamSessionObserver;
use common\observers\StreamSession\EndSoonStreamSessionObserver;
use common\observers\StreamSession\UpdateStreamSessionObserver;
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
        # Shop events
        Event::on(Shop::class, Shop::EVENT_BEFORE_DELETE, [Yii::createObject(DeleteShopObserver::class), 'execute']);

        # Stream Session Events
        Event::on(StreamSession::class, StreamSession::EVENT_AFTER_INSERT, [Yii::createObject(CreateStreamSessionObserver::class), 'execute']);
        Event::on(StreamSession::class, StreamSession::EVENT_AFTER_UPDATE, [Yii::createObject(UpdateStreamSessionObserver::class), 'execute']);
        Event::on(StreamSession::class, StreamSession::EVENT_END_SOON, [Yii::createObject(EndSoonStreamSessionObserver::class), 'execute']);
    }
}
