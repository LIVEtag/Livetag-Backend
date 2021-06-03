<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Shop\Shop;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Shop */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shops'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="shop-view">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'button button--dark button--ghost button--upper button--lg']) ?>
                    <div class="buttons-group">
                        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'button button--dark button--upper button--lg']) ?>
                        <?= Html::a(Yii::t('app', 'Delete shop'), ['delete', 'id' => $model->id], [
                            'class' => 'button button--danger button--ghost button--upper button--lg',
                            'data' => [
                                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header section-box-header">
                    <h4 class="box-title">Info</h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
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
                            'updatedAt:datetime',
                        ],
                    ]); ?>
                </div>
                <!-- /.box-body -->
                <div class="box-footer"></div>
                <!--/.box-footer -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-6">
            <?= $this->render('shop-analytics', ['shop' => $model]); ?>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <div class="buttons-group">
                        <?= Html::a(Yii::t('app', 'Add a seller'), ['/user/create', 'shopId' => $model->id], ['class' => 'button button--dark button--upper button--lg']) ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header section-box-header">
                    <h4 class="box-title">Sellers</h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!--/.box-header -->
                <div class="box-body">
                    <?= GridView::widget([
                        'dataProvider' => $userDataProvider,
                        'filterModel' => $userSearchModel,
                        'options' => ['id' => 'shop-sellers-list', 'class' => 'gridview-wrapper'],
                        'pjax' => true,
                        'hover' => true, //the grid table will highlight row on hover
                        'persistResize' => true, //to store resized column state using local storage persistence
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '80'],
                            ],
                            'email:email',
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
                                'template' => '{view} {delete}',
                                'contentOptions' => ['class' => 'action-button-cell'],
                                'deleteOptions' => [
                                    'data-confirm' => Yii::t('app', 'Are you sure that you want to delete this seller?')
                                ],
                                'buttons' => [
                                    'view' => function ($url) {
                                        return Html::a("<span class='icon icon-eye'></span>View", $url, ['class' => 'action-button button button--darken button--ghost', 'data-pjax' => '0']);
                                    },
                                    'delete' => function ($url) {
                                        return Html::a("<span class='icon icon-trash'></span>", $url, ['class' => 'action-button button button--link button--icon', 'data-pjax' => '0', 'data-confirm' => Yii::t('app', 'Are you sure to delete this item?'), 'data-method' => 'post']);
                                    },
                                ],
                                'urlCreator' => function ($action, $model) {
                                    $params = ['id' => $model->id];
                                    $params[0] = 'user/' . $action;
                                    return Url::toRoute($params);
                                },
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

</section>
<!-- /.section -->