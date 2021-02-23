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
/* @var $model Comment */
/* @var $commentModel Comment */

$this->registerJs(
    '$("#new_comment").on("pjax:end", function(val) {
        $.pjax.reload({container:"#comment-list-pjax"});  //Reload GridView
    });'
);
?>

<?php Pjax::begin(['id' => 'new_comment']) ?>
<?php $form = ActiveForm::begin(['action' => '', 'options' => ['autocomplete' => 'off', 'data-pjax' => true]]); ?>
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
<?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-md btn-primary btn-block bg-black']) ?>
<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>

