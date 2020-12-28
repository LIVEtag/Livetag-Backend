<?php
use dmstr\widgets\Menu;
?>

<aside class="main-sidebar">
    <section class="sidebar">
        <?=
        Menu::widget([
            'options' => [
                'class' => 'sidebar-menu tree',
                'data-widget' => 'tree'
            ],
            'items' => [
                [
                    'label' => 'Users List',
                    'icon' => 'users',
                    'url' => ['user/index'],
                    'active' => Yii::$app->controller->id == 'user'
                ]
            ],
        ]); ?>
    </section>
</aside>
