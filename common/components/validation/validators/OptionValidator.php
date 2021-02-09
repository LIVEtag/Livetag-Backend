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
     * @param $model
     * @param $attribute
     * @return bool
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
        $result = array_diff_key(array_flip(Product::OPTION_REQUIRED), $optionValues);
        if (!empty($result)) {
            $result = array_flip($result);
            $notExists = implode(', ', $result);
            $this->addError(
                $model,
                $attribute,
                "Options must have {$notExists} values"
            );
            return false;
        }
        
    
        foreach ($optionValues as $optionKey => $optionValue) {
            $this->isValidOptionItemValue($model, $attribute, $optionKey, $optionValue);
        }
        return true;
    }
    
    /**
     * @param $model
     * @param $attribute
     * @param $optionKey
     * @param $optionValue
     * @return bool
     */
    public function isValidOptionItemValue($model, $attribute, $optionKey, $optionValue): bool
    {
        if (Product::PRICE === $optionKey && !is_numeric($optionValue)) {
            $this->addError(
                $model,
                $attribute,
                "{$optionKey} must be number type"
            );
            return false;
        }
    
        if (Product::PRICE !== $optionKey && !\is_string($optionValue)) {
            $this->addError(
                $model,
                $attribute,
                "{$optionKey} must be string type"
            );
            return false;
        }
    
        if (\is_string($optionValue) && \strlen($optionValue) > 255) {
            $this->addError(
                $model,
                $attribute,
                "{$optionKey} must be lower than 255"
            );
            return false;
        }
    
        return true;
    }
}
