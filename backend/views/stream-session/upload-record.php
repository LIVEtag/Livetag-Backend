<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\Stream\UploadRecordedShowForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model UploadRecordedShowForm */
/* @var $productIds array */

$this->title = Yii::t('app', 'Upload record');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Livestreams'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->streamSession->id, 'url' => ['view', 'id' => $model->streamSession->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<section class="recorded-show">
    <div class="recorded-show-form">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="box-header">
                        <h4 class="box-title">When you upload a new livestream video, the existing livestream video (if any) will be automatically deleted.</h4>
                    </div>
                    <!--/.box-header -->
                    <div class="box-body table-responsive">
                        <?= $this->render('archive-form', ['form' => $form, 'model' => $model]); ?>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="form-group">
                            <?= Html::a(Yii::t('app', 'Cancel'), ['view', 'id' => $model->streamSession->id], ['class' => 'btn bg-black']) ?>
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
