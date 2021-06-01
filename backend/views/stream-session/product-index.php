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

$editabletemplateBefore = <<< HTML
<div class="kv-editable-form-inline">
    <div class="form-group">
    </div>
HTML

/* @var $this View */
?>

<?=
GridView::widget([
    'dataProvider' => $productDataProvider,
    'filterModel' => $productSearchModel,
    'filterUrl' => ['/stream-session/view', 'id' => $streamSessionId],
    'options' => ['id' => 'session-product-list', 'class' => 'gridview-wrapper'],
    'pjax' => true,
    'hover' => true, //the grid table will highlight row on hover
    'resizableColumns' => false,
    'columns' => [
        [
            'attribute' => 'productId',
            'label' => 'ID',
            'hAlign' => GridView::ALIGN_LEFT,
            'headerOptions' => ['width' => '60'],
        ],
        [
            'attribute' => 'externalId',
            'value' => 'product.externalId',
            'hAlign' => GridView::ALIGN_LEFT,
            'headerOptions' => ['width' => '100'],
        ],
        [
            'attribute' => 'title',
            'value' => 'product.title',
            'hAlign' => GridView::ALIGN_LEFT,
        ],
        [
            'attribute' => 'link',
            'value' => 'product.link',
            'headerOptions' => ['width' => '120'],
            'contentOptions' => ['class' => 'link-cell'],
            'format' => ['url', ['target' => '_blank']]
        ],
        [
            'attribute' => 'options',
            'format' => 'raw',
            'mergeHeader' => true,
            'vAlign' => GridView::ALIGN_TOP,
            'hAlign' => GridView::ALIGN_LEFT,
            'value' => function (StreamSessionProduct $model) {
                return $model->product ? $model->product->getProductOptionsInHTML() : null;
            },
        ],
        [
            'attribute' => 'status',
            'class' => EditableColumn::class,
            'filter' => StreamSessionProduct::STATUSES,
            'value' => function (StreamSessionProduct $model) {
                return $model->getStatusName();
            },
            'refreshGrid' => true,
            'editableOptions' => function (StreamSessionProduct $model) use ($editabletemplateBefore) {
                return [
                    'name' => 'status',
                    'value' => $model->getStatusName(),
                    'format' => Editable::FORMAT_LINK,
                    'inputType' => Editable::INPUT_SELECT2,
                    'placement' => PopoverX::ALIGN_LEFT,
                    'asPopover' => false,
                    'buttonsTemplate' => '{submit}',
                    'inlineSettings' => [
                        'templateBefore' => $editabletemplateBefore
                    ],
                    'options' => [
                        'class' => 'form-control',
                        'data' => StreamSessionProduct::STATUSES,
                    ],
                    'formOptions' => ['action' => ['stream-session/' . StreamSessionController::ACTION_EDITABLE_PRODUCT]]
                ];
            },
            'headerOptions' => ['width' => '190'],
        ],
        [
            'attribute' => 'photo',
            'value' => 'product.photo',
            'format' => ['image', ['width' => '75']],
            'vAlign' => GridView::ALIGN_TOP,
            'hAlign' => GridView::ALIGN_LEFT,
            'headerOptions' => ['width' => '75'],
            'mergeHeader' => true,
        ],
        [
            'class' => ActionColumn::class,
            'vAlign' => GridView::ALIGN_TOP,
            'template' => '{delete-product}',
            'buttons' => [
                'delete-product' => function ($url) {
                    return Html::a('<span class="icon icon-trash"></span>', $url, [
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
