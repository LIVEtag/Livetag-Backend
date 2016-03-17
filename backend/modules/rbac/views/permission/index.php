<?php

use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\modules\rbac\models\search\AuthItem */

$this->title = Yii::t('rbac-backend', 'Permission');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('rbac-backend', 'Create Permission'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    Pjax::begin([
        'enablePushState'=>false,
    ]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => Yii::t('rbac-backend', 'Name'),
            ],
            [
                'attribute' => 'description',
                'label' => Yii::t('rbac-backend', 'Description'),
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, $model) {
                    return Url::to([$action, 'name' => $model->name]);
                }
            ],
        ],
    ]);
    Pjax::end();
    ?>

</div>
