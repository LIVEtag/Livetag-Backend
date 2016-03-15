<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\controllers;

use backend\modules\rbac\components\MenuHelper;
use backend\modules\rbac\models\Menu;
use backend\modules\rbac\models\search\Menu as MenuSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class MenuController
 */
class MenuController extends Controller
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
     * Lists all Menu models
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MenuSearch;
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
     * Displays a single Menu model
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        /** @var \backend\modules\rbac\models\Menu $model */
        $model = Menu::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render(
            'view',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Creates a new Menu model
     * If creation is successful, the browser will be redirected to the 'view' page
     *
     * @return string
     */
    public function actionCreate()
    {
        $model = new Menu;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            MenuHelper::invalidate();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render(
            'create',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Updates an existing Menu model
     * If update is successful, the browser will be redirected to the 'view' page
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /** @var \backend\modules\rbac\models\Menu $model */
        $model = Menu::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($model->getMenuParent()) {
            $model->parentName = $model->getMenuParent()->name;
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            MenuHelper::invalidate();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing Menu model
     * If deletion is successful, the browser will be redirected to the 'index' page
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        /** @var \backend\modules\rbac\models\Menu $model */
        $model = Menu::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model->delete();

        MenuHelper::invalidate();

        return $this->redirect(['index']);
    }
}
