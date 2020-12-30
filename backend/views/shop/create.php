<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

/* @var $this yii\web\View */
/* @var $model backend\models\Shop\Shop */

$this->title = Yii::t('app', 'Create Shop');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shops'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="shop-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</section>
