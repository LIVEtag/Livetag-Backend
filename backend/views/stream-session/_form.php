<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\Stream\SaveAnnouncementForm;
use backend\models\Stream\StreamSession;
use backend\models\User\User;
use kartik\widgets\DateTimePicker;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model SaveAnnouncementForm */
/* @var $form ActiveForm */

/** @var User $user */
$user = Yii::$app->user->identity ?? null;
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

<div class="stream-session-form">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <?php $form = ActiveForm::begin(); ?>
                <div class="box-header"></div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'announcedAtDatetime')
                        ->hint('Singapore UTC +8')
                        ->widget(DateTimePicker::class, [
                            'disabled' => !$model->streamSession->isNew(),
                            'options' => [
                                'placeholder' => '',
                                'autocomplete' => 'off'
                            ],
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd hh:ii',
                                'startDate' => '+0d',
                                'endDate' => '+' . StreamSession::MAX_ANNOUNCED_AT_DAYS . 'd',
                            ]
                        ]); ?>

                    <?= $form->field($model, 'duration')
                        ->hint('Please set the maximum possible length for this show so that the system does not allow other streamers from my shop to accidentally interrupt your stream.')
                        ->widget(Select2::class, [
                            'disabled' => !$model->streamSession->isNew(),
                            'data' => StreamSession::DURATIONS,
                            'options' => ['placeholder' => 'Please set the maximum possible length for this show so that the system does not allow other streamers from my shop to accidentally interrupt your stream.'],
                            'pluginOptions' => ['allowClear' => false],
                        ]); ?>

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
                            'msgPlaceholder' => 'Add file',
                            'maxFileSize' => (Yii::$app->params['maxUploadCoverSize'] / 1024), // the maximum file size for upload in KB
                        ],
                    ])->label('Cover (can be image or video)'); ?>

                    <?= $form->field($model, 'productIds')->widget(Select2::class, [
                        'data' => $productIds,
                        'options' => [
                            'placeholder' => 'Select Products',
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
