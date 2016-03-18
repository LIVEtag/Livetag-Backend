<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var backend\modules\rbac\models\search\AuthItem $searchModel
 */
$this->title = Yii::t('rbac-backend', 'Rules');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('rbac-backend', 'Create Rule'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    Pjax::begin([
        'enablePushState'=>false,
    ]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],
            [
                'attribute' => 'name',
                'label' => Yii::t('rbac-backend', 'Name'),
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
