<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\Comment\Comment;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this View */
/* @var $streamSessionId integer */
/* @var $commentModel Comment */

$this->registerJs(
    '$("#new_comment").on("pjax:end", function(val) {
            parentCommentId = $(\'#commentform-parentcommentid\').val();
            // If no validation errors
            if (!parentCommentId) {
                $.pjax.reload({container:"#comment-list-pjax"});  //Reload GridView
                $(\'.parent-comment-reply\').hide();
                $(\'.parent-comment-reply .parent-comment\').empty();
                $(\'#commentform-parentcommentid\').val(\'\');
            }
    });'
);
?>

<?php Pjax::begin(['id' => 'new_comment', 'enablePushState' => false]) ?>
<?php $form = ActiveForm::begin(['action' => ['create-comment', 'id' => $streamSessionId], 'options' => ['autocomplete' => 'off', 'data-pjax' => true]]); ?>
<?= $form->field($commentModel, 'streamSessionId')->hiddenInput()->label(false) ?>
<?= $form->field($commentModel, 'parentCommentId')->hiddenInput()->label(false) ?>
<?= $form->field($commentModel, 'message')->widget(CKEditor::class, [
    'options' => ['rows' => 4],
    'preset' => 'custom',
    'clientOptions' => [
        'toolbarGroups' => [
            ['name' => 'undo'],
            ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
            ['name' => 'links'],
        ],
        'removeButtons' => 'Anchor,Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
    ]
])->label('Comment'); ?>
<?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'button button--dark button--upper comment-send']) ?>
<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>

