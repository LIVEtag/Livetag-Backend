<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Shop\Shop;
use backend\models\Shop\ShopSearch;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel ShopSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Shops');
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="shop-index">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <?= Html::a(Yii::t('app', 'Create a shop'), ['create'], ['class' => 'btn bg-black']) ?>
                </div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'hover' => true, //the grid table will highlight row on hover
                        'persistResize' => true, //to store resized column state using local storage persistence
                        'options' => ['id' => 'shop-list'],
                        'pjax' => true,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '80'],
                            ],
                            'name',
                            [
                                'attribute' => 'website',
                                'format' => ['url', ['target' => '_blank']]
                            ],
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