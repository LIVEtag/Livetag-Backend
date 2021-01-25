<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\controllers;

use backend\components\Controller;
use backend\models\Stream\StreamSession;
use backend\models\Stream\StreamSessionSearch;
use backend\models\User\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\web\NotFoundHttpException;

/**
 * StreamSessionController implements the CRUD actions for StreamSession model.
 */
class StreamSessionController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => [User::ROLE_ADMIN, User::ROLE_SELLER],
                        ],
                    ],
                ]
            ]
        );
    }

    /**
     * Lists all StreamSession models.
     * Display only sellers sessions (by shopId) for seller role
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if (!$user || ($user->isSeller && !$user->shop)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $searchModel = new StreamSessionSearch();
        $params = Yii::$app->request->queryParams;
        if ($user->isSeller) {
            $params = ArrayHelper::merge($params, [StringHelper::basename(get_class($searchModel)) => ['shopId' => $user->shop->id]]);
        }
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StreamSession model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
        //todo: check access for seller (US 4.4)
        return $this->render('view', [
                'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the StreamSession model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StreamSession the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id)
    {
        $model = StreamSession::findOne($id);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
