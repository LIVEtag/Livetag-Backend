<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\components\api\components;

use yii\filters\auth\HttpBearerAuth as BasicHttpBearerAuth;

/**
 * Class HttpBearerAuth
 */
class HttpBearerAuth extends BasicHttpBearerAuth
{
    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        return parent::authenticate($user, $request, $response);
    }
}
