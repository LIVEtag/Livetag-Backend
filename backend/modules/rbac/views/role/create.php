<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\rbac\models\AuthItem $model
 */

$this->title = Yii::t('rbac-backend', 'Create Role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-backend', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-create">
	<h1><?= Html::encode($this->title) ?></h1>
	<?php echo $this->render('_form', [
        'model' => $model,
    ]); ?>
</div>
