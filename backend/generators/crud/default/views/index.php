<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator backend\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
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
use <?= $generator->indexWidgetType === 'grid' ? "kartik\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
use kartik\grid\ActionColumn;
<?= ($generator->enablePjax && $generator->indexWidgetType !== 'grid') ? 'use yii\widgets\Pjax;' : '' ?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($class)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="<?= Inflector::camel2id(StringHelper::basename($class)) ?>-index">
<?= ($generator->enablePjax && $generator->indexWidgetType !== 'grid') ? "    <?php Pjax::begin(); ?>\n" : '' ?>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <?= "<?= " ?>Html::a(<?= $generator->generateString('Create ' . Inflector::camel2words(StringHelper::basename($class))) ?>, ['create'], ['class' => 'btn bg-black']) ?>
                </div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
<?php if ($generator->indexWidgetType === 'grid') : ?>
                    <?= "<?= " ?>GridView::widget([
                        'dataProvider' => $dataProvider,
                        'hover' => true, //the grid table will highlight row on hover
                        'persistResize' => true, //to store resized column state using local storage persistence
                        'options' => ['id' => '<?= Inflector::camel2id(StringHelper::basename($class)) ?>-list'],
<?php if ($generator->enablePjax): ?>
                        'pjax' => true,
<?php endif; ?>
                        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n                        'columns' => [\n" : "'columns' => [\n"; ?>
<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "                            '" . $name . "',\n";
        } else {
            echo "                            //'" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "                            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "                            //'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
                            [
                                'class' => ActionColumn::class,
                                'vAlign' => GridView::ALIGN_TOP,
                            ],
                        ],
                    ]); ?>
<?php else : ?>
                    <?= "<?= " ?>ListView::widget([
                        'dataProvider' => $dataProvider,
                        'itemOptions' => ['class' => 'item'],
                        'itemView' => function ($model, $key, $index, $widget) {
                            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
                        },
                    ]) ?>
<?php endif; ?>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
<?= ($generator->enablePjax && $generator->indexWidgetType !== 'grid') ? "    <?php Pjax::end(); ?>\n" : '' ?>
</section>