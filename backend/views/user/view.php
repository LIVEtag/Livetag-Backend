<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\User\User;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model User */

$this->title = 'User #' . $model->id;
if ($model->isAdmin) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sellers management'), 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="user-view">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header">
                    <?php if (Yii::$app->user->identity->isAdmin && $model->isSeller) : ?>
                    <div class="buttons-group">
                        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                            'class' => 'button button--danger button--ghost button--upper button--lg',
                            'data' => [
                                'confirm' => Yii::t('app', 'Are you sure that you want to delete this seller?'),
                                'method' => 'post',
                            ],
                        ]); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <!--/.box-header -->
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'role',
                            'email:email',
                            [
                                'label' => 'Shop name',
                                'attribute' => 'shopName',
                                'format' => 'raw',
                                'value' => function (User $model) {
                                    return $model->shop ? Html::a($model->shop->name, ['/shop/view', 'id' => $model->shop->id], ['data-pjax' => '0']) : null;
                                }
                            ],
                            'createdAt:datetime',
                            'updatedAt:datetime',
                        ],
                    ]); ?>
                </div>
                <!-- /.box-body -->
                <div class="box-footer"></div>
                <!--/.box-footer -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- /.section -->