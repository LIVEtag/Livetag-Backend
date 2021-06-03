<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\assets\AppAsset;
use backend\assets\HighlightAsset;
use backend\models\Comment\Comment;
use backend\models\Comment\CommentSearch;
use backend\models\Product\StreamSessionProductSearch;
use backend\models\Stream\StreamSession;
use backend\models\User\User;
use common\models\Analytics\StreamSessionStatistic;
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

$this->title = 'Livestream details ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Livestreams'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/** @var User $user */
$user = Yii::$app->user->identity ?? null;

$publishOptions = [
    'class' => 'button button--success button--upper button--lg',
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
        'class' => 'button button--dark button--upper button--lg',
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

$this->registerJsFile('/backend/web/js/comment-reply.js', [
    'depends' => [AppAsset::class],
]);
$this->registerJsFile('/backend/web/js/highlight.js', [
    'depends' => [HighlightAsset::class],
]);

?>
<section class="stream-session-view">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'button button--dark button--ghost button--upper button--lg']) ?>

                    <div class="buttons-group">
                        <?php if ($user && $user->isSeller) : ?>
                            <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'button button--dark button--upper button--lg']) ?>
                            <?= Html::a(
                                Yii::t('app', $model->isPublished ? 'Unpublish' : 'Publish'),
                                $publishUrl,
                                $publishOptions
                            ); ?>
                            <?php if ($model->isActive()) : ?>
                                <?= Html::a(Yii::t('app', 'End livestream'), ['stop', 'id' => $model->id], [
                                    'class' => 'button button--danger button--ghost button--upper button--lg',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to end the livestream?'),
                                        'method' => 'post',
                                    ],
                                ]); ?>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($model->isStopped() || $model->isArchived()) : ?>
                            <?= Html::a(Yii::t('app', '+ Upload record'), ['upload-record', 'id' => $model->id], ['class' => 'button button--dark button--ghost button--upper']) ?>
                        <?php endif; ?>
                        <?php if ($model->archive) : ?>
                            <?= Html::a(Yii::t('app', 'Delete record'), ['delete-record', 'id' => $model->id], [
                                'class' => 'button button--danger button--upper button--ghost',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure to delete this item?'),
                                    'method' => 'post',
                                ],
                            ]); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header section-box-header">
                    <h4 class="box-title">Livestream details</h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!--/.box-header -->
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'name',
                            [
                                'label' => 'Cover (can be image of video)',
                                'visible' => $user && $user->isAdmin,
                                'format' => 'raw',
                                'value' => function (StreamSession $model) {
                                    $url = $model->getCoverUrl();
                                    if (!$url) {
                                        return null;
                                    }
                                    if ($model->streamSessionCover->isVideo()) {
                                        return "<video width=\"200\" controls>
                                                <source src=\"{$url}\" type=\"video/mp4\">
                                                <p>Your browser doesn't support video. Here is a <a href=\"{$url}\">link to the video</a> instead.</p>
                                            </video>";
                                    }
                                    return Html::img($url, ['class' => 'shop-logo__image']);
                                }
                            ],
                            [
                                'label' => 'Photo (cover image)',
                                'visible' => $user && $user->isSeller,
                                'format' => 'raw',
                                'value' => function (StreamSession $model) {
                                    $url = $model->getCoverUrl();
                                    if (!$url) {
                                        return null;
                                    }
                                    $coverHtml = "<img src=\"{$url}\" class=\"shop-logo__image\">";
                                    if ($model->streamSessionCover->isVideo()) {
                                        $coverHtml = "<video width=\"200\" controls>
                                                          <source src=\"{$url}\" type=\"video/mp4\">
                                                          <p>Your browser doesn't support video. Here is a <a href=\"{$url}\">link to the video</a> instead.</p>
                                                      </video>";
                                    }
                                    $action = Url::to(['/stream-session/delete-cover-file', 'id' => $model->id]);

                                    return "<div class=\"shop-logo text-center\">
                                                    <a type=\"button\" class=\"action-button button button--dark button--icon stream-cover-trash\"
                                                        href=\"{$action}\" title=\"Delete the item\" data-method=\"post\"
                                                        data-confirm=\"Are you sure to delete this item?\">
                                                        <span class=\"icon icon-trash-light\"></span>
                                                    </a>
                                                {$coverHtml}
                                            </div>";
                                }
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'html',
                                'value' => function (StreamSession $model) {
                                    return Html::tag("span", $model->getStatusName(), ['class' => 'status-label status-label--' . $model->getStatusClass()]);
                                },
                            ],
                            'rotate',
                            [
                                'attribute' => 'shopId',
                                'label' => 'Shop',
                                'format' => 'raw',
                                'value' => function (StreamSession $model) {
                                    return $model->shopId ? Html::a($model->shop->name, ['/shop/view', 'id' => $model->shop->id], ['data-pjax' => '0']) : null;
                                },
                                'visible' => $user && $user->isAdmin,
                            ],
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
                                'label' => 'Actual duration',
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
                                'attribute' => 'sessionId',
                                'label' => '<span class="bordered-title">Session ID</span>',
                                'format' => 'html',
                                'value' => function (StreamSession $model) {
                                    if ($model->sessionId) {
                                        return '<pre><code class="language-html">' . $model->sessionId . '</code></pre>';
                                    }
                                    return null;
                                }
                            ],
                            [
                                'label' => '<span class="bordered-title">Integration Snippet</span>',
                                'format' => 'html',
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
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header section-box-header">
                    <h4 class="box-title">Recorded video</h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
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
                                    'format' => 'html',
                                    'value' => function (StreamSessionArchive $model) {
                                        return Html::tag("span", $model->getStatusName(), ['class' => 'status-label status-label--' . $model->getStatusClass()]);
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
                                    'label' => '<span class="bordered-title">Recorded video link</span>',
                                    'format' => 'html',
                                    'value' => function ($model) {
                                        $url = $model->getUrl();
                                        if (!$url) {
                                            return null;
                                        }
                                        return Html::a($url, $url, ['target' => '_blank']);
                                    }
                                ],
                                [
                                    'label' => '<span class="bordered-title">Playlist link</span>',
                                    'format' => 'html',
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

    <?php if ($model->streamSessionStatistic) : ?>
        <div class="row">
            <div class="col-md-6">
                <div class="box box-default">
                    <div class="box-header section-box-header">
                        <h4 class="box-title">Livestream statistic</h4>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <!--/.box-header -->
                    <div class="box-body">
                        <?=
                        DetailView::widget([
                            'model' => $model->streamSessionStatistic,
                            'attributes' => [
                                'streamViewCount',
                                'streamAddToCartCount',
                                'streamAddToCartRate',
                                [
                                    'label' => 'Likes of the livestream',
                                    'value' => function (StreamSessionStatistic $model) {
                                        return $model->streamSession->getActiveLikes();
                                    },
                                ],
                            ],
                        ]);
                        ?>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer"></div>
                    <!--/.box-footer -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->

            <?php if ($model->archive) : ?>
                <div class="col-md-6">
                    <div class="box box-default">
                        <div class="box-header section-box-header">
                            <h4 class="box-title">Recorded video statistic</h4>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <!--/.box-header -->
                        <div class="box-body">
                            <?=
                            DetailView::widget([
                                'model' => $model->streamSessionStatistic,
                                'attributes' => [
                                    'archiveViewCount',
                                    'archiveAddToCartCount',
                                    'archiveAddToCartRate',
                                    [
                                        'label' => 'Likes of the archive',
                                        'value' => function (StreamSessionStatistic $model) {
                                            return $model->streamSession->getArchivedLikes();
                                        },
                                    ],
                                ],
                            ]);
                            ?>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer"></div>
                        <!--/.box-footer -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#comments" data-toggle="tab" aria-expanded="true">Chat</a></li>
                    <li><a href="#products" data-toggle="tab" aria-expanded="false">Products</a></li>
                    <li class="pull-right comments-content buttons-content">
                        <div class="buttons-group">
                            <?= $this->render('comment-enable-form', ['streamSession' => $model, 'time' => date('H:i:s'),]); ?>
                            <div>
                                <?= Html::a(Yii::t('app', 'Refresh'), 'javascript:void(0);', ['class' => 'button button--dark button--upper', 'id' => "reset-button"]); ?>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="comments">
                        <?= $this->render('comment-index', [
                            'commentSearchModel' => $commentSearchModel,
                            'commentDataProvider' => $commentDataProvider,
                            'commentModel' => $commentModel,
                            'streamSession' => $model,
                        ]); ?>
                        <?php if ($model->isActive()) : ?>
                        <div class="parent-comment-reply">
                            <span>Reply to:</span>
                            <button type="button" class="icon icon-close-light parent-comment-reply__close" aria-label="Close"></button>
                            <div>
                                <strong class="parent-comment parent-comment-name"></strong>,
                                <strong class="parent-comment parent-comment-id"></strong>
                            </div>
                            <strong class="parent-comment parent-comment-date-time"></strong>
                            <div class="parent-comment parent-comment-text"></div>
                        </div>
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