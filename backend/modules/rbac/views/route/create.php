<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\modules\rbac\models\Route $model
 * @var ActiveForm $form
 */

$this->title = Yii::t('rbac-backend', 'Create Route');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-backend', 'Routes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Yii::t('rbac-backend', 'Create Route') ?></h1>

<div class="create">

	<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'route') ?>

		<div class="form-group">
			<?= Html::submitButton(Yii::t('rbac-backend', 'Create'), ['class' => 'btn btn-primary']) ?>
		</div>
	<?php ActiveForm::end(); ?>

</div><!-- create -->
