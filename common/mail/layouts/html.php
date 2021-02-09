<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
$logoUrl = Yii::$app->urlManagerBackend->createAbsoluteUrl('images/logo.png');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div style="padding:30px 0;background:#F2F2F2;">
            <table style="width:100%;max-width:500px;background:white;margin:0 auto;font-family: sans-serif;border:0;border-spacing: 0;">
                <tr>
                    <td style="padding:10px 15px; background:#F2F2F2;">
                        <?= Html::img($logoUrl, ['height' => '75', 'style' => ['display' => 'table', 'margin' => '0 auto']]) ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding:25px 15px; background:white;color: #404040;font-size: 15px;line-height: 20px;">
                        <?= $content ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
