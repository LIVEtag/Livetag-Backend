<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\Shop\Shop;
use kartik\widgets\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Shop */
/* @var $form ActiveForm */
$user = Yii::$app->user->identity;
?>

<div class="shop-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header">
                </div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?php if ($user->isAdmin) : ?>
                        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'field form-control']) ?>
                        <?= $form->field($model, 'uri')->textInput(['maxlength' => true, 'class' => 'field form-control']) ?>
                        <?= $form->field($model, 'website')->textInput(['maxlength' => true, 'class' => 'field form-control']) ?>
                    <?php endif; ?>


                    <div class="select-wrapper">
                        <?= $form->field($model, 'iconsTheme')->dropDownList(Shop::ICONS_THEMES, ['class' => 'field form-control']); ?>
                    </div>
                    <div class="form-group">
                        <label>Icons color themes variants</label>
                        <div>
                            <?= Html::img(Yii::getAlias('@web') . '/images/iconsThemes.svg'); ?>
                        </div>
                    </div>

                    <div class="select-wrapper">
                    <?= $form->field($model, 'productIcon')->dropDownList(Shop::PRODUCT_ICONS, ['class' => 'field form-control']); ?>
                    </div>
                    <div class="form-group">
                        <label>Product icon options</label>
                        <div class="product-icons">
                            <?= Html::img(Yii::getAlias('@web') . '/images/productIcons.svg'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-default">
                <div class="box-header">
                </div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= $form->field($model, 'file')->widget(FileInput::class, [
                        'options' => [
                            'multiple' => false,
                        ],
                        'pluginOptions' => [
                            'initialPreview' => $model->logo ? [$model->getUrl()] : [],
                            'initialPreviewConfig' => [
                                [
                                    'caption' => $model->logo,
                                    'showRemove' => false,
                                    'showZoom' => true,
                                    'showDrag' => false,
                                ],
                            ],
                            'initialPreviewAsData' => true,
                            'maxFileCount' => 1,
                            'showUpload' => false,
                            'browseClass' => 'button button--dark',
                            'browseIcon' => '',
                            'browseLabel' => Yii::t('app', 'Browse'),
                            'showRemove' => false,
                            'msgPlaceholder' => 'Add logo',
                            'maxFileSize' => (Yii::$app->params['maxUploadLogoSize'] / 1024), // the maximum file size for upload in KB
                        ],
                    ])->label('Logo'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-footer">
                    <div class="form-group">
                        <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer ?: [$user->isAdmin ? 'index' : 'my'], ['class' => 'button button--dark button--ghost button--upper button--lg']) ?>
                        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'button button--dark button--upper button--lg']) ?>
                    </div>
                </div>
                <!--/.box-footer -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <?php ActiveForm::end(); ?>
</div>
