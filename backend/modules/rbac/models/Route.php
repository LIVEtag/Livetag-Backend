<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\models;

use yii\base\Model;

/**
 * Class Route
 */
class Route extends Model
{
    /**
     * Route value
     *
     * @var string
     */
    public $route;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return[
            [['route'],'safe'],
        ];
    }
}
