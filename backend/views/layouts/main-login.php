<?php

use backend\assets\AppAsset;
use dmstr\web\AdminLteAsset;
use dmstr\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $content string */

AppAsset::register($this);
AdminLteAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&family=Montserrat:wght@700&family=Source+Sans+Pro:wght@400;700&display=swap" rel="stylesheet">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?= $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/ico', 'href' => Url::to(['/images/favicon.ico'])]); ?>
    <?php $this->head() ?>
</head>
<body class="login-template">
<img class="login-template__logo" src="<?= Yii::getAlias('@web') . '/images/logo.svg'; ?>" alt="Livetag logo">
<?php $this->beginBody() ?>
    <?= Alert::widget() ?>
    <?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
