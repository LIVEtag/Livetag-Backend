<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Product\Product;
use kartik\widgets\FileInput;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\web\View;
use backend\models\Product\ProductForm;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model ProductForm */

$productMedias = $model->product->productMedias;
$imageFiles = [];
$previewConfig = [];
foreach ($productMedias as $productMedia) {
    $imageFiles[] = $productMedia->getUrl();
    $previewConfig[] = [
        'caption' => $productMedia->getOriginName(),
        'size' => $productMedia->getSize(),
        'showRemove' => false,
        'showZoom' => true,
        'showDrag' => false,
    ];
}
?>

<div class="product-form">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
                <div class="box-header"></div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= $form->field($model, 'externalId')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items', // required: css class selector
                        'widgetItem' => '.item', // required: css class
                        'min' => 1,
                        'insertButton' => '.add-item', // css class
                        'deleteButton' => '.remove-item', // css class
                        'model' => $model->productOptions[0],
                        'formId' => 'dynamic-form',
                        'formFields' => [
                            'sku',
                            'price',
                            'option',
                        ],

                    ]); ?>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <button type="button" class="pull-right add-item btn btn-success btn-xs"><i
                                        class="fa fa-plus"></i> Add option
                            </button>
                            <div class="clearfix"></div>
                        </div>

                        <div class="panel-body container-items"><!-- widgetContainer -->
                            <?php foreach ($model->productOptions as $index => $modelOption) : ?>
                                <div class="item panel panel-default"><!-- widgetBody -->
                                    <div class="panel-heading">
                                        <button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i
                                                    class="fa fa-minus"></i></button>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?= $form->field($modelOption, "[{$index}]sku")->textInput([
                                                    'placeholder' => Yii::t('app', 'SKU of the product/product option'),
                                                    'maxlength' => true,
                                                ]) ?>
                                            </div>

                                            <div class="col-sm-6">
                                                <?= $form->field($modelOption, "[{$index}]price")->textInput([
                                                    'placeholder' => Yii::t('app', 'Price of the product/product option'),
                                                    'maxlength' => true,
                                                ]) ?>
                                            </div>
                                        </div><!-- end:row -->

                                        <?= $form->field($modelOption, "[{$index}]option")->textInput([
                                            'placeholder' => Yii::t('app', 'Version, size, color, material, length, weight, pack etc.'),
                                            'maxlength' => true,
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php DynamicFormWidget::end(); ?>

                    <?= $form->field($model, 'files[]')->widget(FileInput::class, [
                        'options' => [
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'initialPreview' => $imageFiles,
                            'initialPreviewConfig' => $previewConfig,
                            'initialPreviewAsData' => true,
                            'maxFileCount' => Product::MAX_NUMBER_OF_IMAGES,
                            'showUpload' => false,
                            'showRemove' => false,
                            'overwriteInitial' => true,
                            'msgPlaceholder' => Yii::t('app', 'The photo(s) of the product, if any'),
                            'maxFileSize' => (Yii::$app->params['maxUploadImageSize'] / 1024), // the maximum file size for upload in KB
                        ],
                    ]); ?>

                    <?= $form->field($model, 'link')->textInput([
                        'maxlength' => true,
                        'placeholder' => Yii::t('app', 'The URL link to the product on the shopping site'),
                    ]) ?>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="form-group">

                        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn bg-black']) ?>
                        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
                <!--/.box-footer -->
                <?php ActiveForm::end(); ?>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
</div>

