<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\Shop\Shop;
use yii\web\View;

/* @var $this View */
/* @var $model Shop */

$this->title = Yii::t('app', 'Update shop details');
$this->params['breadcrumbs'][] = ['label' => 'About', 'url' => ['my']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit shop details');
?>
<section class="shop-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>
</section>
