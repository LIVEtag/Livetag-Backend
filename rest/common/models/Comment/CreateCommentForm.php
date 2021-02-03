<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\Comment;

use common\components\validation\validators\PurifyFilter;
use common\models\Comment\Comment;
use common\models\Stream\StreamSession;
use common\models\User;
use yii\base\Model;

class CreateCommentForm extends Model
{
    /** @var string */
    public $message;

    /** @var User */
    private $user;

    /** @var StreamSession */
    private $streamSession;

    /**
     * @param StreamSession $streamSession
     * @param User $user
     */
    public function __construct(StreamSession $streamSession, User $user)
    {
        parent::__construct([]);
        $this->streamSession = $streamSession;
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['message', PurifyFilter::class],
            ['message', 'string', 'max' => 255],
        ];
    }

    /**
     * @return Comment|self
     */
    public function create()
    {
        /** @var Comment $comment */
        $comment = new Comment();
        $comment->userId = $this->user->getId();
        $comment->streamSessionId = $this->streamSession->getId();
        $comment->message = $this->message;

        $comment->save();
        return $comment; //return model or errors
    }
}
