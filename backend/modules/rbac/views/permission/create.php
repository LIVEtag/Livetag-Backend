<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rbac\models\AuthItem */

$this->title = Yii::t('rbac-backend', 'Create Permission');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-backend', 'Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-create">
	<h1><?= Html::encode($this->title) ?></h1>
	<?php echo $this->render('_form', [
        'model' => $model,
    ]); ?>

</div>
