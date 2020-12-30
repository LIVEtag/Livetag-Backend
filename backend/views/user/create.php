<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Shop\Shop;
use backend\models\User\User;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model User */
/* @var $form ActiveForm */

$this->title = Yii::t('app', 'Create User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="user-create">
    <div class="user-form">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="box-header">
                        <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn bg-black']) ?>
                    </div>
                    <!--/.box-header -->
                    <div class="box-body table-responsive">

                        <?= $form->field($model, 'email')->textInput() ?>

                        <?=
                        $form->field($model, 'shopId')->widget(Select2::class, [
                            'data' => $model->shopId ? Shop::getIndexedArray($model->shopId) : ['' => ''],
                            'options' => [
                                'placeholder' => 'Select shop',
                                'class' => 'form-control',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 0,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                ],
                                'ajax' => [
                                    'url' => Url::to(['shop/search']),
                                    'dataType' => 'json',
                                    'delay' => 250,
                                    'cache' => true,
                                ],
                            ],
                        ]); ?>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="form-group">
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
</section>
