<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components;

use common\models\Analytics\StreamSessionEvent;
use common\models\Analytics\StreamSessionProductEvent;
use common\models\Comment\Comment;
use common\models\Product\StreamSessionProduct;
use common\models\Shop\Shop;
use common\models\Stream\StreamSession;
use common\models\User;
use common\observers\Analytics\CreateStreamSessionEventObserver;
use common\observers\Analytics\CreateStreamSessionProductEventObserver;
use common\observers\Comment\CreateCommentObserver;
use common\observers\Comment\DeleteCommentObserver;
use common\observers\Comment\UpdateCommentObserver;
use common\observers\Shop\DeleteShopObserver;
use common\observers\StreamSession\CreateStreamSessionObserver;
use common\observers\StreamSession\EndSoonStreamSessionObserver;
use common\observers\StreamSession\SubscriberTokenCreatedObserver;
use common\observers\StreamSession\UpdateStreamSessionObserver;
use common\observers\StreamSessionProduct\CreateStreamSessionProductObserver;
use common\observers\StreamSessionProduct\DeleteStreamSessionProductObserver;
use common\observers\StreamSessionProduct\UpdateStreamSessionProductObserver;
use common\observers\User\PasswordRestoreObserver;
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
        Event::on(StreamSession::class, StreamSession::EVENT_SUBSCRIBER_TOKEN_CREATED, [
            Yii::createObject(SubscriberTokenCreatedObserver::class),
            'execute'
        ]);

        # Stream Session Product events
        Event::on(StreamSessionProduct::class, StreamSessionProduct::EVENT_AFTER_INSERT, [
            Yii::createObject(CreateStreamSessionProductObserver::class),
            'execute'
        ]);
        Event::on(StreamSessionProduct::class, StreamSessionProduct::EVENT_AFTER_UPDATE, [
            Yii::createObject(UpdateStreamSessionProductObserver::class),
            'execute'
        ]);
        Event::on(StreamSessionProduct::class, StreamSessionProduct::EVENT_AFTER_DELETE, [
            Yii::createObject(DeleteStreamSessionProductObserver::class),
            'execute'
        ]);

        # Chat events
        Event::on(Comment::class, Comment::EVENT_AFTER_INSERT, [Yii::createObject(CreateCommentObserver::class), 'execute']);
        Event::on(Comment::class, Comment::EVENT_AFTER_UPDATE, [Yii::createObject(UpdateCommentObserver::class), 'execute']);
        Event::on(Comment::class, Comment::EVENT_AFTER_DELETE, [Yii::createObject(DeleteCommentObserver::class), 'execute']);

        # User events
        Event::on(User::class, User::EVENT_PASSWORD_RESTORED, [Yii::createObject(PasswordRestoreObserver::class), 'execute']);

        # Analytics
        Event::on(StreamSessionEvent::class, StreamSessionEvent::EVENT_AFTER_INSERT, [
            Yii::createObject(CreateStreamSessionEventObserver::class),
            'execute'
        ]);

        Event::on(StreamSessionProductEvent::class, StreamSessionProductEvent::EVENT_AFTER_INSERT, [
            Yii::createObject(CreateStreamSessionProductEventObserver::class),
            'execute'
        ]);
    }
}
