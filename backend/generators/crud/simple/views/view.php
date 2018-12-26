<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

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
use yii\widgets\DetailView;
use yii\web\View;

/* @var $this View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => \<?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view box box-primary">
    <div class="box-header">
        <?= "<?= " ?>Html::a(\<?= $generator->generateString('Update') ?>, ['update', <?= $urlParams ?>], ['class' => 'btn btn-primary btn-flat']) ?>
        <?= "<?= " ?>Html::a(\<?= $generator->generateString('Delete') ?>, ['delete', <?= $urlParams ?>], [
            'class' => 'btn btn-danger btn-flat',
            'data' => [
                'confirm' => \<?= $generator->generateString('Are you sure you want to delete this item?') ?>,
                'method' => 'post',
            ],
        ]) ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?= "<?= " ?>DetailView::widget([
            'model' => $model,
            'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "                '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = stripos($column->name, 'createdAt') !== false || stripos($column->name, 'updatedAt') !== false ? 'datetime' : $generator->generateColumnFormat($column);
        echo "                '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
    }
}
?>
            ],
        ]) ?>
    </div>
</div>
