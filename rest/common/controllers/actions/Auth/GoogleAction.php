<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\Auth;

/**
 * Class GoogleAction
 */
class GoogleAction extends AbstractAuthAction
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $client = $this->getClient('google');

        $attributes = $this->authOAuth2($client);

        return $attributes;
    }
}
