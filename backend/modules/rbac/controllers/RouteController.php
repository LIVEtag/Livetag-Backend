<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\controllers;

use backend\modules\rbac\components\MenuHelper;
use backend\modules\rbac\components\RouteRule;
use Yii;
use \backend\modules\rbac\models\Route;
use backend\components\Controller as BaseController;
use yii\base\Module;
use yii\caching\TagDependency;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use Exception;

/**
 * Class RouteController
 */
class RouteController extends Controller
{
    const CACHE_TAG = 'rbac.backend.route';

    const CACHE_DURATION = 2592000;

    /**
     * Lists all Route models
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Creates a new AuthItem model
     * If creation is successful, the browser will be redirected to the 'view' page
     *
     * @return string
     */
    public function actionCreate()
    {
        $model = new Route;
        if ($model->load(Yii::$app->getRequest()->post())) {
            if ($model->validate()) {
                $routes = preg_split('/\s*,\s*/', trim($model->route), -1, PREG_SPLIT_NO_EMPTY);
                $this->saveNew($routes);
                MenuHelper::invalidate();
                $this->redirect(['index']);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Assign or remove items
     *
     * @return array
     */
    public function actionAssign()
    {
        $post = Yii::$app->getRequest()->post();
        $action = $post['action'];
        $routes = $post['routes'];
        $manager = Yii::$app->getAuthManager();
        $error = [];
        if ($action == 'assign') {
            $this->saveNew($routes);
        } else {
            foreach ($routes as $route) {
                $child = $manager->getPermission($route);
                try {
                    $manager->remove($child);
                } catch (Exception $exc) {
                    $error[] = $exc->getMessage();
                }
            }
        }
        MenuHelper::invalidate();
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;

        return [
            'type' => 'S',
            'errors' => $error,
        ];
    }

    /**
     * Search Route
     *
     * @param string $target
     * @param string $term
     * @param string $refresh
     * @return array
     */
    public function actionSearch($target, $term = '', $refresh = '0')
    {
        if ($refresh == '1') {
            $this->invalidate();
        }
        $result = [];
        $manager = Yii::$app->getAuthManager();

        $exists = array_keys($manager->getPermissions());
        $routes = $this->getAppRoutes();
        if ($target == 'avaliable') {
            foreach ($routes as $route) {
                if (in_array($route, $exists)) {
                    continue;
                }
                if (empty($term) or strpos($route, $term) !== false) {
                    $result[$route] = true;
                }
            }
        } else {
            foreach ($exists as $name) {
                if ($name[0] !== '/') {
                    continue;
                }
                if (empty($term) or strpos($name, $term) !== false) {
                    $r = explode('&', $name);
                    $result[$name] = !empty($r[0]) && in_array($r[0], $routes);
                }
            }
        }

        Yii::$app->response->format = 'json';
        return $result;
    }

    /**
     * Save one or more route(s)
     *
     * @param array $routes
     */
    private function saveNew(array $routes)
    {
        $manager = Yii::$app->getAuthManager();
        foreach ($routes as $route) {
            try {
                $r = explode('&', $route);
                $item = $manager->createPermission('/' . trim($route, '/'));
                if (count($r) > 1) {
                    $action = '/' . trim($r[0], '/');
                    if (($itemAction = $manager->getPermission($action)) === null) {
                        $itemAction = $manager->createPermission($action);
                        $manager->add($itemAction);
                    }
                    unset($r[0]);
                    foreach ($r as $part) {
                        $part = explode('=', $part);
                        $item->data['params'][$part[0]] = isset($part[1]) ? $part[1] : '';
                    }
                    $this->setDefaultRule();
                    $item->ruleName = RouteRule::RULE_NAME;
                    $manager->add($item);
                    $manager->addChild($item, $itemAction);
                } else {
                    $manager->add($item);
                }
            } catch (Exception $e) {
                // -
            }
        }
    }

    /**
     * Get list of application routes
     *
     * @return array
     */
    public function getAppRoutes()
    {
        $key = __METHOD__;
        $cache = Yii::$app->getCache();
        $result = $cache->get($key);
        if ($result === false) {
            $result = [];
            $this->collectRouteRecursive(Yii::$app, $result);
            if ($cache !== null) {
                $cache->set($key, $result, self::CACHE_DURATION, new TagDependency([
                    'tags' => self::CACHE_TAG
                ]));
            }
        }

        return $result;
    }

    /**
     * Get route(s) recursive
     *
     * @param Module $module
     * @param array $result
     */
    private function collectRouteRecursive($module, &$result)
    {
        $token = "Get Route of '" . get_class($module) . "' with id '" . $module->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);
        try {
            foreach ($module->getModules() as $id => $child) {
                if (($child = $module->getModule($id)) !== null) {
                    $this->collectRouteRecursive($child, $result);
                }
            }

            foreach ($module->controllerMap as $id => $type) {
                $this->collectControllerActions($type, $id, $module, $result);
            }

            $namespace = trim($module->controllerNamespace, '\\') . '\\';
            $this->collectControllerFiles($module, $namespace, '', $result);
            $result[] = ($module->uniqueId === '' ? '' : '/' . $module->uniqueId) . '/*';
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get list controller under module
     *
     * @param Module $module
     * @param string $namespace
     * @param string $prefix
     * @param mixed $result
     */
    private function collectControllerFiles($module, $namespace, $prefix, &$result)
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', $namespace));
        $token = "Get controllers from '{$path}'";
        Yii::beginProfile($token, __METHOD__);
        try {
            if (!is_dir($path)) {
                return;
            }
            foreach (scandir($path) as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_dir($path . '/' . $file)) {
                    $this->collectControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
                } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                    $id = Inflector::camel2id(substr(basename($file), 0, -14));
                    $className = $namespace . Inflector::id2camel($id) . 'Controller';
                    if (strpos($className, '-') === false
                        && class_exists($className)
                        && is_subclass_of($className, BaseController::class)
                    ) {
                        $this->collectControllerActions($className, $prefix . $id, $module, $result);
                    }
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get list action of controller
     *
     * @param mixed $type
     * @param string $id
     * @param Module $module
     * @param string $result
     */
    private function collectControllerActions($type, $id, $module, &$result)
    {
        $token = "Create controller with cofig=" . VarDumper::dumpAsString($type) . " and id='$id'";
        Yii::beginProfile($token, __METHOD__);
        try {
            /* @var $controller BaseController */
            $controller = Yii::createObject($type, [$id, $module]);
            $this->getActionRoutes($controller, $result);
            $result[] = '/' . $controller->uniqueId . '/*';
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get route of action
     *
     * @param BaseController $controller
     * @param array $result all controller action.
     */
    private function getActionRoutes($controller, &$result)
    {
        $token = "Get actions of controller '" . $controller->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);
        try {
            $prefix = '/' . $controller->uniqueId . '/';
            foreach ($controller->actions() as $id => $value) {
                $result[] = $prefix . $id;
            }
            $class = new \ReflectionClass($controller);
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                    $result[] = $prefix . Inflector::camel2id(substr($name, 6));
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Invalidate cache
     */
    private function invalidate()
    {
        TagDependency::invalidate(Yii::$app->getCache(), self::CACHE_TAG);
    }

    /**
     * Set default rule of parameterize route
     */
    private function setDefaultRule()
    {
        if (Yii::$app->authManager->getRule(RouteRule::RULE_NAME) === null) {
            $routeRule = Yii::createObject([
                    'class' => RouteRule::className(),
                    'name' => RouteRule::RULE_NAME]
            );
            Yii::$app->authManager->add($routeRule);
        }
    }
}
