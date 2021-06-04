<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\forms\User\LoginForm */

$this->title = 'Sign In';

$fieldOptions1 = [
    'options' => ['class' => 'field-group field-group--vertical']
];

$fieldOptions2 = [
    'options' => ['class' => 'field-group field-group--vertical']
];
?>

<div class="auth-box">
    <div class="auth-box__body">
        <span class="auth-box__text-above">Ready to stream?</span>
        <h1 class="auth-box__title"><?= $this->title ?></h1>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false, 'options' => [
            'class' => 'form'
        ]]); ?>

        <?= $form
            ->field($model, 'email', $fieldOptions1)
            ->textInput(['class' => 'field form-control', 'placeholder' => $model->getAttributeLabel('email')]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->passwordInput(['class' => 'field form-control', 'placeholder' => $model->getAttributeLabel('password')]) ?>

        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "
                <div class='checkbox-box'>
                    {beginLabel}
                    <div class='checkbox'>
                        {input}
                        <div class='checkbox__custom'></div>
                    </div>
                    <span class='checkbox__label'>{labelTitle}</span>
                    {endLabel}{error}{hint}
                </div>"]) ?>

        <?= Html::submitButton('Sign in', ['class' => 'button button--dark button--upper auth-box__submit', 'name' => 'login-button']) ?>
        <div class="auth-box__footer">
            <?= Html::a('Forgot Password?', 'forgot-password', ['class' => 'forgot-password']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="auto-box__footer">
        <div class="app-links">
            <a class="app-links__link" href="https://apps.apple.com/app/livetag-liveshopping/id1557456258">
                <img class="app-links__image" src="<?= Yii::getAlias('@web') . '/images/appstore.svg' ?>" alt="appstore">
            </a>
            <a class="app-links__link" href="https://play.google.com/store/apps/details?id=com.livetag.sky">
                <img class="app-links__image" src="<?= Yii::getAlias('@web') . '/images/google-play.svg' ?>" alt="google play">
            </a>
        </div>
    </div>
</div>
