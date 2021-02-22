<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\Shop\Shop;
use backend\models\Stream\StreamSession;
use backend\models\Stream\StreamSessionSearch;
use backend\models\User\User;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel StreamSessionSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Livestreams');
$this->params['breadcrumbs'][] = $this->title;

/** @var User $user */
$user = Yii::$app->user->identity ?? null;
?>
<section class="stream-session-index">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header"></div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'hover' => true, //the grid table will highlight row on hover
                        'persistResize' => true, //to store resized column state using local storage persistence
                        'options' => ['id' => 'stream-session-list'],
                        'pjax' => true,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '80'],
                            ],
                            [
                                'attribute' => 'shopId',
                                'label' => 'Shop',
                                'format' => 'raw',
                                'filter' => Html::activeDropDownList($searchModel, 'shopId', Shop::getIndexedArray(), ['class' => 'form-control', 'prompt' => '']),
                                'value' => function (StreamSessionSearch $model) {
                                    return $model->shopId ? Html::a($model->shop->name, ['/shop/view', 'id' => $model->shop->id], ['data-pjax' => '0']) : null;
                                },
                                'visible' => $user && $user->isAdmin,
                            ],
                            [
                                'attribute' => 'startedAt',
                                'label' => 'Date and time',
                                'format' => 'datetime',
                                'mergeHeader' => true,
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '180'],
                                'filter' => false
                            ],
                            [
                                'attribute' => 'duration',
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '100'],
                                'mergeHeader' => true,
                                'filter' => false,
                                'value' => function (StreamSessionSearch $model) {
                                    return $model->duration ? gmdate("H:i:s", $model->duration) : null;
                                }
                            ],
                            [
                                'label' => 'Number of views',
                                'attribute' => 'viewsCount',
                                'headerOptions' => ['width' => '150'],
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_LEFT,
                            ],
                            [
                                'label' => '“Add to cart” clicks',
                                'attribute' => 'addToCartCount',
                                'headerOptions' => ['width' => '150'],
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_LEFT,
                            ],
                            [
                                'label' => '“Add to cart” rate',
                                'attribute' => 'addToCartRate',
                                'headerOptions' => ['width' => '150'],
                                'mergeHeader' => true,
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_LEFT,
                            ],
                            [
                                'attribute' => 'status',
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '100'],
                                'filter' => Html::activeDropDownList($searchModel, 'status', StreamSession::STATUSES, ['class' => 'form-control', 'prompt' => '']),
                                'value' => function (StreamSessionSearch $model) {
                                    return $model->getStatusName();
                                },
                            ],
                            [
                                'class' => ActionColumn::class,
                                'vAlign' => GridView::ALIGN_TOP,
                                'template' => '{view}'
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