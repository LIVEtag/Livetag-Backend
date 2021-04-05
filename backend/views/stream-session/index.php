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
                <div class="box-header">
                    <?php if ($user && $user->isSeller) : ?>
                        <?= Html::a(Yii::t('app', 'Set'), ['create'], ['class' => 'btn bg-black']) ?>
                    <?php endif; ?>
                </div>
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
                                'vAlign' => GridView::ALIGN_TOP,
                                'headerOptions' => ['width' => '80'],
                            ],
                            [
                                'attribute' => 'name',
                                'vAlign' => GridView::ALIGN_TOP,
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
//                            [
//                                'attribute' => 'announcedAt',
//                                'format' => 'datetime',
//                                'mergeHeader' => true,
//                                'vAlign' => GridView::ALIGN_TOP,
//                                'hAlign' => GridView::ALIGN_LEFT,
//                                'headerOptions' => ['width' => '180'],
//                                'filter' => false
//                            ],
//                            [
//                                'attribute' => 'duration',
//                                'label' => 'Duration',
//                                'headerOptions' => ['width' => '80'],
//                                'filter' => Html::activeDropDownList($searchModel, 'duration', StreamSession::DURATIONS, ['class' => 'form-control', 'prompt' => '']),
//                                'value' => function (StreamSession $model) {
//                                    return $model->getMaximumDuration();
//                                }
//                            ],
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
                                'attribute' => 'actualDuration',
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '100'],
                                'mergeHeader' => true,
                                'filter' => false,
                                'value' => function (StreamSessionSearch $model) {
                                    return $model->getActualDuration();
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
                                'template' => '{update} {view}',
                                'visibleButtons' => [
                                    'update' => function () use ($user) {
                                        return $user && $user->isSeller;
                                    },
                                    'delete' => function () use ($user) {
                                        return $user && $user->isSeller;
                                    }
                                ],
                            ],
                            [
                                'header' => '',
                                'class' => ActionColumn::class,
                                'vAlign' => GridView::ALIGN_TOP,
                                'visible' => $user && $user->isSeller,
                                'template' => '{change-publication-status}',
                                'buttons' => [
                                    'change-publication-status' => function ($url, StreamSession $model) {
                                        if ($model->isPublished) {
                                            return Html::a(
                                                'Unpublish',
                                                ['stream-session/unpublish', 'id' => $model->id],
                                                [
                                                    'title' => 'Unpublish the livestream',
                                                    'data-confirm' => 'Do you want to unpublish this livestream?',
                                                    'data-method' => 'post',
                                                    'class' => 'btn btn-primary btn-publication',
                                                ]
                                            );
                                        }
                                        return Html::a(
                                            'Publish',
                                            ['stream-session/publish', 'id' => $model->id],
                                            [
                                                'class' => 'btn btn-success btn-publication',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    }
                                ],
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