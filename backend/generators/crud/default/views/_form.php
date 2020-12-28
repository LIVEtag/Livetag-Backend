<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator backend\generators\crud\Generator */
/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$class = $generator->backendModelClass ?: $generator->modelClass;
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
$year = date('Y');
echo <<<EOF
/**
 * Copyright © $year GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */\n
EOF;
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use <?= ltrim($class, '\\') ?>;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($class, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($class)) ?>-form">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <?= "<?php " ?>$form = ActiveForm::begin(); ?>
                <div class="box-header">
                    <?= "<?= " ?>Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-primary']) ?>
                </div>
                <!--/.box-header -->
                <div class="box-body table-responsive">

                    <?php foreach ($generator->getColumnNames() as $attribute) {
                        if (in_array($attribute, $safeAttributes)) {
                            echo "<?= " . $generator->generateActiveField($attribute) . " ?>\n                        ";
                        }
                    } ?>

                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="form-group">
                        <?= "<?= " ?>Html::submitButton(<?= $generator->generateString('Save') ?>, ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
                <!--/.box-footer -->
                <?= "<?php " ?>ActiveForm::end(); ?>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
</div>
