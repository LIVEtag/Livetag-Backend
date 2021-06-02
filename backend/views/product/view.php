<?php

use backend\models\Product\Product;
use common\components\FileSystem\format\FormatEnum;
use dosamigos\gallery\Gallery;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Product */

$this->title = 'Product details ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<section class="product-view">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <div class="buttons-group">
                        <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'button button--dark button--upper button--lg']) ?>
                        <?= Html::a(Yii::t('app', 'Delete product'), ['delete', 'id' => $model->id], [
                            'class' => 'button button--danger button--ghost button--upper button--lg',
                            'data' => [
                                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]); ?>
                    </div>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'externalId',
                                'label' => Yii::t('app', 'ID of the product in the shop (external)'),
                            ],
                            'title',
                            'description',
                            [
                                'label' => 'SKU, Price and options',
                                'attribute' => 'options',
                                'format' => 'raw',
                                'mergeHeader' => true,
                                'value' => function (Product $model) {
                                    return $model->getProductOptionsInHTML();
                                },
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'html',
                                'value' => function (Product $model) {
                                    return Html::tag("span", $model->getStatusName(), ['class' => 'status-label status-label--' . $model->getStatusClass()]);
                                },
                            ],
                            [
                                'label' => 'Photo',
                                'format' => 'raw',
                                'value' => function (Product $model) {
                                    $items = [];
                                    foreach ($model->productMedias as $media) {
                                        $items[] = [
                                            'url' => $media->getFormattedUrlByName(FormatEnum::LARGE),
                                            'src' => $media->getFormattedUrlByName(FormatEnum::SMALL),
                                            'options' => [
                                                'title' => $media->originName,
                                            ]
                                        ];
                                    }
                                    if (empty($items)) {
                                        return null;
                                    }
                                    return Gallery::widget(['items' => $items]);
                                }
                            ],
                            [
                                'label' => '<span class="bordered-title">Link</span>',
                                'attribute' => 'link',
                                'contentOptions' => ['class' => 'link-cell'],
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
