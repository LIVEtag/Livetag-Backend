<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\validation\validators;

use common\components\validation\ValidationErrorTrait;
use common\models\Product\Product;
use Yii;
use yii\base\DynamicModel;
use yii\validators\Validator as BaseValidator;

class OptionValidator extends BaseValidator
{
    use ValidationErrorTrait;

    /**
     * validate if options and option's item of product are being correct json type
     * @param $model
     * @param $attribute
     * @return bool
     */
    public function validateAttribute($model, $attribute)
    {
        $optionValues = $model->$attribute;
        if (!is_array($optionValues)) {
            $this->addError($model, $attribute, "{$attribute} must be an array");
            return false;
        }

        $missedAttributes = array_diff_key(array_flip(Product::OPTION_REQUIRED), $optionValues);
        if ($missedAttributes) {
            $this->addError(
                $model,
                $attribute,
                Yii::t(
                    'app',
                    '{attribute} required elements are missing: {missed}',
                    [
                        'attribute' => $attribute,
                        'missed' => implode(', ', array_flip($missedAttributes)),
                    ]
                )
            );
            return false;
        }

        foreach ($optionValues as $optionKey => $optionValue) {
            $this->validateOptionItem($model, $attribute, $optionKey, $optionValue);
        }
        return true;
    }

    /**
     * Validate option and add errror to model
     * @param $model
     * @param $attribute
     * @param $optionKey
     * @param $optionValue
     * @return bool
     */
    public function validateOptionItem($model, $attribute, $optionKey, $optionValue)
    {
        //Price cannot be blank.
        $validationModel = new DynamicModel([$optionKey]);
        $validationModel->addRule($optionKey, 'string', ['max' => 255]);
        //Validate required fields
        if (in_array($optionKey, Product::OPTION_REQUIRED)) {
            $validationModel->addRule($optionKey, 'required');
        }
        if ($optionKey == Product::PRICE) {
            $validationModel->addRule($optionKey, 'number');
        }
        $validationModel->$optionKey = $optionValue;
        if (!$validationModel->validate()) {
            $model->addError($attribute, $validationModel->getFirstError($optionKey));
        }
    }
}
