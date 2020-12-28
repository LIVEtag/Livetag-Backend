<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator backend\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$class = $generator->backendModelClass ?: $generator->modelClass;

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

/* @var $this yii\web\View */
/* @var $model <?= ltrim($class, '\\') ?> */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($class)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="<?= Inflector::camel2id(StringHelper::basename($class)) ?>-view">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <?= "<?= " ?>Html::a(<?= $generator->generateString('Update') ?>, ['update', <?= $urlParams ?>], ['class' => 'btn btn-primary']) ?>
                    <?= "<?= " ?>Html::a(<?= $generator->generateString('Delete') ?>, ['delete', <?= $urlParams ?>], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => <?= $generator->generateString('Are you sure you want to delete this item?') ?>,
                            'method' => 'post',
                        ],
                    ]) ?>
                </div>
                <!--/.box-header -->
                <div class="box-body">
                    <?= "<?= " ?>DetailView::widget([
                        'model' => $model,
                        'attributes' => [
<?php
                    if (($tableSchema = $generator->getTableSchema()) === false) {
                        foreach ($generator->getColumnNames() as $name) {
                            echo "                            '" . $name . "',\n";
                        }
                    } else {
                        foreach ($generator->getTableSchema()->columns as $column) {
                            $format = stripos($column->name, 'createdAt') !== false || stripos($column->name, 'updatedAt') !== false ? 'datetime' : $generator->generateColumnFormat($column);
                            echo "                            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                        }
                    }
?>
                        ],
                    ]) ?>
                </div>
                <!-- /.box-body -->
                <div class="box-footer"></div>
                <!--/.box-footer -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
 <!-- /.section -->