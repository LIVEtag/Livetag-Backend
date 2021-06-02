<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Product\Product;
use backend\models\Product\ProductSearch;
use backend\models\Product\ProductsUploadForm;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use kartik\widgets\FileInput;
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
                    <div class="buttons-group">
                        <?= Html::a(
                            Html::tag('span', '', ['class' => 'icon icon-download']) . 'CSV file sample',
                            Yii::$app->urlManagerBackend->createAbsoluteUrl(['/web/uploads/product.csv']),
                            ['class' => 'button button--dark button--ghost button--upper button--lg']
                        ); ?>
                        <button
                                type="button"
                                class="button button--dark button--ghost button--upper button--lg"
                                data-toggle="modal"
                                data-target="#ModalUploadCsvForm"
                            <?= $activeStreamSessionExists ? 'disabled title="' . Yii::t('app', 'You cannot upload products while Live Stream is active') . '"' : ''; ?>  >
                            Upload products
                        </button>
                        <?= Html::a(Yii::t('app', '+ Add a product through the form'), ['create'], ['class' => 'button button--dark button--upper button--lg']) ?>
                    </div>
                    <!-- Modal HTML Markup -->
                    <div id="ModalUploadCsvForm" class="modal fade">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title">Upload products</h4>
                                </div>
                                <div class="modal-body">

                                    <?= $form->field($model, 'file')->widget(FileInput::class, [
                                        'options' => [
                                            'multiple' => false,
                                        ],
                                        'pluginOptions' => [
                                            'maxFileCount' => 1,
                                            'showPreview' => false,
                                            'showUpload' => false,
                                            'browseClass' => 'button button--dark',
                                            'browseIcon' => '',
                                            'showRemove' => false,
                                            'browseLabel' => Yii::t('app', 'Browse'),
                                            'msgPlaceholder' => Yii::t('app', 'Add file'),
                                        ],
                                    ])->label(false); ?>

                                    <?php if ($dataProvider->getTotalCount()) : ?>
                                        <?= $form->field($model, 'type', ['enableClientValidation' => false])->radioList(
                                            ProductsUploadForm::TYPES,
                                            [
                                                'item' => function ($index, $label, $name, $checked, $value) {
                                                    return "<div class='radio-box'><label><div class='radio'><input type='radio' name='$name' value='$value'><div class='radio__custom'></div></div><span class='radio__label'>$label</span></label></div>";
                                                }
                                            ]
                                        )->label(false);
                                        ?>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <div class="buttons-group">
                                        <button type="button" class="button button--dark button--ghost button--upper button--lg" data-dismiss="modal">Cancel</button>
                                        <?= Html::submitButton('Upload', ['class' => 'button button--dark button--upper button--lg']) ?>
                                    </div>
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
                        'options' => ['id' => 'product-list', 'class' => 'gridview-wrapper'],
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
                                'attribute' => 'description',
                                'hAlign' => GridView::ALIGN_LEFT,
                            ],
                            [
                                'label' => 'Price and options',
                                'attribute' => 'options',
                                'format' => 'raw',
                                'mergeHeader' => true,
                                'value' => function (Product $model) {
                                    return $model->getProductOptionsInHTML();
                                },
                                'vAlign' => GridView::ALIGN_TOP,
                            ],
                            [
                                'attribute' => 'status',
                                'vAlign' => GridView::ALIGN_TOP,
                                'hAlign' => GridView::ALIGN_LEFT,
                                'headerOptions' => ['width' => '100'],
                                'contentOptions' => ['class' => 'status-cell'],
                                'filter' => Html::tag('div', Html::activeDropDownList($searchModel, 'status', Product::STATUSES, ['class' => 'form-control', 'prompt' => '']), ['class' => 'select-wrapper no-label']),
                                'value' => function (Product $model) {
                                    return $model->getStatusName();
                                },
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
                                'contentOptions' => ['class' => 'link-cell'],
                                'headerOptions' => ['width' => '120'],
                                'format' => ['url', ['target' => '_blank']]
                            ],
                            [
                                'class' => ActionColumn::class,
                                'vAlign' => GridView::ALIGN_TOP,
                                'contentOptions' => ['class' => 'action-button-cell'],
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'update' => function ($url) {
                                        return Html::a("<a href='$url' class='action-button button button--darken button--ghost'><span class='icon icon-pen'></span>Edit</a>", $url);
                                    },
                                    'view' => function ($url) {
                                        return Html::a("<a href='$url' class='action-button button button--darken button--ghost'><span class='icon icon-eye'></span>View</a>", $url);
                                    },
                                    'delete' => function ($url) {
                                        return Html::a("<a href='$url' class='action-button button button--link button--icon'><span class='icon icon-trash'></span></a>", $url);
                                    },
                                ]
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