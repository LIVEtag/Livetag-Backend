<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Stream\StreamSession;
use backend\models\User\User;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model StreamSession */

$this->title = 'Livestream details #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Livestream'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/** @var User $user */
$user = Yii::$app->user->identity ?? null;
?>
<section class="stream-session-view">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <?php if ($model->isActive()) : ?>
                        <?= Html::a(Yii::t('app', 'End livestream'), ['stop', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('app', 'Are you sure you want to end the livestream?'),
                                'method' => 'post',
                            ],
                        ]); ?>
                    <?php endif; ?>
                </div>
                <!--/.box-header -->
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'status',
                                'value' => function (StreamSession $model) {
                                    return $model->getStatusName();
                                },
                            ],
                            [
                                'attribute' => 'shopId',
                                'label' => 'Shop',
                                'format' => 'raw',
                                'value' => function (StreamSession $model) {
                                    return $model->shopId ? Html::a($model->shop->name, ['/shop/view', 'id' => $model->shop->id], ['data-pjax' => '0']) : null;
                                },
                                'visible' => $user && $user->isAdmin,
                            ],
                            'sessionId',
                            'createdAt:datetime',
                            'startedAt:datetime',
                            'stoppedAt:datetime',
                            [
                                'label' => 'Duration',
                                'value' => function (StreamSession $model) {
                                    return $model->getDuration();
                                }
                            ],
                        ],
                    ]); ?>
                </div>
                <!-- /.box-body -->
                <div class="box-footer"></div>
                <!--/.box-footer -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <!-- TODO: set active class to comments -->
                    <li class=""><a href="#comments" data-toggle="tab" aria-expanded="true">Comments</a></li>
                    <li class="active"><a href="#products" data-toggle="tab"aria-expanded="false">Products</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane " id="comments">
                        <b>TBU</b>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane active" id="products">
                        <?= $this->render('product-index', [
                            'productSearchModel' => $productSearchModel,
                            'productDataProvider' => $productDataProvider,
                            'streamSessionId' => $model->id,
                        ]); ?>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- nav-tabs-custom -->
        </div>
        <!-- /.col -->
</section>
<!-- /.section -->