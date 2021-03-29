<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\Stream\SaveAnnouncementForm;
use yii\web\View;

/* @var $this View */
/* @var $model SaveAnnouncementForm */

$this->title = Yii::t('app', 'Update'); //Update Stream Session: {nameAttribute}
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stream Sessions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->streamSession->id, 'url' => ['view', 'id' => $model->streamSession->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<section class="stream-session-update">
    <?= $this->render('_form', [
        'model' => $model,
        'productIds' => $productIds
    ]); ?>
</section>
