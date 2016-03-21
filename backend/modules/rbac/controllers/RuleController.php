<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\controllers;

use backend\modules\rbac\components\MenuHelper;
use backend\modules\rbac\models\BizRule;
use Yii;
use backend\components\Controller;
use backend\modules\rbac\models\search\BizRule as BizRuleSearch;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class RuleController
 */
class RuleController extends Controller
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
        $searchModel = new BizRuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

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
        $item = Yii::$app->authManager->getRule($name);
        if ($item === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new BizRule($item);

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new AuthItem model
     * If creation is successful, the browser will be redirected to the 'view' page
     *
     * @return string
     */
    public function actionCreate()
    {
        $model = new BizRule(null);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
        $item = Yii::$app->authManager->getRule($name);
        if ($item === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new BizRule($item);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            MenuHelper::invalidate();

            return $this->redirect(['view', 'name' => $model->name]);
        }

        return $this->render('update', ['model' => $model,]);
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page
     *
     * @param string $name
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDelete($name)
    {
        $item = Yii::$app->authManager->getRule($name);
        if ($item === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        /** @var \common\modules\rbac\components\DbManager $authManager */
        $authManager = Yii::$app->getAuthManager();
        $model = new BizRule($item);
        $authManager->remove($model->getItem());
        MenuHelper::invalidate();

        return $this->redirect(['index']);
    }
}
