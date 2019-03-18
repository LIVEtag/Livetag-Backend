<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
$resetLink = Yii::$app->params['projectDomain'] . $user->passwordResetToken;
?>
<div class="password-reset">
    <p>Hello <?= Html::encode($user->email) ?>,</p>

    <p>Follow the link below to reset your password:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>