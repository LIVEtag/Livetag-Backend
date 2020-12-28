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
/* @var $this yii\web\View */
/* @var $model <?= ltrim($class, '\\') ?> */

$this->title = <?= strtr($generator->generateString('Update ' .
    Inflector::camel2words(StringHelper::basename($class)) .
    ': {nameAttribute}', ['nameAttribute' => '{nameAttribute}']), [
    '\'{nameAttribute}\'' => '$model->' . $generator->getNameAttribute()
]) ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($class)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('Update') ?>;
?>
<section class="<?= Inflector::camel2id(StringHelper::basename($class)) ?>-update">
    <?= '<?= ' ?>$this->render('_form', [
        'model' => $model,
    ]) ?>
</section>
