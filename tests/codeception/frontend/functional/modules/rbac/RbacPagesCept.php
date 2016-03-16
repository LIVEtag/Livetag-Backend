<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use tests\codeception\frontend\FunctionalTester;
use tests\codeception\backend\functional\modules\rbac\_pages\IndexPage;
use tests\codeception\backend\functional\modules\rbac\_pages\RoutePage;
use tests\codeception\backend\functional\modules\rbac\_pages\PermissionPage;
use tests\codeception\backend\functional\modules\rbac\_pages\MenuPage;
use tests\codeception\backend\functional\modules\rbac\_pages\RolePage;
use tests\codeception\backend\functional\modules\rbac\_pages\AssignmentPage;
use tests\codeception\backend\functional\modules\rbac\_pages\RulePage;

/* @var $scenario Codeception\Scenario */

$I = new FunctionalTester($scenario);
$I->wantTo('ensure rbac pages works');

$indexPage = IndexPage::openBy($I);
$I->dontSee('Assignments', 'h1');
$indexPage = AssignmentPage::openBy($I);
$I->dontSee('Assignments', 'h1');

$indexPage = RolePage::openBy($I);
$I->dontSee('Roles', 'h1');

$indexPage = PermissionPage::openBy($I);
$I->dontSee('Permission', 'h1');

$indexPage = RoutePage::openBy($I);
$I->dontSee('Routes', 'h1');

$indexPage = RulePage::openBy($I);
$I->dontSee('Rules', 'h1');

$indexPage = MenuPage::openBy($I);
$I->dontSee('Menus', 'h1');
