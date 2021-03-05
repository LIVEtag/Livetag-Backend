<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\validation\validators;

use Yii;
use yii\helpers\StringHelper;
use yii\validators\Validator;

/**
 * ArrayValidator validates array of categories
 *
 * Must be an array of integers
 * Optionally, you may configure the [[max]] and [[min]] properties to ensure the amount items
 *
 */
class ArrayValidator extends Validator
{
    /**
     * @var bool whether the each element can only be an integer. Defaults to true.
     */
    public $integerOnly = false;

    /**
     * @var int upper limit of the items count. Defaults to null, meaning no upper limit.
     * @see toMany for the customized message used when too many elements.
     */
    public $max;

    /**
     * @var int lower limit of the items count. Defaults to null, meaning no lower limit.
     * @see toFew for the customized message used when too few elements.
     */
    public $min;

    /**
     * @var string user-defined error message used when the number of elements is greater than [[max]].
     */
    public $toMany;

    /**
     * @var string user-defined error message used when the number of elements is less than [[min]].
     */
    public $toFew;

    /**
     * @var string the regular expression for matching integers.
     */
    public $integerPattern = '/^\s*[+-]?\d+\s*$/';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = $this->integerOnly ?
                Yii::t('yii', '{attribute} must be an integer.') :
                Yii::t('yii', '{attribute} is invalid.');
        }
        if ($this->min !== null && $this->toFew === null) {
            $this->toFew = Yii::t('yii', '{attribute} must contain at least {min} items.');
        }
        if ($this->max !== null && $this->toMany === null) {
            $this->toMany = Yii::t('yii', '{attribute} must contain more than {max} items.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (!is_array($value)) {
            $this->addError($model, $attribute, $this->message);
            return;
        }
        if (!is_array($value) || ($this->integerOnly && !$this->isIntegerArray($value))) {
            $this->addError($model, $attribute, $this->message);
            return;
        }

        if ($this->min !== null && count($value) < $this->min) {
            $this->addError($model, $attribute, $this->toFew, ['min' => $this->min]);
        }
        if ($this->max !== null && count($value) > $this->max) {
            $this->addError($model, $attribute, $this->toMany, ['max' => $this->max]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        if (!is_array($value) || ($this->integerOnly && !$this->isIntegerArray($value))) {
            return [$this->message, []];
        } elseif ($this->min !== null && count($value) < $this->min) {
            return [$this->toFew, ['min' => $this->min]];
        } elseif ($this->max !== null && count($value) > $this->max) {
            return [$this->toMany, ['max' => $this->max]];
        }

        return null;
    }

    /**
     * Check if array contains only integers
     * @param srray $array
     * @return bool
     */
    protected function isIntegerArray($array):bool
    {
        return $array === array_filter($array, function ($value) {
            return preg_match($this->integerPattern, StringHelper::normalizeNumber($value));
        });
    }
}
