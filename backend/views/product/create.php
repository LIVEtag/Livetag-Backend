<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Product\ProductForm;
use yii\web\View;

/* @var $this View */
/* @var $model ProductForm */

$this->title = Yii::t('app', 'Add product');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="product-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</section>