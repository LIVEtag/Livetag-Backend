<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\components\api;

use yii\rest\UrlRule as BaseUrlRule;

/**
 * Class UrlRule
 */
class UrlRule extends BaseUrlRule
{
    /**
     * UrlRule constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->tokens = [
            '{id}' => '<id:\\d[\\d,]*>',
            '{productId}' => '<productId:\\d[\\d,]*>',
            '{slug}' => '<slug:[0-9a-zA-Z\-]+>',
        ];
        parent::__construct($config);
    }
}
