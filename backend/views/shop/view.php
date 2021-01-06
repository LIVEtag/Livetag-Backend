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
                    <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn bg-black']) ?>
                    <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]); ?>
                </div>
                <!--/.box-header -->
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'name',
                            [
                                'attribute' => 'website',
                                'format' => ['url', ['target' => '_blank']]
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
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h4 class="box-title pull-right">Sellers</h4>
                    <?= Html::a(Yii::t('app', 'Add a seller'), ['/user/create', 'shopId' => $model->id], ['class' => 'btn bg-black']) ?>
                </div>
                <!--/.box-header -->
                <div class="box-body">
                    <?= GridView::widget([
                        'dataProvider' => $userDataProvider,
                        'filterModel' => $userSearchModel,
                        'options' => ['id' => 'shop-sellers-list'],
                        'pjax' => true,
                        'hover' => true, //the grid table will highlight row on hover
                        'persistResize' => true, //to store resized column state using local storage persistence
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'hAlign' => GridView::ALIGN_CENTER,
                                'headerOptions' => ['width' => '80'],
                            ],
                            'email:email',
                            [
                                'attribute' => 'createdAt',
                                'format' => 'datetime',
                                'mergeHeader' => true,
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_CENTER,
                                'headerOptions' => ['width' => '200'],
                                'filter' => false
                            ],
                            [
                                'class' => ActionColumn::class,
                                'vAlign' => GridView::ALIGN_TOP,
                                'template' => '{view} {delete}',
                                'deleteOptions' => [
                                    'data-confirm' => Yii::t('app', 'Are you sure that you want to delete this seller?')
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
                <!-- /.box-body -->
                <div class="box-footer"></div>
                <!--/.box-footer -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- /.section -->