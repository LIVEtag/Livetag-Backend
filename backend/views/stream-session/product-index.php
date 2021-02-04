<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\controllers\StreamSessionController;
use backend\models\Product\StreamSessionProduct;
use kartik\editable\Editable;
use kartik\grid\ActionColumn;
use kartik\grid\EditableColumn;
use kartik\grid\GridView;
use kartik\popover\PopoverX;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
?>

<?=
GridView::widget([
    'dataProvider' => $productDataProvider,
    'filterModel' => $productSearchModel,
    'filterUrl' => ['/stream-session/view', 'id' => $streamSessionId],
    'options' => ['id' => 'session-product-list'],
    'pjax' => true,
    'hover' => true, //the grid table will highlight row on hover
    'resizableColumns' => false,
    'columns' => [
        [
            'attribute' => 'productId',
            'label' => 'ID',
            'hAlign' => GridView::ALIGN_CENTER,
            'headerOptions' => ['width' => '60'],
        ],
        [
            'attribute' => 'sku',
            'value' => 'product.sku',
            'label' => 'SKU',
            'hAlign' => GridView::ALIGN_CENTER,
            'headerOptions' => ['width' => '100'],
        ],
        [
            'attribute' => 'title',
            'value' => 'product.title',
            'hAlign' => GridView::ALIGN_CENTER,
        ],
        [
            'attribute' => 'link',
            'value' => 'product.link',
            'headerOptions' => ['width' => '120'],
            'format' => ['url', ['target' => '_blank']]
        ],
        [
            'attribute' => 'options',
            'format' => 'raw',
            'mergeHeader' => true,
            'value' => function (StreamSessionProduct $model) {
                return $model->product ? $model->product->getProductOptionsInHTML() : null;
            },
            'hAlign' => GridView::ALIGN_CENTER,
            'vAlign' => GridView::ALIGN_TOP,
        ],
        [
            'attribute' => 'status',
            'class' => EditableColumn::class,
            'filter' => StreamSessionProduct::STATUSES,
            'value' => function (StreamSessionProduct $model) {
                return $model->getStatusName();
            },
            'refreshGrid' => true,
            'editableOptions' => function (StreamSessionProduct $model) {
                return [
                    'name' => 'status',
                    'value' => $model->getStatusName(),
                    'format' => Editable::FORMAT_LINK,
                    'inputType' => Editable::INPUT_SELECT2,
                    'placement' => PopoverX::ALIGN_LEFT,
                    'options' => [
                        'class' => 'form-control',
                        'data' => StreamSessionProduct::STATUSES,
                    ],
                    'formOptions' => ['action' => ['stream-session/' . StreamSessionController::ACTION_EDITABLE_PRODUCT]]
                ];
            },
            'headerOptions' => ['width' => '75'],
        ],
        [
            'attribute' => 'photo',
            'value' => 'product.photo',
            'format' => ['image', ['width' => '75']],
            'vAlign' => GridView::ALIGN_TOP,
            'hAlign' => GridView::ALIGN_CENTER,
            'headerOptions' => ['width' => '100'],
            'mergeHeader' => true,
        ],
        [
            'class' => ActionColumn::class,
            'vAlign' => GridView::ALIGN_TOP,
            'template' => '{delete-product}',
            'buttons' => [
                'delete-product' => function ($url) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'data-pjax' => true,
                            'title' => 'Delete',
                            'data-confirm' => 'Are you sure you want to delete this item?',
                            'data-method' => 'post'
                    ]);
                }
            ],
        ],
    ],
]);
?>
