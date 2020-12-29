<?php
use backend\models\User\User;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
/* @var $this View */
/* @var $model User */

$this->title = Yii::t('app', 'Change Password');
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="user-update">
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
                        <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>
                        <?= $form->field($model, 'newPassword')->passwordInput(['autofocus' => true]) ?>
                        <?= $form->field($model, 'confirmPassword')->passwordInput(['autofocus' => true]) ?>
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
