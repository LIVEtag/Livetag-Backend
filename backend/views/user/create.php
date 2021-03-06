<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Shop\Shop;
use backend\models\User\User;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model User */
/* @var $form ActiveForm */

$this->title = Yii::t('app', 'Add a seller');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sellers management'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="user-create">
    <div class="user-form">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="box-header"></div>
                    <!--/.box-header -->
                    <div class="box-body table-responsive">

                        <?= $form->field($model, 'email')->textInput(['class' => 'field form-control']) ?>

                        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'field form-control']) ?>

                        <?= $form->field($model, 'shopId')->widget(Select2::class, [
                            'data' => Shop::getIndexedArray(),
                            'options' => [
                                'placeholder' => 'Select shop ...'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]); ?>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="form-group">
                            <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer ?: ['index'], ['class' => 'button button--dark button--ghost button--upper button--lg']) ?>
                            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'button button--dark button--upper button--lg']) ?>
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
