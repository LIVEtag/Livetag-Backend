<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

/* @var $this yii\web\View */
/* @var $model backend\models\StreamSession\StreamSession */

$this->title = Yii::t('app', 'Set'); //Create Stream Session
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stream Sessions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="stream-session-create">
    <?= $this->render('_form', [
        'model' => $model,
        'productIds' => $productIds
    ]) ?>
</section>
