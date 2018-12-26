<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\Pages;

use rest\common\models\StaticPage;
use rest\components\api\actions\Action;
use yii\data\ActiveDataProvider;
use yii\data\Sort;

/**
 * Class ViewAction
 */
class ListAction extends Action
{
    /**
     * @return null|ActiveDataProvider
     */
    public function run()
    {
        $sort = new Sort(
            [
                'attributes' => [
                    'sortOrder',
                ],
                'defaultOrder' => [
                    'sortOrder' => SORT_ASC
                ],
            ]
        );

        return new ActiveDataProvider(
            [
                'query' => StaticPage::find(),
                'pagination' => [
                    'defaultPageSize' => 10, //set page size here
                ],
                'sort' => $sort,
            ]
        );
    }
}
