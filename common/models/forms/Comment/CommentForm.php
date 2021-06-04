<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\models\forms\Comment;

use common\models\Comment\Comment;
use common\models\User;
use yii\base\Model;

/**
 * Comment form
 */
class CommentForm extends Model
{
    /**
     * @var string
     */
    public $message;

    /**
     * @var integer
     */
    public $userId;

    /**
     * @var string
     */
    public $streamSessionId;

    /**
     * @var integer
     */
    public $parentCommentId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message', 'streamSessionId', 'userId'], 'required'],
            [['userId', 'streamSessionId', 'parentCommentId'], 'integer'],
            [['message'], 'string', 'max' => 255],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['userId' => 'id']],
            [['parentCommentId'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::class, 'targetAttribute' => ['parentCommentId' => 'id']],
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $comment = new Comment();
        $comment->userId = $this->userId;
        $comment->streamSessionId = $this->streamSessionId;
        if ($this->parentCommentId) {
            $comment->parentCommentId = (int)$this->parentCommentId;
        }
        $comment->message = $this->message;
        if (!$comment->save()) {
            $this->addErrors($comment->getErrors());
            return false;
        }
        return true;
    }
}
