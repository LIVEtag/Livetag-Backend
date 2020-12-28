<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator backend\generators\crud\Generator */
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

/* @var $this yii\web\View */
/* @var $model <?= ltrim($class, '\\') ?> */

$this->title = <?= $generator->generateString('Create ' . Inflector::camel2words(StringHelper::basename($class))) ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($class)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="<?= Inflector::camel2id(StringHelper::basename($class)) ?>-create">
    <?= "<?= " ?>$this->render('_form', [
        'model' => $model,
    ]) ?>
</section>
