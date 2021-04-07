<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\Stream\SaveAnnouncementForm;
use yii\web\View;

/* @var $this View */
/* @var $model SaveAnnouncementForm */

$this->title = Yii::t('app', 'Setup'); //Create Stream Session
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stream Sessions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="stream-session-create">
<?= $this->render('_form', [
    'model' => $model,
    'productIds' => $productIds
]) ?>
</section>
