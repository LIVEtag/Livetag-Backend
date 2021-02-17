<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

/* @var $this yii\web\View */
/* @var $model backend\models\Comment\Comment */
/* @var $commentModel Comment */

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

?>

<?php
$this->registerJs(
    '$("document").ready(function() {
                $("#new_comment").on("pjax:end", function(val) {
                $.pjax.reload({container:"#comment-list-pjax"});  //Reload GridView
                $("#comment-message").val("");
                });
                 $("#reset-button").on("click", function(val) {
                    $.pjax.reload({container:"#comment-list-pjax"});  //Reload GridView
                });
        });'
);
?>

<section class="comment-create">
    <!--/.box-header -->
    <div class="box-body">
        <div class="comment-form">
            <div class="row">
                <div id="message-added"></div>

                <div class="col-md-4">
                    <h4>Chat</h4>
                    <?php Pjax::begin(['id' => 'new_comment']) ?>
                    <?php $form = ActiveForm::begin(['action' => '','options' => ['autocomplete' => 'off','data-pjax' => true]]); ?>
                    <!--/.box-header -->
                    <div class="box-body">
                        <?= $form->field($commentModel, 'message')->widget(CKEditor::className(), [
                            'options' => ['rows' => 6],
                            'preset' => 'basic'
                        ]) ?>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-md btn-primary btn-block bg-black']) ?>
                    </div>
                    <!--/.box-footer -->
                    <?php ActiveForm::end(); ?>
                    <?php Pjax::end(); ?>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
        </div>
    </div>
</section>