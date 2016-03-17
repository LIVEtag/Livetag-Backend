<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\controllers;

use backend\modules\rbac\components\MenuHelper;
use backend\modules\rbac\models\AuthItem;
use backend\modules\rbac\models\search\AuthItem as AuthItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\rbac\Item;
use Yii;
use yii\web\Response;

/**
 * Class PermissionController
 */
class PermissionController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all AuthItem models
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch(['type' => Item::TYPE_PERMISSION]);
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single AuthItem model
     *
     * @param string $name
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($name)
    {
        $item = Yii::$app->getAuthManager()->getPermission($name);

        if ($item === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new AuthItem($item);

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return string
     */
    public function actionCreate()
    {
        $model = new AuthItem();
        $model->type = Item::TYPE_PERMISSION;
        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            MenuHelper::invalidate();

            return $this->redirect(['view', 'name' => $model->name]);
        }

        return $this->render('create', ['model' => $model,]);
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page
     *
     * @param string $name
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($name)
    {
        $item = Yii::$app->getAuthManager()->getPermission($name);

        if ($item === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new AuthItem($item);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            MenuHelper::invalidate();

            return $this->redirect(['view', 'name' => $model->name]);
        }

        return $this->render('update', ['model' => $model,]);
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $name
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDelete($name)
    {
        $item = Yii::$app->getAuthManager()->getPermission($name);

        if ($item === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        /** @var \common\modules\rbac\components\DbManager $authManager */
        $authManager = Yii::$app->getAuthManager();
        $model = new AuthItem($item);

        $authManager->remove($model->getItem());
        MenuHelper::invalidate();

        return $this->redirect(['index']);
    }

    /**
     * Assign or remove items
     *
     * @return array
     */
    public function actionAssign()
    {
        $post = Yii::$app->getRequest()->post();
        $name = $post['name'];
        $action = $post['action'];
        $roles = $post['roles'];
        $manager = Yii::$app->getAuthManager();
        $parent = $manager->getPermission($name);
        $error = [];
        if ($action == 'assign') {
            foreach ($roles as $role) {
                $child = $manager->getPermission($role);
                try {
                    $manager->addChild($parent, $child);
                } catch (\Exception $exc) {
                    $error[] = $exc->getMessage();
                }
            }
        } else {
            foreach ($roles as $role) {
                $child = $manager->getPermission($role);
                try {
                    $manager->removeChild($parent, $child);
                } catch (\Exception $exc) {
                    $error[] = $exc->getMessage();
                }
            }
        }
        MenuHelper::invalidate();
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;

        return['type' => 'S', 'errors' => $error];
    }

    /**
     * Search role
     *
     * @param string $name
     * @param string $target
     * @param string $term
     * @return array
     */
    public function actionSearch($name, $target, $term = '')
    {
        $result = [
            'Permission' => [],
            'Routes' => [],
        ];
        $authManager = Yii::$app->getAuthManager();
        if ($target == 'avaliable') {
            $children = array_keys($authManager->getChildren($name));
            $children[] = $name;
            foreach ($authManager->getPermissions() as $permissionsName => $role) {
                if (in_array($permissionsName, $children)) {
                    continue;
                }
                if (empty($term) or strpos($permissionsName, $term) !== false) {
                    $result[$permissionsName[0] === '/'
                        ? 'Routes'
                        : 'Permissions'][$permissionsName] = $permissionsName;
                }
            }
        } else {
            foreach ($authManager->getChildren($name) as $childName => $child) {
                if (empty($term) or strpos($childName, $term) !== false) {
                    $result[$childName[0] === '/' ? 'Routes' : 'Permissions'][$childName] = $childName;
                }
            }
        }

        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        return array_filter($result);
    }
}
