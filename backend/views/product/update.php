<?php

use backend\models\Product\ProductForm;
use yii\web\View;

/* @var $this View */
/* @var $model ProductForm */

$this->title = Yii::t('app', 'Edit product'); //Update Stream Session: {nameAttribute}
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->product->id, 'url' => ['view', 'id' => $model->product->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<section class="product-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>
</section>


