<?php

use common\models\forms\User\UserProfileForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model UserProfileForm */

$this->title = Yii::t('app', 'Change Name');

?>

<section class="change-name">
    <div class="change-name-form">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="box-header"></div>
                    <!--/.box-header -->
                    <div class="box-body table-responsive">
                        <?= $form->field($model, 'name')->textInput([
                            'placeholder' => Yii::t('app', 'Current name'),
                            'maxlength' => true,
                            'class' => 'field form-control',
                        ]) ?>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="form-group">
                            <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer ?: Yii::$app->homeUrl, ['class' => 'button button--dark button--ghost button--upper button--lg']) ?>
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

