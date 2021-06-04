<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\assets\HighlightAsset;
use backend\models\Shop\Shop;
use yii\helpers\Html;
use yii\helpers\Url;
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
                    <div class="buttons-group">
                        <?= Html::a(Yii::t('app', 'Update'), ['update-my'], ['class' => 'button button--dark button--upper button--lg']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header section-box-header box-header--no-indent">
                    <h4 class="box-title">Details</h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!--/.box-header -->
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute' => 'logo',
                                'format' => 'raw',
                                'value' => function (Shop $model) {
                                    $imageUrl = $model->getUrl();
                                    if (!$imageUrl) {
                                        return null;
                                    }

                                    $action = Url::to(['/shop/delete-logo', 'id' => $model->id]);
                                    return "<div class=\"shop-logo\">
                                                <a type=\"button\" class=\"action-button button button--dark button--icon stream-cover-trash\"
                                                    href=\"{$action}\" title=\"Delete the item\" data-method=\"post\"
                                                    data-confirm=\"Are you sure to delete this item?\">
                                                    <i class=\"icon icon-trash-light\"></i>
                                                </a>
                                                <img src=\"{$imageUrl}\" class=\"shop-logo__image\">
                                            </div>";
                                }
                            ],
                            'name',
                            'uri',
                            [
                                'attribute' => 'website',
                                'contentOptions' => ['class' => 'link-cell'],
                                'format' => ['url', ['target' => '_blank']]
                            ],
                            [
                                'attribute' => 'iconsTheme',
                                'value' => function (Shop $model) {
                                    return $model->getIconsThemeName();
                                }
                            ],
                            [
                                'label' => 'Icons color themes variants',
                                'format' => 'raw',
                                'value' => function () {
                                    return Html::img(Yii::getAlias('@web') . '/images/iconsThemes.svg');
                                }
                            ],
                            [
                                'attribute' => 'productIcon',
                                'value' => function (Shop $model) {
                                    return $model->getProductIconName();
                                }
                            ],
                            [
                                'label' => 'Product icon options',
                                'format' => 'raw',
                                'value' => function () {
                                    return Html::img(Yii::getAlias('@web') . '/images/productIcons.svg', ['class' => 'product-icons']);
                                }
                            ],
                            'createdAt:datetime',
                            [
                                'label' => 'Documentation',
                                'contentOptions' => ['class' => 'link-cell'],
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
        </div>
        <div class="col-md-6">
            <?= $this->render('shop-analytics', ['shop' => $model ]); ?>
            <?php if ($snippet) : ?>
                <div class="box box-default">
                <div class="box-header section-box-header box-header--no-indent">
                    <h4 class="box-title">Integration Snippet</h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!--/.box-header -->
                <div class="box-body">
                    <pre><code class="language-html"><?= $snippet; ?></code></pre>
                </div>
                <!-- /.box-body -->
                <div class="box-footer"></div>
                <!--/.box-footer -->
            </div>
            <?php endif; ?>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- /.section -->