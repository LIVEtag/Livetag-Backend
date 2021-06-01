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
/** @var User $user */
$user = Yii::$app->user->identity ?? null;
$defaultCancelUrl = $user && $user->isAdmin ? ['user/index'] : ['site/index'];
?>
<section class="user-update">
    <div class="user-form">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="box-header"></div>
                    <!--/.box-header -->
                    <div class="box-body table-responsive">
                        <?= $form->field($model, 'password')->passwordInput(['class' => 'field form-control']) ?>
                        <?= $form->field($model, 'newPassword')->passwordInput(['class' => 'field form-control']) ?>
                        <?= $form->field($model, 'confirmPassword')->passwordInput(['class' => 'field form-control']) ?>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="form-group">
                            <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer ?: $defaultCancelUrl, ['class' => 'button button--dark button--ghost button--upper button--lg']) ?>
                            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'button button--dark button--upper button-lg']) ?>
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
