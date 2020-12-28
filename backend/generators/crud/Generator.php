<?php
namespace backend\generators\crud;

use Yii;
use yii\base\Model;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\gii\generators\crud\Generator as CrudGenerator;
use yii\helpers\StringHelper;

/**
 * @inheritdoc
 */
class Generator extends CrudGenerator
{
    public $enableI18N = true;
    public $enablePjax = true;

    /**
     * If check - generate backend model based on common modelClass
     * @var bool
     */
    public $generateBackendModel = true;

    /**
     * Generated backend model class
     * @var string
     */
    public $backendModelClass;
    public $baseControllerClass = 'backend\components\Controller';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['generateBackendModel'], 'boolean'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'generateBackendModel' => 'Generate Backend Model',
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'generateBackendModel' => 'If the flag is set, a class will be generated in the backend application, which will inherit "Model Class".
               Namespace will be generated from controller name',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        if ($this->generateBackendModel) {
            $modelClass = StringHelper::basename($this->modelClass);
            $namespace = str_replace('Controller', '', StringHelper::basename($this->controllerClass));
            //set backend model class (to override use section in general files)
            $this->backendModelClass = 'backend\models\\' . $namespace . '\\' . $modelClass;
        }
        //generate original files
        $files = parent::generate();
        //generate backend model file (if required)
        if ($this->generateBackendModel) {
            $backendModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->backendModelClass, '\\') . '.php'));
            $files[] = new CodeFile($backendModel, $this->render('backendModel.php', ['namespace' => $namespace]));
        }
        return $files;
    }

    /**
     * @inheritdoc)
     */
    public function generateSearchConditions()
    {
        $columns = [];
        if (($table = $this->getTableSchema()) === false) {
            $class = $this->modelClass;
            /* @var $model Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                $columns[$column->name] = $column->type;
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_TINYINT:
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "self::tableName() . '.{$column}' => \$this->{$column},";
                    break;
                default:
                    $likeKeyword = $this->getClassDbDriverName() === 'pgsql' ? 'ilike' : 'like';
                    $likeConditions[] = "->andFilterWhere(['{$likeKeyword}', self::tableName() . '.{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n"
                . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

    /**
     * An inline validator that checks if the attribute value refers to a valid namespaced class name.
     * The validator DO NOT check if the directory containing the new class file exist or not.
     * @param string $attribute the attribute being validated
     * @param array $params the validation options
     */
    public function validateNewClass($attribute, $params)
    {
        $class = ltrim($this->$attribute, '\\');
        if (($pos = strrpos($class, '\\')) === false) {
            $this->addError($attribute, "The class name must contain fully qualified namespace name.");
        } else {
            $ns = substr($class, 0, $pos);
            $path = Yii::getAlias('@' . str_replace('\\', '/', $ns), false);
            if ($path === false) {
                $this->addError($attribute, "The class namespace is invalid: $ns");
            }
        }
    }


}
