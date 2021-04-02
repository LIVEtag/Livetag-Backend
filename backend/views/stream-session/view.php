<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\Comment\Comment;
use backend\models\Comment\CommentSearch;
use backend\models\Product\StreamSessionProductSearch;
use backend\models\Stream\StreamSession;
use backend\models\User\User;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model StreamSession */
/* @var $commentSearchModel CommentSearch */
/* @var $commentDataProvider ActiveDataProvider */
/* @var $productSearchModel StreamSessionProductSearch */
/* @var $productDataProvider ActiveDataProvider */
/* @var $commentModel Comment */
/* @var $isPosted bool */

$this->title = 'Livestream details #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Livestream'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/** @var User $user */
$user = Yii::$app->user->identity ?? null;

$this->registerJs(
    '$("#reset-button").on("click", function(val) {
        $.pjax.reload({container:"#comment-list-pjax"});  //Reload GridView
    });

    $(\'a[data-toggle="tab"][href="#comments"]\').on("shown.bs.tab", function (e) {
        $(".comments-content").show();
    })
    $(\'a[data-toggle="tab"][href="#products"]\').on("shown.bs.tab", function (e) {
        $(".comments-content").hide();
    })'
);
?>
<section class="stream-session-view">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn bg-black']) ?>
                    <?php if ($user && $user->isSeller) : ?>
                        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                        <?php if ($model->isActive()) : ?>
                            <?= Html::a(Yii::t('app', 'End livestream'), ['stop', 'id' => $model->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to end the livestream?'),
                                    'method' => 'post',
                                ],
                            ]); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <!--/.box-header -->
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'name',
                            [
                                'label' => 'Photo (cover image)',
                                'format' => ['image', ['width' => '200']],
                                'value' => function (StreamSession $model) {
                                    return $model->getCoverUrl();
                                }
                            ],
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
                            'announcedAt:datetime',
                            [
                                'label' => 'Maximum Duration',
                                'value' => function (StreamSession $model) {
                                    return $model->getMaximumDuration();
                                }
                            ],
                            'createdAt:datetime',
                            'startedAt:datetime',
                            'stoppedAt:datetime',
                            [
                                'label' => 'Number of views',
                                'attribute' => 'streamSessionStatistic.viewsCount',
                            ],
                            [
                                'label' => '“Add to cart” clicks',
                                'attribute' => 'streamSessionStatistic.addToCartCount',
                            ],
                            [
                                'label' => '“Add to cart” rate',
                                'value' => function (StreamSession $model) {
                                    return $model->getAddToCartRate();
                                }
                            ],
                            [
                                'label' => 'Duration',
                                'value' => function (StreamSession $model) {
                                    return $model->getActualDuration();
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
                    <li class="active"><a href="#comments" data-toggle="tab" aria-expanded="true">Chat</a></li>
                    <li><a href="#products" data-toggle="tab" aria-expanded="false">Products</a></li>
                    <li class="pull-right comments-content">
                        <div>
                            <?= Html::a(Yii::t('app', 'Refresh'), 'javascript:void(0);', ['class' => 'btn btn-xs bg-black', 'id' => "reset-button"]); ?>
                        </div>
                    </li>
                    <li class="pull-right comments-content">
                        <?= $this->render('comment-enable-form', ['streamSession' => $model, 'time' => date('H:i:s'),]); ?>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="comments">
                        <?= $this->render('comment-index', [
                            'commentSearchModel' => $commentSearchModel,
                            'commentDataProvider' => $commentDataProvider,
                            'streamSessionId' => $model->id,
                            'commentModel' => $commentModel,
                        ]); ?>
                        <?php if ($model->isActive()) : ?>
                        <!--Display comment form only for active session-->
                            <?= $this->render('comment-form', [
                                'commentModel' => $commentModel,
                                'streamSessionId' => $model->id,
                            ]); ?>
                        <?php endif; ?>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="products">
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