<?php

/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\controllers\ProductController;
use backend\models\Product\Product;
use kartik\editable\Editable;
use kartik\grid\ActionColumn;
use kartik\grid\EditableColumn;
use kartik\grid\GridView;
use kartik\popover\PopoverX;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\Product\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $statusesAvailable array */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>

<section class="product-index">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <h3>
                        <?= Html::a('CSV file sample', Yii::$app->urlManagerBackend->createAbsoluteUrl(['/web/uploads/product.csv'])) ?>
                    </h3>
                </div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'hover' => true, //the grid table will highlight row on hover
                        'persistResize' => true, //to store resized column state using local storage persistence
                        'options' => ['id' => 'product-list'],
                        'pjax' => true,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'id',
                            ],
                            [
                                'attribute' => 'title',
                            ],
                            [
                                'attribute' => 'sku',
                            ],
                            [
                                'attribute' => 'link',
                                'format' => ['url', ['target' => '_blank']]
                            ],
                            [
                                'attribute' => 'status',
                                'class' => EditableColumn::class,
                                'filter' => $statusesAvailable,
                                'value' => function (Product $model) {
                                    return  $model->getStatusName();
                                },
                                'refreshGrid' => true,
                                'editableOptions' => function (Product $model) use ($statusesAvailable) {
                                    return [
                                        'name' => 'status',
                                        'value' => $model->getStatusName(),
                                        'format' => Editable::FORMAT_LINK,
                                        'inputType' => Editable::INPUT_SELECT2,
                                        'placement' => PopoverX::ALIGN_LEFT,
                                        'options' => [
                                            'class' => 'form-control',
                                            'data' => $statusesAvailable,
                                        ],
                                        'formOptions' => ['action' => [ProductController::ACTION_EDITABLE_STATUS]]
                                    ];
                                },
                                'headerOptions' => ['width' => '75'],
                            ],
                            [
                                'label' => 'Option',
                                'attribute' => 'options',
                                'format' => 'raw',
                                'mergeHeader' => true,
                                'value' => function (Product $model) {
                                    return $model->getProductOptionsInHTML($model->options) ?? '';
                                },
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_CENTER,
                            ],
                            [
                                'attribute' => 'photo',
                                'format' => ['image',
                                    [
                                        'width' => '75'
                                    ]
                                ],
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_CENTER,
                                'headerOptions' => ['width' => '100'],
                                'mergeHeader' => true,
                            ],
                            ['class' => ActionColumn::class,
                             'template' => '{delete}'
                            ],
                        ],
                    ]); ?>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>