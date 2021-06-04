<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use common\models\Stream\StreamSession;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;
use lavrentiev\widgets\toastr\Notification;

/* @var $this View */
/* @var $streamSession StreamSession */

$isEnabled = (int) $streamSession->getCommentsEnabled();
$this->registerJs(
    'var commentFormDisplay = function () {
        if($("input[name=commentsEnabled]").val()) {
            $("#new_comment").hide();
        } else {
            $("#new_comment").show();
        }
    };
    commentFormDisplay();
    $("#enabled_comment").on("pjax:end", function(val) {
        commentFormDisplay();
        $.pjax.reload({container:"#comment-list-pjax"});  //Reload GridView
    });'
);
?>
    <?php Pjax::begin(['id' => 'enabled_comment', 'enablePushState' => false]); ?>
    <?= Html::beginForm(['enable-comment', 'id' => $streamSession->id], 'post', ['data-pjax' => '', 'class' => 'form-inline']); ?>
    <?= Html::hiddenInput('commentsEnabled', !$isEnabled); ?>
    <?= Html::submitButton($isEnabled ? 'Disable comments' : 'Enable comments', ['class' => 'button button--ghost button--upper button--' . ($isEnabled ? 'danger' : 'success')]) ?>
    <?php
    //Execute below code only for ajax request(pjax load)
    if (Yii::$app->request->isAjax) {
        if ($streamSession->hasErrors()) {
            $errors = $streamSession->getFirstErrors();
            foreach ($errors as $error) {
                echo Notification::widget([
                    'type' => 'error',
                    'title' => 'Failed to update comments',
                    'message' => $error
                ]);
            }
        } elseif ($isEnabled) {
            echo Notification::widget([
                'type' => 'success',
                'message' => Yii::t('app', 'Comment section of the widget is enabled and buyers can comment the livestream.')
            ]);
        } else {
            echo Notification::widget([
                'type' => 'warning',
                'message' => Yii::t('app', 'Comment section of the widget was disabled. Now buyers can not comment the livestream.')
            ]);
        }
    }
    ?>
    <?= Html::endForm() ?>
    <?php Pjax::end(); ?>