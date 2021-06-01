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
        'caption' => $productMedia->originName,
        'size' => $productMedia->size,
        'showRemove' => false,
        'showZoom' => true,
        'showDrag' => false,
    ];
}
?>

<div class="product-form">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header"></div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= $form->field($model, 'externalId')->textInput(['maxlength' => true, 'class' => 'field form-control']) ?>
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'class' => 'field form-control']) ?>
                    <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'class' => 'field form-control']) ?>

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
                            <button type="button" class="pull-right add-item button button--dark"><i
                                        class="fa fa-plus"></i> Add option
                            </button>
                            <div class="clearfix"></div>
                        </div>

                        <div class="panel-body container-items"><!-- widgetContainer -->
                            <?php foreach ($model->productOptions as $index => $modelOption) : ?>
                                <div class="item panel panel-default panel-options"><!-- widgetBody -->
                                    <div class="panel-body">
                                        <button type="button" class="remove-item button button--link">
                                            <span class="icon icon-trash"></span>
                                        </button>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?= $form->field($modelOption, "[{$index}]sku")->textInput([
                                                    'placeholder' => Yii::t('app', 'SKU of the product/product option'),
                                                    'maxlength' => true,
                                                    'class' => 'field form-control'
                                                ]) ?>
                                            </div>

                                            <div class="col-sm-6">
                                                <?= $form->field($modelOption, "[{$index}]price")->textInput([
                                                    'placeholder' => Yii::t('app', 'Price of the product/product option'),
                                                    'maxlength' => true,
                                                    'class' => 'field form-control'
                                                ]) ?>
                                            </div>
                                        </div><!-- end:row -->

                                        <?= $form->field($modelOption, "[{$index}]option")->textInput([
                                            'placeholder' => Yii::t('app', 'Version, size, color, material, length, weight, pack etc.'),
                                            'maxlength' => true,
                                            'class' => 'field form-control'
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php DynamicFormWidget::end(); ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header"></div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
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
                            'browseClass' => 'button button--dark',
                            'browseIcon' => '',
                            'browseLabel' => Yii::t('app', 'Browse'),
                            'overwriteInitial' => true,
                            'msgPlaceholder' => Yii::t('app', 'The photo(s) of the product, if any'),
                            'maxFileSize' => (Yii::$app->params['maxUploadImageSize'] / 1024), // the maximum file size for upload in KB
                        ],
                    ]); ?>

                    <?= $form->field($model, 'link')->textInput([
                        'maxlength' => true,
                        'placeholder' => Yii::t('app', 'The URL link to the product on the shopping site'),
                        'class' => 'field form-control'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box-footer">
                <div class="form-group">
                    <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'button button--dark button--ghost button--upper button--lg']) ?>
                    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'button button--dark button--upper button--lg']) ?>
                </div>
            </div>
            <!--/.box-footer -->
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

