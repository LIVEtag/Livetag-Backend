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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message', 'streamSessionId', 'userId'], 'required'],
            [['userId', 'streamSessionId'], 'integer'],
            [['message'], 'string', 'max' => 255],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['userId' => 'id']],
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
        $comment->message = $this->message;
        if (!$comment->save()) {
            $this->addErrors($comment->getErrors());
            return false;
        }
        return true;
    }
}
