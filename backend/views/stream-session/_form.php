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
                            'initialPreview' => $coverFile ? [$coverFile->getUrl()] : [],
                            'initialPreviewConfig' => [
                                [
                                    'caption' => $coverFile ? $coverFile->getOriginName() : null,
                                    'size' => $coverFile ? $coverFile->getSize() : null,
                                    'showRemove' => false,
                                    'showZoom' => true,
                                    'showDrag' => false,
                                ],
                            ],
                            'initialPreviewAsData' => true,
                            'maxFileCount' => 1,
                            'showUpload' => false,
                            'showRemove' => false,
                            'msgPlaceholder' => 'Add image',
                            'maxFileSize' => (Yii::$app->params['maxUploadImageSize'] / 1024), // the maximum file size for upload in KB
                        ],
                    ])->label('Photo (cover image)'); ?>

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
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="form-group">
                        <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer ?: ['index'], ['class' => 'btn bg-black']) ?>
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