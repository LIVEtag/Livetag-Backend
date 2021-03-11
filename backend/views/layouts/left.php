<?php
use backend\models\User\User;
use dmstr\widgets\Menu;

/** @var User $user */
$user = Yii::$app->user->identity ?? null;
?>

<aside class="main-sidebar">
    <section class="sidebar">
        <?= Menu::widget([
            'options' => [
                'class' => 'sidebar-menu tree',
                'data-widget' => 'tree'
            ],
            'items' => [
                [
                    'options' => ['class' => 'header']
                ],
                [
                    'label' => 'About',
                    'icon' => 'info-circle',
                    'url' => ['/shop/my'],
                    'visible' => $user && $user->isSeller
                ],
                [
                    'label' => 'Products',
                    'icon' => 'cubes',
                    'url' => ['/product/index'],
                    'visible' => $user && $user->isSeller
                ],
                [
                    'label' => 'Livestreams',
                    'icon' => 'video-camera',
                    'url' => ['/stream-session/index'],
                    'active' => Yii::$app->controller->id == 'stream-session',
                    'visible' => $user && $user->isSeller
                ],
                [
                    'label' => 'Shops',
                    'icon' => 'shopping-bag',
                    'url' => ['/shop/index'],
                    'active' => Yii::$app->controller->id == 'shop',
                    'visible' => $user && $user->isAdmin
                ],
                [
                    'label' => 'Sellers management',
                    'icon' => 'users',
                    'url' => ['/user/index'],
                    'active' => Yii::$app->controller->id == 'user',
                    'visible' => $user && $user->isAdmin
                ],
                [
                    'options' => ['class' => 'header']
                ],
                [
                    'label' => 'Login',
                    'url' => ['/site/login'],
                    'visible' => Yii::$app->user->isGuest
                ],
                [
                    'label' => 'Logout',
                    'icon' => 'sign-out',
                    'url' => ['/site/logout'],
                    'template' => '<a href="{url}" data-method="post">{icon} {label}</a>',
                    'visible' => !Yii::$app->user->isGuest
                ],
            ],
        ]); ?>
    </section>
</aside>
