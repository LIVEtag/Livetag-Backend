<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
/* @var $this yii\web\View */
/* @var $model backend\models\Shop\Shop */

$this->title = Yii::t('app', 'Update Shop: {nameAttribute}', [
    'nameAttribute' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shops'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<section class="shop-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</section>
