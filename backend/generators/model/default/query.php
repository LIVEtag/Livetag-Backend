<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

/**
 * @param $path
 * @return mixed
 */
function getShortAqName($path)
{
    $list = explode('\\', $path);
    return array_pop($list);
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
declare(strict_types = 1);

namespace <?= $generator->queryNs ?>;

use <?= ltrim($modelFullClassName, '\\') . ";\n"; ?>
use <?= ltrim($generator->queryBaseClass, '\\') . ";\n"; ?>

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @see <?= getShortAqName($modelFullClassName) . "\n" ?>
 */
class <?= $className ?> extends <?= getShortAqName('\\' . ltrim($generator->queryBaseClass, '\\')) . "\n" ?>
{
    /**
     * @inheritdoc
     * @return <?= getShortAqName($modelFullClassName) ?>[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return <?= getShortAqName($modelFullClassName) ?>|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
