<?php

use yii\helpers\StringHelper;
use rest\components\api\ActiveController;

/* @var $this yii\web\View */
/* @var $generator \rest\generators\crud\Generator */

$authenticator = $generator->actionUpdate && $generator->actionUpdateAuthenticatorExcept ? '\''.ActiveController::ACTION_UPDATE.'\',' :'';
$authenticator .= $generator->actionIndex && $generator->actionIndexAuthenticatorExcept ? '\''.ActiveController::ACTION_INDEX.'\',' :'';
$authenticator .= $generator->actionView && $generator->actionViewAuthenticatorExcept ? '\''.ActiveController::ACTION_VIEW.'\',' :'';
$authenticator .= $generator->actionCreate && $generator->actionCreateAuthenticatorExcept ? '\''.ActiveController::ACTION_CREATE.'\',' :'';
$authenticator .= $generator->actionDelete &&  $generator->actionDeleteAuthenticatorExcept ? '\''.ActiveController::ACTION_DELETE.'\',' :'';
$authenticator = trim($authenticator,',');

$rulesMap = [];
foreach ($generator->getRulesList() as $keyRule => $rule){

    $rulesMap[$rule] = $generator->actionIndex && $keyRule === $generator->actionIndexRules ? '\''.ActiveController::ACTION_INDEX.'\',' :'';
    $rulesMap[$rule] .= $generator->actionView && $keyRule === $generator->actionViewRules ? '\''.ActiveController::ACTION_VIEW.'\',' :'';
    $rulesMap[$rule] .= $generator->actionUpdate && $keyRule === $generator->actionUpdateRules ? '\''.ActiveController::ACTION_UPDATE.'\',' :'';
    $rulesMap[$rule] .= $generator->actionDelete && $keyRule === $generator->actionDeleteRules ? '\''.ActiveController::ACTION_DELETE.'\',' :'';
    $rulesMap[$rule] .= $generator->actionCreate && $keyRule === $generator->actionCreateRules ? '\''.ActiveController::ACTION_CREATE.'\',' :'';
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
                    'except' => [<?=$authenticator?>],
                ],
                <?php endif;?>

                'access' => [
                    'rules' => [
                        <?php foreach($rulesMap as $role=>$action):?>
                        <?php if($action !== ''):?>

                        [
                            'allow' => true,
                            'actions' => [<?=$action?>],
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
