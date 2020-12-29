<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Shop\Shop;

/* @var $this yii\web\View */
/* @var $model backend\models\Shop\Shop */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shop-form">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <?php $form = ActiveForm::begin(); ?>
                <div class="box-header">
                    <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn bg-black']) ?>
                </div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'website')->textInput(['maxlength' => true]) ?>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="form-group">
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
