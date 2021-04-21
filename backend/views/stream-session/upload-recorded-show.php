<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Stream\UploadRecordedShowForm;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model UploadRecordedShowForm */
/* @var $productIds array */

$this->title = Yii::t('app', 'Upload recorded show');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Livestreams'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$js = /** @lang JavaScript */
    <<<SCRP
    $('#uploadrecordedshowform-type-link').on('click', function() {
        if ($(this).is(':checked')) {
            $('#uploadrecordedshowform-directurl').prop('disabled', false);
            $('#uploadrecordedshowform-file').fileinput('lock').fileinput('refresh');
        }
    });

    $('#uploadrecordedshowform-type-upload').on('click', function() {
        if ($(this).is(':checked')) {
            var directurlInput = $('#uploadrecordedshowform-directurl');
            directurlInput.val('');
            directurlInput.prop('disabled', true);
            $('#uploadrecordedshowform-file').fileinput('unlock').fileinput('refresh');
        }
    });
SCRP;

$this->registerJs($js);
?>

<section class="recorded-show">
    <div class="recorded-show-form">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="box-header"></div>
                    <!--/.box-header -->
                    <div class="box-body table-responsive">
                        <?= $form->field($model, 'name')->textInput([
                                'placeholder' => Yii::t('app', 'Please set the name for the livestream'),
                                'maxlength' => true,
                            ]) ?>

                        <?= $form->field($model, 'uploadType')->radio([
                            'id' => 'uploadrecordedshowform-type-link',
                            'value' => UploadRecordedShowForm::TYPE_LINK,
                            'uncheck' => null,
                            'label' => $model->getAttributeLabel('directUrl'),
                            'checked' => 'checked',
                        ]); ?>
                        
                        <?= $form->field($model, 'directUrl')
                            ->textInput(['placeholder' => Yii::t('app', 'Please put the direct URL link to the file with the video')])
                            ->label(false);
                        ?>

                        <?= $form->field($model, 'uploadType')->radio([
                            'id' => 'uploadrecordedshowform-type-upload',
                            'value' => UploadRecordedShowForm::TYPE_UPLOAD,
                            'uncheck' => null,
                            'label' => $model->getAttributeLabel('file'),
                        ]); ?>

                        <?= $form->field($model, 'file')
                            ->hint(Yii::t('app', 'max size 5GB'))
                            ->widget(FileInput::class, [
                                'options' => [
                                    'multiple' => false,
                                    'disabled' => true
                                ],
                                'pluginOptions' => [
                                    'showPreview' => false,
                                    'maxFileCount' => 1,
                                    'showUpload' => false,
                                    'showRemove' => false,
                                    'browseLabel' => Yii::t('app', 'Upload'),
                                    // the maximum file size for upload in KB
                                    'maxFileSize' => (Yii::$app->params['maxUploadVideoSize'] / 1024),
                                ],
                            ])->label(false); ?>

                        <?= $form->field($model, 'productIds')->widget(Select2::class, [
                            'data' => $productIds,
                            'options' => [
                                'placeholder' => Yii::t('app', 'Select Products'),
                                'multiple' => true,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]); ?>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="form-group">
                            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn bg-black']) ?>
                            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                    <!--/.box-footer -->
                    <?php ActiveForm::end(); ?>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
    </div>
</section>
