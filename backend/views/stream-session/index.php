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

$type = Yii::$app->request->getQueryParam('type');
$tabTitle = 'Upcoming shows';
$tabStatuses = [StreamSession::STATUS_NEW => 'New'];
if ($type == StreamSessionSearch::TYPE_ACTIVE_AND_PAST)  {
    $tabTitle = 'Active and past shows';
    $tabStatuses = array_diff(StreamSession::STATUSES, $tabStatuses);
}

/** @var User $user */
$user = Yii::$app->user->identity ?? null;
?>
<section class="stream-session-index">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <?= Html::a('<span class="page-title">Setup</span>', ['index', 'type' => StreamSessionSearch::TYPE_UPCOMING]); ?>
                        <?= Html::a('<span class="page-title">Moderate</span>', ['index', 'type' => StreamSessionSearch::TYPE_ACTIVE_AND_PAST]); ?>
                        <?php if ($user && $user->isSeller) : ?>
                            <li class="pull-right setup-content buttons-content">
                                <div><?= Html::a(Yii::t('app', '+ Create new show'), ['create'], ['class' => 'button button--dark button--upper button--lg']) ?></div>
                            </li>
                            <li class="pull-right setup-content buttons-content">
                                <div><?= Html::a(Yii::t('app', '+ Upload recorded show'), ['upload-recorded-show'], ['class' => 'button button--dark button--ghost button--upper button--lg']) ?></div>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="box-header box-header--no-indent">
                                <div class="section-box-header">
                                    <span class="box-title"><?= $tabTitle ?></span>
                                </div>
                            </div>
                            <div class="box-body table-responsive">
                                <?= GridView::widget([
                                    'dataProvider' => $dataProvider,
                                    'hover' => true, //the grid table will highlight row on hover
                                    'persistResize' => true, //to store resized column state using local storage persistence
                                    'options' => ['id' => 'stream-session-list', 'class' => 'gridview-wrapper'],
                                    'pjax' => true,
                                    'filterModel' => $searchModel,
                                    'columns' => [
                                        [
                                            'attribute' => 'id',
                                            'label' => 'Id',
                                            'hAlign' => GridView::ALIGN_LEFT,
                                            'vAlign' => GridView::ALIGN_TOP,
                                            'headerOptions' => ['width' => '80'],
                                        ],
                                        [
                                            'attribute' => 'name',
                                            'vAlign' => GridView::ALIGN_TOP,
                                            'contentOptions' => ['class' => 'name-cell'],
                                        ],
                                        [
                                            'attribute' => 'shopId',
                                            'label' => 'Shop',
                                            'format' => 'raw',
                                            'filter' => Html::tag('div', Html::activeDropDownList($searchModel, 'shopId', Shop::getIndexedArray(), ['class' => 'form-control', 'prompt' => '']), ['class' => 'select-wrapper no-label']),
                                            'value' => function (StreamSessionSearch $model) {
                                                return $model->shopId ? Html::a($model->shop->name, ['/shop/view', 'id' => $model->shop->id], ['data-pjax' => '0']) : null;
                                            },
                                            'visible' => $user && $user->isAdmin,
                                        ],
                                        [
                                            'attribute' => 'startedAt',
                                            'label' => 'Started at',
                                            'format' => 'datetime',
                                            'mergeHeader' => true,
                                            'vAlign' => GridView::ALIGN_TOP,
                                            'hAlign' => GridView::ALIGN_LEFT,
                                            'headerOptions' => ['width' => '180'],
                                            'filter' => false
                                        ],
                                        [
                                            'label' => 'Actual duration',
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
                                            'attribute' => 'totalViewCount',
                                            'headerOptions' => ['width' => '150'],
                                            'vAlign' => GridView::ALIGN_TOP,
                                            'hAlign' => GridView::ALIGN_LEFT,
                                        ],
                                        [
                                            'label' => '“Add to cart” clicks',
                                            'attribute' => 'totalAddToCartCount',
                                            'headerOptions' => ['width' => '150'],
                                            'vAlign' => GridView::ALIGN_TOP,
                                            'hAlign' => GridView::ALIGN_LEFT,
                                        ],
                                        [
                                            'label' => '“Add to cart” rate',
                                            'attribute' => 'totalAddToCartRate',
                                            'headerOptions' => ['width' => '150'],
                                            'vAlign' => GridView::ALIGN_TOP,
                                            'hAlign' => GridView::ALIGN_LEFT,
                                        ],
                                        [
                                            'label' => 'Likes',
                                            'attribute' => 'likes',
                                            'headerOptions' => ['width' => '80'],
                                            'mergeHeader' => true,
                                            'vAlign' => GridView::ALIGN_TOP,
                                            'hAlign' => GridView::ALIGN_LEFT,
                                        ],
                                        [
                                            'attribute' => 'status',
                                            'vAlign' => GridView::ALIGN_TOP,
                                            'hAlign' => GridView::ALIGN_LEFT,
                                            'format' => 'html',
                                            'headerOptions' => ['width' => '100'],
                                            'filter' => Html::tag('div', Html::activeDropDownList($searchModel, 'status', $tabStatuses, ['class' => 'form-control', 'prompt' => '']), ['class' => 'select-wrapper no-label']),
                                            'value' => function (StreamSessionSearch $model) {
                                                return Html::tag("span", $model->getStatusName(), ['class' => 'status-label status-label--' . $model->getStatusClass()]);
                                            },
                                        ],
                                        [
                                            'class' => ActionColumn::class,
                                            'vAlign' => GridView::ALIGN_TOP,
                                            'contentOptions' => ['class' => 'action-button-cell'],
                                            'template' => '{view} {update}',
                                            'visibleButtons' => [
                                                'update' => function () use ($user) {
                                                    return $user && $user->isSeller;
                                                },
                                                'delete' => function () use ($user) {
                                                    return $user && $user->isSeller;
                                                }
                                            ],
                                            'buttons' => [
                                                'update' => function ($url) {
                                                    return Html::a('<span class="icon icon-pen"></span> Edit', $url, ['class' => 'action-button button button--darken button--ghost']);
                                                },
                                                'view' => function ($url) {
                                                    return Html::a('<span class="icon icon-eye"></span> View', $url, ['class' => 'action-button button button--darken button--ghost', 'data-pjax' => '0']);
                                                }
                                            ]
                                        ],
                                    ],
                                ]); ?>
                            </div>
                        </div>
                        <div class="tab-pane">
                            <div class="box-header box-header--no-indent">
                                <div class="section-box-header">
                                    <span class="box-title">Active and past shows</span>
                                </div>
                            </div>
                            <div class="box-body table-responsive">

                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>