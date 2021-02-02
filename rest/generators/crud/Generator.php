<?php

namespace rest\generators\crud;

use Yii;
use yii\base\ErrorException;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\gii\CodeFile;
use yii\helpers\BaseInflector;
use yii\helpers\Json;

class Generator extends \yii\gii\Generator
{
    public $db = 'db';
    public $modelClass;
    public $controllerClass;
    public $changeUrlManager = true;
    public $changeSwagger = true;
    public $swaggerPath = '@rest/modules/swagger/config/swagger.json';
    public $actionCreate = true;
    public $actionCreateAuthenticatorExcept = false;
    public $actionCreateRules = '@';
    public $actionView = true;
    public $actionViewAuthenticatorExcept = false;
    public $actionViewRules = '@';
    public $actionUpdate = true;
    public $actionUpdateAuthenticatorExcept = false;
    public $actionUpdateRules = '@';
    public $actionIndex = true;
    public $actionIndexAuthenticatorExcept = false;
    public $actionIndexRules = '@';
    public $actionDelete = true;
    public $actionDeleteAuthenticatorExcept = false;
    public $actionDeleteRules = '@';

    public function getName()
    {
        return 'REST CRUD Generator';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'This generator generates CRUD action REST customization.';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [
                ['controllerClass', 'modelClass'], 'filter',
                'filter' => 'trim'
            ],
            [['modelClass', 'controllerClass'], 'required'],
            [
                [
                    'actionCreateRules', 'actionDeleteRules',
                    'actionViewRules', 'actionUpdateRules', 'actionIndexRules'
                ], 'safe'
            ],

            [
                ['modelClass', 'controllerClass'], 'match',
                'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'
            ],
            [['modelClass'], 'validateClass', 'params' => ['extends' => BaseActiveRecord::className()]],
            [
                ['controllerClass'], 'match', 'pattern' => '/Controller$/',
                'message' => 'Controller class name must be suffixed with "Controller".'
            ],
            [
                ['controllerClass'], 'match', 'pattern' => '/(^|\\\\)[A-Z][^\\\\]+Controller$/',
                'message' => 'Controller class name must start with an uppercase letter.'
            ],
            [['controllerClass'], 'validateNewClass'],
            [['modelClass'], 'validateModelClass'],
            [
                [
                    'enableI18N', 'changeUrlManager', 'actionCreate', 'actionDelete', 'actionView',
                    'actionUpdate',
                    'actionIndex', 'actionCreateAuthenticatorExcept', 'actionDeleteAuthenticatorExcept',
                    'actionViewAuthenticatorExcept', 'actionUpdateAuthenticatorExcept',
                    'actionIndexAuthenticatorExcept','changeSwagger'
                ], 'boolean'
            ],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'modelClass' => 'Model Class',
            'controllerClass' => 'Controller Class',
            'changeUrlManager' => 'Add rules to UrlManager Component',
            'changeSwagger' => 'Change swagger.json',
            'actionViewAuthenticatorExcept' => 'Authenticator Except',
            'actionIndexAuthenticatorExcept' => 'Authenticator Except',
            'actionUpdateAuthenticatorExcept' => 'Authenticator Except',
            'actionDeleteAuthenticatorExcept' => 'Authenticator Except',
            'actionCreateAuthenticatorExcept' => 'Authenticator Except',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'modelClass' => 'This is the ActiveRecord class associated with the table that CRUD will be built upon.
                You should provide a fully qualified class name, e.g., <code>app\models\Post</code>.',
            'controllerClass' => 'This is the name of the controller class to be generated. You should
                provide a fully qualified namespaced class (e.g. <code>app\controllers\PostController</code>),
                and class name should be in CamelCase with an uppercase first letter. Make sure the class
                is using the same namespace as specified by your application\'s controllerNamespace property.',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['controller.php'];
    }

    /**
     * Checks if model class is valid
     */
    public function validateModelClass()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pk = $class::primaryKey();
        if (empty($pk)) {
            $this->addError('modelClass', "The table associated with $class must have primary key(s).");
        }
    }

    /**
     * @return array|CodeFile[]
     * @throws ErrorException
     */
    public function generate()
    {
        $controllerFile = $this->getControllerFile();

        $files = [
            new CodeFile($controllerFile, $this->render('controller.php')),
        ];
        if ($this->changeSwagger) {
            $swaggerPath = Yii::getAlias($this->swaggerPath);
            $oldSwaggerJson = file_get_contents($swaggerPath);
            $oldSwagger = Json::decode($oldSwaggerJson);
            $swaggerConf = $this->swaggerUpdate($oldSwagger);
            $newSwaggerJson = Json::encode(
                $swaggerConf,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
            $files[] = new CodeFile(
                $swaggerPath,
                $newSwaggerJson
            );
        }
        if ($this->changeUrlManager) {
            $files[] = $this->getCodeFileUrlManager();
        }
        return $files;
    }

    /**
     * @return string
     */
    public function getControllerFile()
    {
        $path = '@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php';
        return Yii::getAlias($path);
    }

    /**
     * @param $swaggerConf
     * @return mixed
     * @throws ErrorException
     */
    public function swaggerUpdate($swaggerConf)
    {
        $properties = $this->getSwaggerProperties();
        $objectName = lcfirst($this::getClassName($this->modelClass));
        if (!isset($swaggerConf['components']['schemas'][$objectName])) {
            $swaggerConf['components']['schemas'][$objectName] = [
                'type' => 'object',
                'properties' => $properties
            ];
        }

        $urlPath = $this->getUrlPath();
        if ($this->actionIndex) {
            $swaggerConf['paths']['/' . $urlPath]['get'] = $this->getSwaggerPathIndex($objectName);
        }
        if ($this->actionCreate) {
            $swaggerConf['paths']['/' . $urlPath]['post'] = $this->getSwaggerCreate($objectName, $properties);
        }
        if ($this->actionDelete) {
            $swaggerConf['paths']['/' . $urlPath . '/{id}']['delete'] = $this->getSwaggerDelete($objectName);
        }
        if ($this->actionView) {
            $swaggerConf['paths']['/' . $urlPath . '/{id}']['get'] = $this->getSwaggerView($objectName);
        }
        if ($this->actionUpdate) {
            $swaggerConf['paths']['/' . $urlPath . '/{id}']['put'] = $this->getSwaggerUpdate($objectName, $properties);
        }
        return $swaggerConf;
    }

    /***
     * @return array
     */
    public function getSwaggerProperties()
    {
        $tableSchema = $this->modelClass::getTableSchema();
        $properties = [];
        foreach ($tableSchema->columns as $column) {
            unset($type);
            $columnPhpType = $column->phpType;
            if ($columnPhpType === 'integer') {
                $type = 'integer';
            } elseif ($columnPhpType === 'boolean') {
                $type = 'bool';
            } elseif ($columnPhpType === 'float') {
                $type = $columnPhpType;
            } elseif ($columnPhpType === 'string') {
                $type = $columnPhpType;
            }
            if (!empty($type)) {
                $properties[$column->name] = [
                    'type' => $type,
                ];
            }
        }

        return $properties;
    }

    public static function getClassName($class)
    {
        $namespace = explode('\\', $class);
        return end($namespace);
    }

    /**
     * @return string
     * @throws ErrorException
     */
    public function getUrlPath()
    {
        $patternPath = '|rest\\\modules\\\(.*)\\\controllers\\\(.*)Controller|is';
        preg_match($patternPath, $this->controllerClass, $matches);
        if (empty($matches[1])) {
            throw new ErrorException(
                sprintf('Change the configuration file for the pattern " %s " ', $patternPath)
            );
        }
        return str_replace('\\', '/', $matches[1]) . '/' . BaseInflector::camel2id($matches[2]);
    }

    private function getSwaggerPathIndex($objectName)
    {
        return [
            'tags' => [
                ucfirst($objectName)
            ],
            'summary' => 'List ' . $objectName,
            'operationId' => $objectName . 'Index',
            'parameters' => [],
            'responses' => [
                200 =>
                    [
                        'description' => 'OK',
                        'content' =>
                            [
                                'application/json' =>
                                    [
                                        'schema' =>
                                            [
                                                'allOf' =>
                                                    [
                                                        0 =>
                                                            [
                                                                '$ref' => '#/components/schemas/status200',
                                                            ],
                                                        1 =>
                                                            [
                                                                'properties' =>
                                                                    [
                                                                        'result' =>
                                                                            [
                                                                                '$ref' => '#/components/schemas/' .
                                                                                    $objectName,
                                                                            ],
                                                                    ],
                                                            ],
                                                    ],
                                            ],
                                    ],
                            ],
                    ],
                401 =>
                    [
                        '$ref' => '#/components/responses/401',
                    ],
            ]
        ];
    }

    private function getSwaggerCreate($objectName, $properties)
    {
        return [
            'tags' => [
                ucfirst($objectName)
            ],
            'summary' => 'Create new ' . $objectName,
            'operationId' => $objectName . 'CreateNew',
            'responses' =>
                [
                    200 =>
                        [
                            'description' => 'OK',
                            'content' =>
                                [
                                    'application/json' =>
                                        [
                                            'schema' =>
                                                [
                                                    'allOf' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    '$ref' => '#/components/schemas/status200',
                                                                ],
                                                            1 =>
                                                                [
                                                                    'properties' =>
                                                                        [
                                                                            'result' =>
                                                                                [
                                                                                    '$ref' => '#/components/schemas/' .
                                                                                        $objectName,
                                                                                ],
                                                                        ],
                                                                ],
                                                        ],
                                                ],
                                        ],
                                ],
                        ],
                    401 =>
                        [
                            '$ref' => '#/components/responses/401',
                        ],
                    403 =>
                        [
                            '$ref' => '#/components/responses/403',
                        ],
                    422 =>
                        [
                           '$ref' => '#/components/responses/422',
                        ],
                ],
            'requestBody' =>
                [
                    'content' =>
                        [
                            'application/x-www-form-urlencoded' =>
                                [
                                    'schema' =>
                                        [
                                            'type' => 'object',
                                            'properties' => $properties,
                                        ],
                                ],
                        ],
                ],
        ];
    }

    private function getSwaggerDelete($objectName)
    {
        return [
            'tags' => [
                ucfirst($objectName)
            ],
            'summary' => 'Remove ' . $objectName,
            'operationId' => 'Remove ' . $objectName,
            'parameters' =>
                [
                    0 =>
                        [
                            'in' => 'path',
                            'name' => 'id',
                            'description' => $objectName . ' id',
                            'required' => true,
                            'schema' =>
                                [
                                    'type' => 'integer',
                                ],
                        ],
                ],
            'responses' =>
                [
                    204 =>
                        [
                            'description' => 'No Response',
                            'content' =>
                                [
                                    'application/json' =>
                                        [
                                            'schema' =>
                                                [
                                                    'allOf' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    '$ref' => '#/components/responses/status204',
                                                                ],
                                                        ],
                                                ],
                                        ],
                                ],
                        ],
                    401 =>
                        [
                            '$ref' => '#/components/responses/401',
                        ],
                    403 =>
                        [
                            '$ref' => '#/components/responses/403',
                        ],
                ],
        ];
    }

    private function getSwaggerView($objectName)
    {
        return [
            'tags' => [
                ucfirst($objectName)
            ],
            'summary' => 'View ' . $objectName,
            'operationId' => $objectName . 'View',
            'parameters' =>
                [
                    0 =>
                        [
                            'in' => 'path',
                            'name' => 'id',
                            'description' => 'Id ' . $objectName,
                            'required' => true,
                            'schema' =>
                                [
                                    'type' => 'string',
                                ],
                        ],
                ],
            'responses' =>
                [
                    200 => [
                        'description' => 'OK',
                        'content' =>
                            [
                                'application/json' =>
                                    [
                                        'schema' =>
                                            [
                                                'allOf' =>
                                                    [
                                                        0 =>
                                                            [
                                                                '$ref' => '#/components/schemas/status200',
                                                            ],
                                                        1 =>
                                                            [
                                                                'properties' =>
                                                                    [
                                                                        'result' =>
                                                                            [
                                                                                '$ref' => '#/components/schemas/' .
                                                                                    $objectName,
                                                                            ],
                                                                    ],
                                                            ],
                                                    ],
                                            ],
                                    ],
                            ],
                    ],
                    401 =>
                        [
                            '$ref' => '#/components/responses/401',
                        ],
                ],

        ];
    }

    private function getSwaggerUpdate($objectName, $properties)
    {
        return [
            'tags' => [
                ucfirst($objectName)
            ],
            'summary' => 'Update ' . $objectName,
            'operationId' => $objectName . 'Update',
            'parameters' =>
                [
                    0 =>
                        [
                            'in' => 'path',
                            'name' => 'id',
                            'description' => $objectName . ' id',
                            'required' => true,
                            'schema' =>
                                [
                                    'type' => 'integer',
                                ],
                        ],
                ],
            'responses' =>
                [
                    200 => [
                        'description' => 'OK',
                        'content' =>
                            [
                                'application/json' =>
                                    [
                                        'schema' =>
                                            [
                                                'allOf' =>
                                                    [
                                                        0 =>
                                                            [
                                                                '$ref' => '#/components/schemas/status200',
                                                            ],
                                                        1 =>
                                                            [
                                                                'properties' =>
                                                                    [
                                                                        'result' =>
                                                                            [
                                                                                '$ref' => '#/components/schemas/' .
                                                                                    $objectName,
                                                                            ],
                                                                    ],
                                                            ],
                                                    ],
                                            ],
                                    ],
                            ],
                    ],
                    401 =>
                        [
                           '$ref' => '#/components/responses/401',
                        ],
                    403 =>
                        [
                            '$ref' => '#/components/responses/403',
                        ],
                    422 =>
                        [
                            '$ref' => '#/components/responses/422',
                        ],
                ],
            'requestBody' =>
                [
                    'content' =>
                        [
                            'application/x-www-form-urlencoded' =>
                                [
                                    'schema' =>
                                        [
                                            'type' => 'object',
                                            'properties' => $properties
                                        ],
                                ],
                        ],
                ],
        ];
    }

    /**
     * @return CodeFile
     * @throws ErrorException
     */
    public function getCodeFileUrlManager(): CodeFile
    {
        $restConfigPath = Yii::getAlias('@rest') . '/config/main.php';
        $restConfig = file_get_contents($restConfigPath);
        $pattern = '|\'urlManager\' => \[\n\s+\'rules\' => \[|is';
        if (preg_match($pattern, $restConfig) !== 1) {
            throw new ErrorException(sprintf('Change the configuration file for the pattern " %s " OR
            disabled setting "%s"', $pattern, $this->getAttributeLabel('changeUrlManager')));
        }

        $urlPath = $this->getUrlPath();
        $newRules = '\'urlManager\' => [
            \'rules\' => [
                [
                    \'class\' => UrlRule::class,
                    \'controller\' => [
                        \'' . $urlPath . '\' => \'' . $urlPath . '\'
                    ]
                ],';
        if (!stristr($restConfig, $newRules)) {
            /**
             * Add Rules config - overwrite
             */
            $newRules = preg_replace($pattern, $newRules, $restConfig);
            return new CodeFile($restConfigPath, $newRules);
        } else {
            /**
             * Unchanged config
             */
            return new CodeFile($restConfigPath, $restConfig);
        }
    }

    /**
     * @return array
     */
    public function getRulesList()
    {
        return ['@' => '@', '?' => '?'];
    }
}
