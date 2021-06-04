<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Stream\StreamSession;
use backend\models\Stream\UploadRecordedShowForm;
use common\components\FileSystem\media\MediaInterface;
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

$coverFile = $model->streamSession->streamSessionCover ?? null;
$initialPreviewConfigItem = [
    'showRemove' => false,
    'showZoom' => true,
    'showDrag' => false,
];

$initialPreview = [];
$initialPreviewAsData = true;
if ($coverFile) {
    $initialPreviewConfigItem['type'] = $coverFile->type;
    $initialPreviewConfigItem['caption'] = $coverFile->originName;
    $initialPreviewConfigItem['size'] = $coverFile->size;
    $initialPreview = [$coverFile->getUrl()];
    if ($coverFile->isVideo()) {
        $initialPreviewAsData = false;
        $initialPreview = '<div style="font-size: 110px;"><i class="fa fa-file-video-o"></i></div>';
    }
}
?>

<section class="recorded-show">
    <div class="recorded-show-form">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-body table-responsive">
                        <?= $form->field($model, 'name')->textInput([
                                'placeholder' => Yii::t('app', 'Please set the name for the livestream'),
                                'maxlength' => true,
                                'class' => 'field form-control'
                            ]) ?>

                        <?= $this->render('archive-form', ['form' => $form, 'model' => $model]); ?>

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

                        <?= $form->field($model, 'internalCart')
                            ->hint(Yii::t('app', 'Please set the behavior of the system when the buyer clicks on a product in the product list (Shop) or on the product card in the video (presented now).'))
                            ->widget(Select2::class, [
                                'data' => StreamSession::INTERNAL_CART_OPTIONS,
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Select Product details view'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => false,
                                ],
                            ]); ?>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="form-group">
                            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'button button--dark button--ghost button--upper']) ?>
                            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'button button--dark button--upper']) ?>
                        </div>
                    </div>
                    <!--/.box-footer -->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-header"></div>
                    <!--/.box-header -->
                    <div class="box-body table-responsive">
                        <?= $form->field($model, 'file')->widget(FileInput::class, [
                            'options' => [
                                'multiple' => false,
                            ],
                            'pluginOptions' => [
                                'initialPreview' => $initialPreview,
                                'initialPreviewConfig' => [$initialPreviewConfigItem],
                                'initialPreviewAsData' => $initialPreviewAsData,
                                'maxFileCount' => 1,
                                'showUpload' => false,
                                'showRemove' => false,
                                'browseClass' => 'button button--dark',
                                'browseIcon' => '',
                                'browseLabel' => Yii::t('app', 'Browse'),
                                'msgPlaceholder' => Yii::t('app', 'Add image'),
                                'maxFileSize' => (Yii::$app->params['maxUploadCoverSize'] / 1024), // the maximum file size for upload in KB
                            ],
                        ]); ?>
                    </div>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</section>
