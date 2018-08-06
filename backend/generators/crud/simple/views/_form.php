<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
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
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form box box-primary">
    <?= "<?php " ?>$form = ActiveForm::begin(); ?>
    <div class="box-body table-responsive">

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo "        <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
    }
} ?>
    </div>
    <div class="box-footer">
        <?= "<?= " ?>Html::submitButton(\<?= $generator->generateString('Save') ?>, ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?= "<?php " ?>ActiveForm::end(); ?>
</div>
