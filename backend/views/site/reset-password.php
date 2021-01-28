<?php
use backend\models\User\User;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
/* @var $this View */
/* @var $model User */

$this->title = Yii::t('app', 'Restore password');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="login-box">
    <div class="login-logo">
        <?= Html::img(Yii::getAlias('@web') . '/images/logo.png', ['alt' => Yii::$app->name]) ?>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">

        <div class="text-center">
            <h2 class="text-center">Reset your password</h2>
            <p>Please set the new password for your account using the form below</p>
            <div class="panel-body">

                <?php $form = ActiveForm::begin(); ?>
                <div class="form-group">
                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'New Password','class' => 'form-control'])->label(false) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'confirmPassword')->passwordInput(['placeholder' => 'Confirm New Password','class' => 'form-control'])->label(false) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'resetToken')->hiddenInput()->label(false) ?>
                </div>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Set New Password'), ['class' => 'btn btn-lg btn-primary btn-block']) ?>
                </div>
                <div class="form-group">
                    <?= Html::a(Yii::t('app', 'Back to Login'), ['index'], ['class' => 'btn btn-md btn-primary btn-block bg-black']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
