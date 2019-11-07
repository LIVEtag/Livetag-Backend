<?php
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator \rest\generators\crud\Generator */

$js = <<<JS
function updateActionCheckbox(i) {
      var checked = $(this).prop('checked');
      $(this).
      closest('.row').
      find('.action__authenticatorExcept,.action__Rules').
      prop('disabled',!checked)
}

$('.action__field').each(updateActionCheckbox);
$('.action__field').on('click',updateActionCheckbox);
JS;
$this->registerJs($js);
$rulesList = $generator->getRulesList();
?>

<?= $form->field($generator, 'modelClass'); ?>
<?= $form->field($generator, 'controllerClass'); ?>

<div class="row">
    <div class="col col-sm-4"><?= $form->field($generator, 'actionIndex')
            ->checkbox(['class' => 'action__field']); ?></div>
    <div class="col col-sm-4"><?= $form->field($generator, 'actionIndexAuthenticatorExcept')
            ->label('Authenticator Except')->checkbox(['class' => 'action__authenticatorExcept']); ?></div>
    <div class="col col-sm-4"><?= $form->field($generator, 'actionIndexRules')->dropDownList($rulesList,
            ['prompt'=>'No rule'])->label(false); ?></div>
</div>
<div class="row border">
    <div class="col col-sm-4"><?= $form->field($generator, 'actionCreate')
            ->checkbox(['class' => 'action__field']); ?></div>
    <div class="col col-sm-4"><?= $form->field($generator, 'actionCreateAuthenticatorExcept')
            ->checkbox(['class' => 'action__authenticatorExcept'])
            ->label('Authenticator Except')->checkbox(['class' => 'action__authenticatorExcept']); ?></div>
    <div class="col col-sm-4"><?= $form->field($generator, 'actionCreateRules')->dropDownList($rulesList,
            ['prompt'=>'No rule'])->label(false); ?></div>
</div>
<div class="row border">
    <div class="col col-sm-4"><?= $form->field($generator, 'actionView')
            ->checkbox(['class' => 'action__field']); ?></div>
    <div class="col col-sm-4"><?= $form->field($generator, 'actionViewAuthenticatorExcept')
            ->checkbox(['class' => 'action__authenticatorExcept']); ?></div>
    <div class="col col-sm-4"><?= $form->field($generator, 'actionViewRules')->dropDownList($rulesList,
            ['prompt'=>'No rule'])->label(false); ?></div>
</div>
<div class="row border">
    <div class="col col-sm-4"><?= $form->field($generator, 'actionUpdate')
            ->checkbox(['class' => 'action__field']); ?></div>
    <div class="col col-sm-4"><?= $form->field($generator, 'actionUpdateAuthenticatorExcept')
            ->label('Authenticator Except')->checkbox(['class' => 'action__authenticatorExcept']); ?></div>
    <div class="col col-sm-4"><?= $form->field($generator, 'actionUpdateRules')->dropDownList($rulesList,
            ['prompt'=>'No rule'])->label(false); ?></div>
</div>
<div class="row border  ">
    <div class="col col-sm-4"><?= $form->field($generator, 'actionDelete')
            ->checkbox(['class' => 'action__field']); ?></div>
    <div class="col col-sm-4"><?= $form->field($generator, 'actionDeleteAuthenticatorExcept')
            ->label('Authenticator Except')->checkbox(['class' => 'action__authenticatorExcept']); ?></div>
    <div class="col col-sm-4"><?= $form->field($generator, 'actionDeleteRules')->dropDownList($rulesList,
            ['prompt'=>'No rule'])->label(false); ?></div>
</div>

<?= $form->field($generator, 'changeUrlManager')->checkbox(); ?>
<?= $form->field($generator, 'changeSwagger')->checkbox(); ?>
