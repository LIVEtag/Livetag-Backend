<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac;

use Yii;
use yii\base\Module as BaseModule;
use yii\helpers\Inflector;
use yii\i18n\PhpMessageSource;

/**
 * GUI manager for RBAC
 *
 * Use [[\yii\base\Module::$controllerMap]] to change property of controller.
 * To change listed menu, use property [[$menus]].
 *
 * ~~~
 * 'layout' => 'left-menu', // default to null mean use application layout.
 * 'controllerMap' => [
 *     'assignment' => [
 *         'class' => 'mdm\admin\controllers\AssignmentController',
 *         'userClassName' => 'app\models\User',
 *         'idField' => 'id'
 *     ]
 * ],
 * 'menus' => [
 *     'assignment' => [
 *         'label' => 'Grand Access' // change label
 *     ],
 *     'route' => null, // disable menu
 * ],
 * ~~~
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'assignment';

    /**
     * @var array
     * @see [[items]]
     */
    private $menus = [];

    /**
     * @var array
     * @see [[items]]
     */
    private $coreItems = [
        'assignment' => 'Assignments',
        'role' => 'Roles',
        'permission' => 'Permissions',
        'route' => 'Routes',
        'rule' => 'Rules',
        'menu' => 'Menus',
    ];

    /**
     * @var array
     * @see [[items]]
     */
    private $normalizeMenus;

    /**
     * Main layout using for module. Default to layout of parent module
     * Its used when `layout` set to 'left-menu', 'right-menu' or 'top-menu'
     *
     * @var string
     */
    public $mainLayout = '@rbac/views/layouts/main.php';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::configure($this, require(__DIR__ . '/config/main.php'));

        $this->registerTranslations();
    }

    /**
     * Get avalible menu
     *
     * @return array
     */
    public function getMenus()
    {
        if ($this->normalizeMenus !== null) {
            return $this->normalizeMenus;
        }

        $mid = '/' . $this->getUniqueId() . '/';
        // resolve core menus
        $this->normalizeMenus = [];
        foreach ($this->coreItems as $id => $lable) {
            $this->normalizeMenus[$id] = [
                'label' => Yii::t('rbac-backend', $lable),
                'url' => [$mid . $id]
            ];
        }

        foreach (array_keys($this->controllerMap) as $id) {
            $this->normalizeMenus[$id] = [
                'label' => Yii::t('rbac-backend', Inflector::humanize($id)),
                'url' => [$mid . $id]
            ];
        }

        // user configure menus
        foreach ($this->menus as $id => $value) {
            if (empty($value)) {
                unset($this->normalizeMenus[$id]);
            } else {
                if (!is_array($value)) {
                    $value = ['label' => $value];
                }
                $this->normalizeMenus[$id] = isset($this->normalizeMenus[$id])
                    ? array_merge($this->normalizeMenus[$id], $value)
                    : $value;
                if (!isset($this->normalizeMenus[$id]['url'])) {
                    $this->normalizeMenus[$id]['url'] = [$mid . $id];
                }
            }
        }

        return $this->normalizeMenus;
    }

    /**
     * Set or add available menu
     *
     * @param array $menus
     */
    public function setMenus($menus)
    {
        $this->menus = array_merge($this->menus, $menus);
        $this->normalizeMenus = null;
    }

    /**
     * RegisterTranslations
     */
    private function registerTranslations()
    {
        Yii::$app->i18n->translations['rbac-backend'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'basePath' => '@rbac/messages'
        ];
    }
}
