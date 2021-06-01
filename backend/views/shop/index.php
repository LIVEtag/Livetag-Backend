<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Shop\Shop;
use backend\models\Shop\ShopSearch;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel ShopSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Shops');
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="shop-index">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <div class="buttons-group">
                        <?= Html::a(Yii::t('app', 'Create a shop'), ['create'], ['class' => 'button button--dark button--upper button--lg']) ?>
                    </div>
                </div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'hover' => true, //the grid table will highlight row on hover
                        'persistResize' => true, //to store resized column state using local storage persistence
                        'options' => ['id' => 'shop-list', 'class' => 'gridview-wrapper'],
                        'pjax' => true,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '80'],
                            ],
                            [
                                'attribute' => 'logo',
                                'format' => ['image', ['width' => '100']],
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_LEFT,
                                'mergeHeader' => true,
                                'value' => function (Shop $model) {
                                    return $model->getUrl();
                                }
                            ],
                            [
                                'label' => 'Name',
                                'attribute' => 'name',
                                'contentOptions' => ['class' => 'name-cell'],
                            ],
                            'uri',
                            [
                                'attribute' => 'website',
                                'format' => ['url', ['target' => '_blank']],
                                'contentOptions' => ['class' => 'link-cell'],
                            ],
                            [
                                'attribute' => 'createdAt',
                                'format' => 'datetime',
                                'mergeHeader' => true,
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '200'],
                                'filter' => false
                            ],
                            [
                                'class' => ActionColumn::class,
                                'vAlign' => GridView::ALIGN_TOP,
                                'contentOptions' => ['class' => 'action-button-cell'],
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'view' => function ($url) {
                                        return Html::a('<span class="icon icon-eye"></span> View', $url, ['class' => 'action-button button button--darken button--ghost', 'data-pjax' => '0']);
                                    },
                                    'update' => function ($url) {
                                        return Html::a('<span class="icon icon-pen"></span> Edit', $url, ['class' => 'action-button button button--darken button--ghost', 'data-pjax' => '0']);
                                    },
                                    'delete' => function ($url) {
                                        return Html::a('<span class="icon icon-trash"></span>', $url, [
                                            'class' => 'action-button button button--link button--icon',
                                            'data' => [
                                                'confirm' => 'Are you sure to delete this item?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                    }
                                ]
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