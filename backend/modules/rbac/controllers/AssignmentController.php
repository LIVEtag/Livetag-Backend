<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\controllers;

use Yii;
use common\models\User;
use backend\modules\rbac\models\search\Assignment as AssignmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\modules\rbac\components\MenuHelper;
use yii\web\Response;

/**
 * Class AssignmentController
 *
 * AssignmentController implements the CRUD actions for User model
 */
class AssignmentController extends Controller
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
                    'assign' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Assignment models
     *
     * @return string
     */
    public function actionIndex()
    {

        $searchModel = new AssignmentSearch;
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'idField' => 'id',
                'usernameField' => 'username',
                'extraColumns' => [],
            ]
        );
    }

    /**
     * Displays a single Assignment model
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = User::findIdentity($id);

        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render(
            'view',
            [
                'model' => $model,
                'idField' => 'id',
                'usernameField' => 'username',
                'fullnameField' => 'username',
            ]
        );
    }

    /**
     * Assign or revoke assignment to user
     *
     * @return string
     */
    public function actionAssign()
    {
        $id = Yii::$app->request->post('id');
        $action = Yii::$app->request->post('action');
        $roles = Yii::$app->request->post('roles');

        $manager = Yii::$app->authManager;
        $error = [];
        if ($action == 'assign') {
            foreach ($roles as $name) {
                try {
                    $item = $manager->getRole($name);
                    $item = $item ? : $manager->getPermission($name);
                    $manager->assign($item, $id);
                } catch (\Exception $exception) {
                    $error[] = $exception->getMessage();
                }
            }
        } else {
            foreach ($roles as $name) {
                try {
                    $item = $manager->getRole($name);
                    $item = $item ? : $manager->getPermission($name);
                    $manager->revoke($item, $id);
                } catch (\Exception $exception) {
                    $error[] = $exception->getMessage();
                }
            }
        }
        MenuHelper::invalidate();
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'type' => 'S',
            'errors' => $error,
        ];
    }

    /**
     * Search roles of user
     *
     * @param int $id
     * @param string  $target
     * @param string  $term
     * @return string
     */
    public function actionSearch($id, $target, $term = '')
    {
        Yii::$app->response->format = 'json';

        $authManager = Yii::$app->authManager;
        $roles = $authManager->getRoles();
        $permissions = $authManager->getPermissions();

        $avaliable = [];
        $assigned = [];
        foreach ($authManager->getAssignments($id) as $assigment) {
            if (isset($roles[$assigment->roleName])) {
                if (empty($term) || strpos($assigment->roleName, $term) !== false) {
                    $assigned['Roles'][$assigment->roleName] = $assigment->roleName;
                }
                unset($roles[$assigment->roleName]);
            } else if (isset($permissions[$assigment->roleName])
                && $assigment->roleName[0] != '/'
            ) {
                if (empty($term) || strpos($assigment->roleName, $term) !== false) {
                    $assigned['Permissions'][$assigment->roleName] = $assigment->roleName;
                }
                unset($permissions[$assigment->roleName]);
            }
        }

        if ($target == 'avaliable') {
            if (count($roles)) {
                foreach ($roles as $role) {
                    if (empty($term) || strpos($role->name, $term) !== false) {
                        $avaliable['Roles'][$role->name] = $role->name;
                    }
                }
            }
            if (count($permissions)) {
                foreach ($permissions as $role) {
                    if ($role->name[0] != '/' && (empty($term) || strpos($role->name, $term) !== false)) {
                        $avaliable['Permissions'][$role->name] = $role->name;
                    }
                }
            }

            return $avaliable;
        }

        return $assigned;
    }
}
