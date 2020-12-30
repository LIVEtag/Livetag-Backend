<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\ActionColumn;
use backend\models\User\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\User\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sellers management');
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="user-index">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn bg-black']) ?>
                </div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'hover' => true, //the grid table will highlight row on hover
                        'persistResize' => true, //to store resized column state using local storage persistence
                        'options' => ['id' => 'user-list'],
                        'pjax' => true,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'hAlign' => GridView::ALIGN_CENTER,
                                'headerOptions' => ['width' => '80'],
                            ],
                            [
                                'attribute' => 'role',
                                'filter' => Html::activeDropDownList($searchModel, 'role', User::ROLES, ['class' => 'form-control', 'prompt' => '']),
                                'value' => function (User $model) {
                                    return $model->getRoleName();
                                }
                            ],
                            'email:email',
                            [
                                'attribute' => 'status',
                                'filter' => Html::activeDropDownList($searchModel, 'status', User::STATUSES, ['class' => 'form-control', 'prompt' => '']),
                                'value' => function (User $model) {
                                    return $model->getStatusName();
                                }
                            ],
                            'createdAt:datetime',
                            [
                                'class' => ActionColumn::class,
                                'vAlign' => GridView::ALIGN_TOP,
                                'template' => '{view} {delete}',
                            ],
                        ],
                    ]); ?>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>