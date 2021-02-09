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

<div class="login-box">
    <div class="login-logo">
        <?= Html::img(Yii::getAlias('@web') . '/images/logo.png', ['alt' => Yii::$app->name]) ?>
    </div>
    <div class="login-box-body">
        <div class="text-center">
            <h2 class="text-center">Forgot Your Password?</h2>
            <p>Please indicate the email you are registered with. We will send the password recovery link there </p>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(); ?>
                <div class="box-body table-responsive">
                    <?= $form->field($model, 'email')->textInput(['placeholder' => 'Email','class' => 'form-control'])->label(false) ?>
                </div>
                <div class="box-footer">
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Reset Password'), ['class' => 'btn btn-lg btn-primary btn-block']) ?>
                    </div>
                    <div class="box-header">
                        <?= Html::a(Yii::t('app', 'Back to Login'), ['index'], ['class' => 'btn btn-md btn-primary btn-block bg-black']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>