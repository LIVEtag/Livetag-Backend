<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

if ($queryClassName) {
    $queryClassFullName = ($generator->ns === $generator->queryNs)
        ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
}

/**
 * @param $path
 * @return mixed
 */
function getShortArName($path)
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

namespace <?= $generator->ns ?>;

use Yii;
use yii\db\ActiveQuery;
use <?= ltrim($generator->baseClass, '\\'); ?>;
<?php if ($queryClassName) : ?>
use <?= ltrim($queryClassFullName, '\\'); ?>;
<?php endif; ?>

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column) : ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)) : ?>
 *
<?php foreach ($relations as $name => $relation) : ?>
 * @property-read <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= getShortArName('\\' . ltrim($generator->baseClass, '\\')) . "\n" ?>
{
<?php if (!empty($relations)) : ?>
<?php foreach ($relations as $name => $relation) : ?>
    /** @see get<?= $name; ?>() */
    const <?= $generator->relationConstant($relation[1])  . " = '" . lcfirst($name) . "';\n" ?>
<?php endforeach; ?>
<?php endif; ?>
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%<?= $generator->generateTableName($tableName) ?>}}';
    }
<?php if ($generator->db !== 'db') : ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return \Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

<?php if ($queryClassName) : ?>
<?php

    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassName ?> the active query used by this AR class.
     */
    public static function find(): <?= $queryClassName."\n" ?>
    {
        return new <?= $queryClassName ?>(get_called_class());
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
<?php foreach ($labels as $name => $label) : ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation) : ?>

    /**
     * @return ActiveQuery
     */
    public function get<?= $name ?>(): ActiveQuery
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
}
