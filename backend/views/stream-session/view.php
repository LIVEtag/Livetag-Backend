<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\assets\HighlightAsset;
use backend\models\Comment\Comment;
use backend\models\Comment\CommentSearch;
use backend\models\Product\StreamSessionProductSearch;
use backend\models\Stream\StreamSession;
use backend\models\User\User;
use common\models\Stream\StreamSessionArchive;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
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
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Livestreams'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/** @var User $user */
$user = Yii::$app->user->identity ?? null;

$publishOptions = [
    'class' => 'btn btn-success',
];
$publishUrl = ['publish', 'id' => $model->id];

if ($model->isPublished) {
    $publishOptionsExtra = [
        'data' => [
            'confirm' => Yii::t('app', 'Do you want to unpublish this livestream?'),
            'method' => 'post',
        ]
    ];

    if ($model->isActive()) {
        $publishOptionsExtra = [
            'disabled' => 'disabled',
            'title' => Yii::t('app', 'You cannot unpublish while Live Stream is active'),
        ];
    }
    $publishOptions = array_merge([
        'id' => 'publication-link',
        'class' => 'btn btn-danger',
        ], $publishOptionsExtra);
    $publishUrl = ['unpublish', 'id' => $model->id];
}

$this->registerJs(
    '$("#reset-button").on("click", function(val) {
        $.pjax.reload({container:"#comment-list-pjax"});  //Reload GridView
    });

    $(\'a[data-toggle="tab"][href="#comments"]\').on("shown.bs.tab", function (e) {
        $(".comments-content").show();
    })
    $(\'a[data-toggle="tab"][href="#products"]\').on("shown.bs.tab", function (e) {
        $(".comments-content").hide();
    })

    $(\'#publication-link\').on(\'click\', function(e) {
        if ($(this).attr(\'disabled\') == \'disabled\') {
            e.preventDefault();
        }
    });
    '
);

$this->registerJsFile('/backend/web/js/highlight.js', [
    'depends' => [HighlightAsset::class],
]);

?>
<section class="stream-session-view">
    <div class="row">
        <div class="col-md-7">
            <div class="box box-default">
                <div class="box-header">
                    <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn bg-black']) ?>
                    <?php if ($user && $user->isSeller) : ?>
                        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(
                            Yii::t('app', $model->isPublished ? 'Unpublish' : 'Publish'),
                            $publishUrl,
                            $publishOptions
                        ); ?>
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
                                'visible' => $user && $user->isAdmin,
                                'format' => ['image', ['width' => '200']],
                                'value' => function (StreamSession $model) {
                                    return $model->getCoverUrl();
                                }
                            ],
                            [
                                'label' => 'Photo (cover image)',
                                'visible' => $user && $user->isSeller,
                                'format' => 'raw',
                                'value' => function (StreamSession $model) {
                                    $imageUrl = $model->getCoverUrl();
                                    if (!$imageUrl) {
                                        return null;
                                    }

                                    $action = Url::to(['/stream-session/delete-cover-image', 'id' => $model->id]);
                                    return "<div class=\"shop-logo\">
                                                <div class=\"shop-logo__trash\">
                                                    <a type=\"button\" class=\"btn btn-sm btn-default\"
                                                        href=\"{$action}\" title=\"Delete the item\" data-method=\"post\"
                                                        data-confirm=\"Are you sure to delete this item?\">
                                                        <i class=\"glyphicon glyphicon-trash\"></i>
                                                    </a>
                                                </div>
                                                <img src=\"{$imageUrl}\" class=\"shop-logo__image\">
                                            </div>";
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
                            [
                                'attribute' => 'internalCart',
                                'value' => function (StreamSession $model) {
                                    return $model->getInternalCartText();
                                }
                            ],
                            [
                                'label' => 'Integration Snippet',
                                'format' => 'raw',
                                'value' => function () use ($snippet) {
                                    return '<pre><code class="language-html">' . $snippet . '</code></pre>';
                                }
                            ]
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
        <div class="col-md-5">
            <div class="box box-default">
                <div class="box-header">
                    <?php if ($model->isStopped() || $model->isArchived()) : ?>
                        <?= Html::a(Yii::t('app', 'Upload record'), ['upload-record', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?php endif; ?>
                    <?php if ($model->archive) : ?>
                        <?= Html::a(Yii::t('app', 'Delete record'), ['delete-record', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('app', 'Are you sure to delete this item?'),
                                'method' => 'post',
                            ],
                        ]); ?>
                    <?php endif; ?>
                    <h4 class="box-title pull-right">Recorded video </h4>
                </div>
                <!--/.box-header -->
                <div class="box-body">
                    <?php if ($model->archive) : ?>
                        <?= DetailView::widget([
                            'model' => $model->archive,
                            'attributes' => [
                                'id',
                                [
                                    'attribute' => 'status',
                                    'value' => function (StreamSessionArchive $model) {
                                        return $model->getStatusName();
                                    },
                                ],
                                [
                                    'attribute' => 'duration',
                                    'value' => function (StreamSessionArchive $model) {
                                        return $model->getFormattedDuration();
                                    }
                                ],
                                'createdAt:datetime',
                                'updatedAt:datetime',
                                [
                                    'label' => 'Recorded video link',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        $url = $model->getUrl();
                                        if (!$url) {
                                            return null;
                                        }
                                        return Html::a($url, $url, ['target' => '_blank']);
                                    }
                                ],
                                [
                                    'label' => 'Playlist link',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        $url = $model->getPlaylistUrl();
                                        if (!$url) {
                                            return null;
                                        }
                                        return Html::a($url, $url, ['target' => '_blank']);
                                    }
                                ]
                            ],
                        ]); ?>
                    <?php else : ?>
                        <p>Record not available</p>
                    <?php endif; ?>
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