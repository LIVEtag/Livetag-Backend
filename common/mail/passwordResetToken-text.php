<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->passwordResetToken]);
?>
Hello <?= $user->email ?>,

Follow the link below to reset your password:

<?= $resetLink ?>
