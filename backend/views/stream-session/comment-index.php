<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\Comment\Comment;
use backend\models\Comment\CommentSearch;
use backend\models\Stream\StreamSession;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $commentSearchModel CommentSearch */
/* @var $commentDataProvider ActiveDataProvider */
/* @var $streamSession StreamSession */

?>

<?= GridView::widget([
    'dataProvider' => $commentDataProvider,
    'hover' => true, //the grid table will highlight row on hover
    'persistResize' => true, //to store resized column state using local storage persistence
    'options' => ['id' => 'comment-list'],
    'pjax' => true,
    'filterModel' => $commentSearchModel,
    'columns' => [
        [
            'attribute' => 'id',
            'hAlign' => GridView::ALIGN_LEFT,
            'headerOptions' => ['width' => '60'],
        ],
        [
            'attribute' => 'username',
            'label' => 'Name',
            'value' => static function (Comment $model) {
                return $model->user->isSeller ? $model->user->email : $model->user->name;
            },
            'hAlign' => GridView::ALIGN_LEFT,
        ],
        [
            'attribute' => 'message',
            'format' => 'html',
            'label' => 'Text',
            'hAlign' => GridView::ALIGN_LEFT,
        ],
        [
            'attribute' => 'parentCommentId',
            'label' => 'ID of the replied comment',
            'format' => 'raw',
            'value' => function (Comment $model) {
                if ($model->parentCommentId) {
                    return Html::a(
                        $model->parentCommentId,
                        [
                            'view',
                            'id' => $model->streamSessionId,
                            'CommentSearch[id]' => $model->parentCommentId
                        ]
                    );
                }
                return null;
            }
        ],
        [
            'attribute' => 'createdAt',
            'format' => 'datetime',
            'label' => 'Date&Time',
            'hAlign' => GridView::ALIGN_LEFT,
        ],
        [
            'class' => ActionColumn::class,
            'vAlign' => GridView::ALIGN_TOP,
            'template' => '{reply} {delete-comment}',
            'visibleButtons' => [
                'reply' => function (Comment $model) {
                    return $model->streamSession->commentsEnabled;
                }
            ],
            'buttons' => [
                'reply' => function () {
                    return '<span class="fa fa-reply comment-reply" data-pjax=""></span>';
                },
                'delete-comment' => function ($url) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'data-pjax' => true,
                            'title' => 'Delete',
                            'data-confirm' => 'Are you sure you want to delete this item?',
                            'data-method' => 'post'
                    ]);
                }
            ],
        ],
    ],
]) ?>
