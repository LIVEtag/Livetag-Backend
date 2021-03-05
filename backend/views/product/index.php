<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Product\Product;
use common\models\forms\Product\ProductsUploadForm;
use backend\models\Product\ProductSearch;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use yii\data\ActiveDataProvider;

/* @var $this View */
/* @var $searchModel ProductSearch */
/* @var $dataProvider ActiveDataProvider */
/* @var $model ProductsUploadForm */
/* @var $activeStreamSessionExists bool */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>

<section class="product-index">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <button
                        type="button"
                        class="btn btn-primary bg-black"
                        data-toggle="modal"
                        data-target="#ModalUploadCsvForm"
                        <?= $activeStreamSessionExists ? 'disabled title="' . Yii::t('app', 'You cannot upload products while Live Stream is active') . '"' : ''; ?>  >
                        Upload products
                    </button>
                    <?= Html::a(
                        Html::tag('i', ' CSV file sample', ['class' => 'fa fa-download']),
                        Yii::$app->urlManagerBackend->createAbsoluteUrl(['/web/uploads/product.csv']),
                        ['class' => 'btn btn-default']
                    ); ?>
                    <!-- Modal HTML Markup -->
                    <div id="ModalUploadCsvForm" class="modal fade">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title">Refresh products list</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Do you want to refresh your product list?</p>
                                    <?= $form->field($model, 'file')->fileInput()->label(false) ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                                    <?= Html::submitButton('Refresh', ['class' => 'btn btn-success']) ?>
                                </div>
                                <?php ActiveForm::end(); ?>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                </div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'hover' => true, //the grid table will highlight row on hover
                        'persistResize' => true, //to store resized column state using local storage persistence
                        'options' => ['id' => 'product-list'],
                        'pjax' => true,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '60'],
                            ],
                            [
                                'attribute' => 'externalId',
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '100'],
                            ],
                            [
                                'attribute' => 'title',
                                'hAlign' => GridView::ALIGN_LEFT,
                            ],
                            [
                                'label' => 'Price and Options',
                                'attribute' => 'options',
                                'format' => 'raw',
                                'mergeHeader' => true,
                                'value' => function (Product $model) {
                                    return $model->getProductOptionsInHTML();
                                },
                                'vAlign' => GridView::ALIGN_TOP,
                            ],
                            [
                                'attribute' => 'photo',
                                'format' => ['image', ['width' => '75']],
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '100'],
                                'mergeHeader' => true,
                            ],
                            [
                                'attribute' => 'link',
                                'headerOptions' => ['width' => '120'],
                                'format' => ['url', ['target' => '_blank']]
                            ]
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