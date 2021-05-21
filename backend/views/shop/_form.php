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
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <?php $form = ActiveForm::begin(); ?>
                <div class="box-header">
                </div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?php if ($user->isAdmin) : ?>
                        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'uri')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'website')->textInput(['maxlength' => true]) ?>
                    <?php endif; ?>
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
                            'showRemove' => false,
                            'msgPlaceholder' => 'Add logo',
                            'maxFileSize' => (Yii::$app->params['maxUploadLogoSize'] / 1024), // the maximum file size for upload in KB
                        ],
                    ])->label('Logo'); ?>

                    <?= $form->field($model, 'iconsTheme')->dropDownList(Shop::ICONS_THEMES); ?>
                    <div class="form-group">
                        <label>Icons color themes variants</label>
                        <div>
                            <?= Html::img(Yii::getAlias('@web') . '/images/iconsThemes.svg'); ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'productIcon')->dropDownList(Shop::PRODUCT_ICONS); ?>
                    <div class="form-group">
                        <label>Product icon options</label>
                        <div class="product-icons">
                            <?= Html::img(Yii::getAlias('@web') . '/images/productIcons.svg'); ?>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="form-group">
                        <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer ?: [$user->isAdmin ? 'index' : 'my'], ['class' => 'btn bg-black']) ?>
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
