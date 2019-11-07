<?php

use yii\helpers\StringHelper;
use rest\components\api\ActiveController;

/* @var $this yii\web\View */
/* @var $generator \rest\generators\crud\Generator */

/**
 * @param $action
 * @param int $tab
 * @return string
 */
$behaviorsAction = function ($action,$tab = 11){
    return PHP_EOL.str_repeat(" ",$tab * 4).$action.',';
};

$authenticator = $generator->actionIndex && $generator->actionIndexAuthenticatorExcept ? $behaviorsAction('self::ACTION_INDEX',9) :'';
$authenticator .= $generator->actionUpdate && $generator->actionUpdateAuthenticatorExcept ? $behaviorsAction('self::ACTION_UPDATE',9) :'';
$authenticator .= $generator->actionView && $generator->actionViewAuthenticatorExcept ? $behaviorsAction('self::ACTION_VIEW',9) :'';
$authenticator .= $generator->actionCreate && $generator->actionCreateAuthenticatorExcept ? $behaviorsAction('self::ACTION_CREATE',9) :'';
$authenticator .= $generator->actionDelete &&  $generator->actionDeleteAuthenticatorExcept ? $behaviorsAction('self::ACTION_DELETE',9) :'';
$authenticator = trim($authenticator,",");

$rulesMap = [];
foreach ($generator->getRulesList() as $keyRule => $rule){

    $rulesMap[$rule] = $generator->actionIndex && $keyRule === $generator->actionIndexRules ? $behaviorsAction('self::ACTION_INDEX') :'';
    $rulesMap[$rule] .= $generator->actionView && $keyRule === $generator->actionViewRules ? $behaviorsAction('self::ACTION_VIEW') :'';
    $rulesMap[$rule] .= $generator->actionUpdate && $keyRule === $generator->actionUpdateRules ? $behaviorsAction('self::ACTION_UPDATE') :'';
    $rulesMap[$rule] .= $generator->actionCreate && $keyRule === $generator->actionCreateRules ? $behaviorsAction('self::ACTION_CREATE') :'';
    $rulesMap[$rule] .= $generator->actionDelete && $keyRule === $generator->actionDeleteRules ? $behaviorsAction('self::ACTION_DELETE') :'';
    $rulesMap[$rule] = trim($rulesMap[$rule],',');

}

echo "<?php\n";
?>
/**
* Copyright © <?= date('Y')?> GBKSOFT. Web and Mobile Software Development.
* See LICENSE.txt for license details.
*/

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use yii\helpers\ArrayHelper;
use <?=$generator->modelClass?>;
use rest\components\api\ActiveController;

/**
 * <?= $generator::getClassName($generator->controllerClass)?> implements the CRUD actions for <?= $generator::getClassName($generator->modelClass) ?> model.
 */

class <?= $generator::getClassName($generator->controllerClass)?> extends ActiveController
{
    public $modelClass = <?=$generator::getClassName($generator->modelClass)?>::class;

    /**
    * @inheritdoc
    */
    public function behaviors():array
    {
        $behaviors = ArrayHelper::merge(
            parent::behaviors(),
            [
                <?php if($authenticator !== ''):?>

                'authenticator' => [
                    'except' => [<?=$authenticator?>

                                ],
                ],
                <?php endif;?>

                'access' => [
                    'rules' => [
                        <?php foreach($rulesMap as $role=>$action):?>
                        <?php if($action !== ''):?>

                        [
                            'allow' => true,
                            'actions' => [<?=$action?>

                                          ],
                            'roles' => ['<?=$role?>']
                        ],
                        <?php endif;?>
                        <?php endforeach;?>

                    ],
                ]
            ]
        );
        return $behaviors;
    }
    /**
    * @inheritdoc
    */
    public function actions()
    {
        $actions = parent::actions();
        <?php if(!$generator->actionIndex):?>

        unset($actions[ActiveController::ACTION_INDEX]);
        <?php endif;?>
        <?php if(!$generator->actionView):?>

        unset($actions[ActiveController::ACTION_VIEW]);
        <?php endif;?>
        <?php if(!$generator->actionUpdate):?>

        unset($actions[ActiveController::ACTION_UPDATE]);
        <?php endif;?>
        <?php if(!$generator->actionDelete):?>

        unset($actions[ActiveController::ACTION_DELETE]);
        <?php endif;?>
        <?php if(!$generator->actionCreate):?>

        unset($actions[ActiveController::ACTION_CREATE]);
        <?php endif;?>

        return $actions;
    }
}
