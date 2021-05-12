<?php

use backend\models\Product\Product;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Product */

$this->title = 'Product details #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<section class="product-view">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]); ?>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'externalId',
                                'label' => Yii::t('app', 'ID of the product in the shop (external'),
                            ],
                            'title',
                            'description',
                            //todo photo and options
                            [
                                'attribute' => 'link',
                                'format' => ['url', ['target' => '_blank']]
                            ],
                        ],
                    ]); ?>
                </div>
                <div class="box-footer"></div>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
