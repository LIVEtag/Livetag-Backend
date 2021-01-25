<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use backend\models\Stream\StreamSession;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model StreamSession */

$this->title = 'Livestream details #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Livestream'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/** @var User $user */
$user = Yii::$app->user->identity ?? null;

?>
<section class="stream-session-view">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header"></div>
                <!--/.box-header -->
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'status',
                                'value' => function (StreamSession $model) {
                                    return $model->getStatusName();
                                },
                            ],
                            [
                                'attribute' => 'shopId',
                                'label' => 'Shop',
                                'format' => 'raw',
                                'value' => function (StreamSession $model) {
                                    return $model->shopId ? Html::a($model->shop->name, ['/shop/view', 'id' => $model->shop->id], ['data-pjax' => '0']) : null;
                                },
                                'visible' => $user && $user->isAdmin,
                            ],
                            'sessionId',
                            'createdAt:datetime',
                            'updatedAt:datetime',
                            'expiredAt:datetime',
                            [
                                'label' => 'Duration',
                                'value' => function (StreamSession $model) {
                                    return $model->getDuration();
                                }
                            ],
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