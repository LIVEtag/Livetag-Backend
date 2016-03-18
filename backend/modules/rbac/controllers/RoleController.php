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

/**
 * Class RoleController
 */
class RoleController extends Controller
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
        $searchModel = new AuthItemSearch(['type' => Item::TYPE_ROLE]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]
        );
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
        $item = Yii::$app->getAuthManager()->getRole($name);
        if ($item === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new AuthItem($item);

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page
     *
     * @return string
     */
    public function actionCreate()
    {
        $model = new AuthItem();
        $model->type = Item::TYPE_ROLE;
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
        $item = Yii::$app->getAuthManager()->getRole($name);
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
     * Deletes an existing AuthItem model
     * If deletion is successful, the browser will be redirected to the 'index' page
     *
     * @param string $name
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDelete($name)
    {
        $item = Yii::$app->getAuthManager()->getRole($name);
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
        $parent = $manager->getRole($name);
        $error = [];
        if ($action == 'assign') {
            foreach ($roles as $role) {
                $child = $manager->getRole($role);
                $child = $child ? : $manager->getPermission($role);
                try {
                    $manager->addChild($parent, $child);
                } catch (\Exception $e) {
                    $error[] = $e->getMessage();
                }
            }
        } else {
            foreach ($roles as $role) {
                $child = $manager->getRole($role);
                $child = $child ? : $manager->getPermission($role);
                try {
                    $manager->removeChild($parent, $child);
                } catch (\Exception $e) {
                    $error[] = $e->getMessage();
                }
            }
        }
        MenuHelper::invalidate();
        Yii::$app->response->format = 'json';

        return[
            'type' => 'S',
            'errors' => $error,
        ];
    }

    /**
     * Search role
     *
     * @param string $id
     * @param string $target
     * @param string $term
     * @return array
     */
    public function actionSearch($id, $target, $term = '')
    {
        $result = [
            'Roles' => [],
            'Permissions' => [],
            'Routes' => [],
        ];
        $authManager = Yii::$app->authManager;
        if ($target == 'avaliable') {
            $children = array_keys($authManager->getChildren($id));
            $children[] = $id;
            foreach ($authManager->getRoles() as $name => $role) {
                if (in_array($name, $children)) {
                    continue;
                }
                if (empty($term) or strpos($name, $term) !== false) {
                    $result['Roles'][$name] = $name;
                }
            }
            foreach ($authManager->getPermissions() as $name => $role) {
                if (in_array($name, $children)) {
                    continue;
                }
                if (empty($term) or strpos($name, $term) !== false) {
                    $result[$name[0] === '/' ? 'Routes' : 'Permissions'][$name] = $name;
                }
            }
        } else {
            foreach ($authManager->getChildren($id) as $name => $child) {
                if (empty($term) or strpos($name, $term) !== false) {
                    if ($child->type == Item::TYPE_ROLE) {
                        $result['Roles'][$name] = $name;
                    } else {
                        $result[$name[0] === '/' ? 'Routes' : 'Permissions'][$name] = $name;
                    }
                }
            }
        }
        Yii::$app->response->format = 'json';

        return array_filter($result);
    }
}
