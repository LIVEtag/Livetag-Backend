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

                        <?= $this->render('archive-form', ['form' => $form, 'model' => $model]); ?>

                        <?= $form->field($model, 'file')->widget(FileInput::class, [
                            'options' => [
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'initialPreviewConfig' => [
                                    [
                                        'showRemove' => false,
                                        'showZoom' => true,
                                        'showDrag' => false,
                                    ],
                                ],
                                'initialPreviewAsData' => true,
                                'maxFileCount' => 1,
                                'showUpload' => false,
                                'showRemove' => false,
                                'browseLabel' => Yii::t('app', 'Upload'),
                                'msgPlaceholder' => Yii::t('app', 'Add image'),
                                'maxFileSize' => (Yii::$app->params['maxUploadImageSize'] / 1024), // the maximum file size for upload in KB
                            ],
                        ]); ?>


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
