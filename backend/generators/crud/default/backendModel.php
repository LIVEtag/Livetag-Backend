<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator backend\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
$modelAlias = 'BaseModel';


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

namespace backend\models\<?= $namespace ?>;

use <?= ltrim($generator->modelClass, '\\') . " as " . $modelAlias ?>;

/**
 * Represents the backend version of `<?= $generator->modelClass ?>`.
 */
class <?= $modelClass ?> extends <?= $modelAlias ?>

{

}
