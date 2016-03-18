<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\modules\rbac\models\search\Menu */

$this->title = Yii::t('rbac-backend', 'Menus');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>
    <p>
        <?= Html::a(Yii::t('rbac-backend', 'Create Menu'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    Pjax::begin(['formSelector' => 'form', 'enablePushState' => false]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            [
                'attribute' => 'menuParent.name',
                'filter' => Html::activeTextInput($searchModel, 'parentName', [
                    'class' => 'form-control', 'id' => null
                ]),
                'label' => Yii::t('rbac-backend', 'Parent'),
            ],
            'route',
            'order',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    Pjax::end();
    ?>

</div>
