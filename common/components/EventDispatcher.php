<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
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
use common\models\Stream\StreamSessionArchive;
use common\models\Stream\StreamSessionLike;
use common\models\User;
use common\observers\Analytics\CreateStreamSessionEventObserver;
use common\observers\Analytics\CreateStreamSessionProductEventObserver;
use common\observers\Comment\CreateCommentObserver;
use common\observers\Comment\DeleteCommentObserver;
use common\observers\Comment\UpdateCommentObserver;
use common\observers\Shop\DeleteShopObserver;
use common\observers\StreamSession\CreateStreamSessionArchiveObserver;
use common\observers\StreamSession\CreateStreamSessionLikeObserver;
use common\observers\StreamSession\CreateStreamSessionObserver;
use common\observers\StreamSession\DeleteStreamSessionArchiveObserver;
use common\observers\StreamSession\EndSoonStreamSessionObserver;
use common\observers\StreamSession\UpdateStreamSessionArchiveObserver;
use common\observers\StreamSession\UpdateStreamSessionObserver;
use common\observers\StreamSessionProduct\CreateStreamSessionProductObserver;
use common\observers\StreamSessionProduct\DeleteStreamSessionProductObserver;
use common\observers\StreamSessionProduct\UpdateStreamSessionProductObserver;
use common\observers\User\PasswordChangedObserver;
use common\observers\User\PasswordRestoredObserver;
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
        Event::on(StreamSession::class, StreamSession::EVENT_AFTER_COMMIT_INSERT, [Yii::createObject(CreateStreamSessionObserver::class), 'execute']);
        Event::on(StreamSession::class, StreamSession::EVENT_AFTER_COMMIT_UPDATE, [Yii::createObject(UpdateStreamSessionObserver::class), 'execute']);
        Event::on(StreamSession::class, StreamSession::EVENT_END_SOON, [Yii::createObject(EndSoonStreamSessionObserver::class), 'execute']);

        # Stream Session Archive
        Event::on(StreamSessionArchive::class, StreamSessionArchive::EVENT_AFTER_COMMIT_INSERT, [
            Yii::createObject(CreateStreamSessionArchiveObserver::class),
            'execute'
        ]);
        Event::on(StreamSessionArchive::class, StreamSessionArchive::EVENT_AFTER_COMMIT_UPDATE, [
            Yii::createObject(UpdateStreamSessionArchiveObserver::class),
            'execute'
        ]);
        Event::on(StreamSessionArchive::class, StreamSessionArchive::EVENT_AFTER_COMMIT_DELETE, [
            Yii::createObject(DeleteStreamSessionArchiveObserver::class),
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

        # Stream Session Like events
        Event::on(StreamSessionLike::class, StreamSessionLike::EVENT_AFTER_INSERT, [
            Yii::createObject(CreateStreamSessionLikeObserver::class),
            'execute',
        ]);

        # Chat events
        Event::on(Comment::class, Comment::EVENT_AFTER_INSERT, [Yii::createObject(CreateCommentObserver::class), 'execute']);
        Event::on(Comment::class, Comment::EVENT_AFTER_UPDATE, [Yii::createObject(UpdateCommentObserver::class), 'execute']);
        Event::on(Comment::class, Comment::EVENT_AFTER_DELETE, [Yii::createObject(DeleteCommentObserver::class), 'execute']);

        # User events
        Event::on(User::class, User::EVENT_PASSWORD_RESTORED, [Yii::createObject(PasswordRestoredObserver::class), 'execute']);
        Event::on(User::class, User::EVENT_PASSWORD_CHANGED, [Yii::createObject(PasswordChangedObserver::class), 'execute']);

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
