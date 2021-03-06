<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\User\User;
use backend\models\User\UserSearch;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel UserSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Sellers management');
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="user-index">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <div class="buttons-group">
                        <?= Html::a(Yii::t('app', 'Add a seller'), ['create'], ['class' => 'button button--dark button--upper button--lg']) ?>
                    </div>
                </div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'hover' => true, //the grid table will highlight row on hover
                        'persistResize' => true, //to store resized column state using local storage persistence
                        'options' => ['id' => 'user-list', 'class' => 'gridview-wrapper'],
                        'pjax' => true,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '80'],
                            ],
                            [
                                'label' => 'Name',
                                'attribute' => 'name',
                                'contentOptions' => ['class' => 'name-cell'],
                            ],
                            'email:email',
                            [
                                'label' => 'Shop name',
                                'attribute' => 'shopName',
                                'format' => 'raw',
                                'value' => function (User $model) {
                                    return $model->shop ? Html::a($model->shop->name, ['/shop/view', 'id' => $model->shop->id], ['data-pjax' => '0']) : null;
                                }
                            ],
                            [
                                'label' => 'Created at',
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
                                'contentOptions' => ['class' => 'action-button-cell'],
                                'template' => '{view} {delete}',
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