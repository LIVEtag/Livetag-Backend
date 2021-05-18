<?php

namespace console\migrations;

use common\components\db\Migration;
use console\migrations\M210111122050CreateProductTable as Product;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class M210513074908ProductMultipleImages
 */
class M210513074908ProductMultipleImages extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update(Product::TABLE_NAME, ['photo' => new Expression('CONCAT("[\"",`photo`,"\"]")')]);
        $this->alterColumn(Product::TABLE_NAME, 'photo', $this->json()->null());
        $this->renameColumn(Product::TABLE_NAME, 'photo', 'photos');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn(Product::TABLE_NAME, 'photos', 'photo');

        $this->alterColumn(Product::TABLE_NAME, 'photo', $this->string(255)->null());

        $query = (new Query())->select(['id', 'photo'])->from(Product::TABLE_NAME);
        foreach ($query->each() as $product) {
            $id = ArrayHelper::getValue($product, 'id');
            $firstPhoto = ArrayHelper::getValue(Json::decode(ArrayHelper::getValue($product, 'photo')), 0);
            $this->update(Product::TABLE_NAME, ['photo' => $firstPhoto], ['id' => $id]);
        }


    }
}
