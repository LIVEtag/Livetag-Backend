<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use tests\codeception\backend\FunctionalTester;
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
$I->see('Assignments', 'h1');

$indexPage = RoutePage::openBy($I);
$I->see('Routes', 'h1');

$indexPage = PermissionPage::openBy($I);
$I->see('Permission', 'h1');

$indexPage = MenuPage::openBy($I);
$I->see('Menus', 'h1');

$indexPage = RolePage::openBy($I);
$I->see('Roles', 'h1');

$indexPage = AssignmentPage::openBy($I);
$I->see('Assignments', 'h1');

$indexPage = RulePage::openBy($I);
$I->see('Rules', 'h1');
