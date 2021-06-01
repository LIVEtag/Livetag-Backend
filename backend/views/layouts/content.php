<?php

use dmstr\widgets\Alert;
use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\Breadcrumbs;

?>
<div class="content-wrapper">
    <section class="content-header">
        <?php if (isset($this->blocks['content-header'])) { ?>
            <h1 class="page-title"><?= $this->blocks['content-header'] ?></h1>
        <?php } else { ?>
            <h1 class="page-title page-title-<?= str_replace(' ', '-', strtolower(Html::encode($this->title))) ?>">
                <?php
                if ($this->title !== null) {
                    echo Html::encode($this->title);
                } else {
                    echo Inflector::camel2words(
                        Inflector::id2camel($this->context->module->id)
                    );
                    echo ($this->context->module->id !== Yii::$app->id) ? '<small>Module</small>' : '';
                } ?>
            </h1>
        <?php } ?>

        <?=
        Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
    </section>

    <section class="content">
        <?= Alert::widget() ?>
        <?= $content ?>
        <?= Dialog::widget(['overrideYiiConfirm' => true]); ?>
    </section>
</div>