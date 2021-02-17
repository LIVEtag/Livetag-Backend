<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\controllers\actions\User;

use common\models\forms\User\UserProfileForm;
use common\models\User;
use Yii;
use yii\base\Action;

/**
 * Class UpdateAction
 */
class UpdateAction extends Action
{
    public function run()
    {
        $params = \Yii::$app->request->getBodyParams();
        /**@var User $currentUser*/
        $currentUser = Yii::$app->user->identity;
        $userProfileForm = new UserProfileForm($currentUser);
        $userProfileForm->setAttributes($params);
        return $userProfileForm->save();
    }
}
