<?php
use common\models\User;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $content string */

/* @var $user User */
$user = Yii::$app->user->identity;
?>

<header class="main-header">
    <?= Html::a(
        '<span class="logo-mini">LT</span><span class="logo-lg"><img src="' . Yii::getAlias('@web') . '/images/logo.png" class="user-image" alt="' . Yii::$app->name . '"/></span>',
        Yii::$app->homeUrl,
        ['class' => 'logo']
    ); ?>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <?php if ($user) : ?>
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="user-image" alt="User Image"/>
                            <span class="hidden-xs"><?= $user->email ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>

                                <p>
                                    <?= $user->email ?>
                                    <small>Member since <?= Yii::$app->formatter->asDate($user->createdAt) ?></small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <?=
                                    Html::a(
                                        'Change Password',
                                        ['/user/change-password'],
                                        ['class' => 'btn btn-default btn-flat']
                                    )
                                    ?>
                                </div>
                                <div class="pull-right">
                                    <?=
                                    Html::a(
                                        'Sign out',
                                        ['/site/logout'],
                                        ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                    )
                                    ?>
                                </div>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>
