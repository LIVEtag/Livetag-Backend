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

$this->title = Yii::t('app', 'Forgot Password?');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="forgot-template">
    <div class="auth-box">
        <div class="auth-box__body">
            <h1 class="auth-box__title">Forgot your password?</h1>
            <span class="auth-box__text-below">Please enter your account email. We will send
                recovery link on your email.</span>
            <?php $form = ActiveForm::begin(['options' => [
                'class' => 'form'
            ]]); ?>
            <?= $form->field($model, 'email')->textInput(['placeholder' => 'Email', 'class' => 'field form-control']) ?>
            <?= Html::submitButton(Yii::t('app', 'Reset Password'), ['class' => 'button button--dark button--upper auth-box__submit']) ?>
            <div class="auth-box__footer">
                <?= Html::a(Yii::t('app', 'Back to Login'), ['index'], ['class' => 'back']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
