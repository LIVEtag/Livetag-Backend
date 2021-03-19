<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\assets\HighlightAsset;
use backend\models\Shop\Shop;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Shop */

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;

HighlightAsset::register($this);

$this->registerJsFile('/backend/web/js/highlight.js', [
    'depends' => [HighlightAsset::class],
]);
?>
<section class="shop-view">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <?= Html::a(Yii::t('app', 'Update'), ['update-my'], ['class' => 'btn btn-primary']) ?>
                </div>
                <!--/.box-header -->
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute' => 'logo',
                                'format' => ['image', ['width' => '150']],
                                'value' => function ($model) {
                                    return $model->getUrl();
                                }
                            ],
                            'name',
                            'uri',
                            [
                                'attribute' => 'website',
                                'format' => ['url', ['target' => '_blank']]
                            ],
                            'createdAt:datetime',
                            [
                                'label' => 'Documentation',
                                'format' => ['url', ['target' => '_blank']],
                                'value' => function () {
                                    return Yii::$app->urlManagerSDK->getBaseUrl();
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

    <?php if ($snippet) : ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h4 class="box-title">Integration Snippet</h4>
                    </div>
                    <!--/.box-header -->
                    <div class="box-body">
                        <pre><code class="language-html"><?= $snippet; ?></code></pre>
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
    <?php endif; ?>
</section>
<!-- /.section -->