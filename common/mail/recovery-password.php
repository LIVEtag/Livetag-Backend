<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
$resetLink = Yii::$app->urlManagerBackend->createAbsoluteUrl(['site/reset-password','token' => $user->passwordResetToken]);

?>
<div class="password-reset">
    <p>Hello <?= Html::encode($user->email) ?>,</p>
        <p>You have requested a new password. Please click <?= Html::a('link', $resetLink) ?> to reset your password.</p>
    </p>
</div>