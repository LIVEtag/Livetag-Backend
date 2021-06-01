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
    <div class="auth-box">
        <div class="auth-box__body">
            <h1 class="auth-box__title">Reset your password</h1>
            <span class="auth-box__text-below">Please set the new password for your account using the form below</span>
                <?php $form = ActiveForm::begin(); ?>
                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'New Password', 'class' => 'field form-control'])->label(false) ?>
                    <?= $form->field($model, 'confirmPassword')->passwordInput(['placeholder' => 'Confirm New Password', 'class' => 'field form-control'])->label(false) ?>
                    <?= $form->field($model, 'resetToken')->hiddenInput()->label(false) ?>
                    <?= Html::submitButton(Yii::t('app', 'Set New Password'), ['class' => 'button button--dark button--upper auth-box__submit']) ?>
                    <?= Html::a(Yii::t('app', 'Back to Login'), ['index'], ['class' => 'button button--dark button--ghost button--upper auth-box__submit']) ?>
                <?php ActiveForm::end(); ?>
            </div>
    </div>
</div>
