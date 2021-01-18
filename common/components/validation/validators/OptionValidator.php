<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\validation\validators;

use common\components\validation\ValidationErrorTrait;
use common\models\Product\Product;
use yii\validators\Validator as BaseValidator;

class OptionValidator extends BaseValidator
{
    use ValidationErrorTrait;
    
    /**
     * validate if options and option's item of product are being correct json type
     */
    public function validateAttribute($model, $attribute)
    {
        $optionValues = $model->$attribute;
        if (!\is_array($optionValues)) {
            $this->addError(
                $model,
                $attribute,
                "property {$attribute} has wrong data"
            );
            return false;
        }
    
        foreach ($optionValues as $optionValue) {
            $result = array_diff(Product::OPTION_REQUIRED, array_flip($optionValue));
            if (!empty($result)) {
                $notExists = implode(', ', $result);
                $this->addError(
                    $model,
                    $attribute,
                    "Options must have {$notExists} values"
                );
                return false;
            }
    
            $this->isValidOptionItemValue($model, $attribute, $optionValue);
        }
        return true;
    }
    
    /**
     * @param $model
     * @param $attribute
     * @param $optionValue
     * @return bool
     */
    public function isValidOptionItemValue($model, $attribute, $optionValue): bool
    {
        if (!is_numeric($optionValue) && Product::PRICE === $optionValue) {
            $this->addError(
                $model,
                $attribute,
                "{$optionValue} must be number type"
            );
            return false;
        } elseif (!\is_string($optionValue)) {
            $this->addError(
                $model,
                $attribute,
                "{$optionValue} must be string type"
            );
            return false;
        } elseif (\strlen($optionValue) > 255) {
            $this->addError(
                $model,
                $attribute,
                "{$optionValue} must be lower than 255"
            );
            return false;
        }
        return true;
    }
}
