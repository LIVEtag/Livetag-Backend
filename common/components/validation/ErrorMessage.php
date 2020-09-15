<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 *
 * Base usages
 *
 * Use to Model (or any extended classes)
 *
 * in rules section:
 * ```
 * public function rules()
 * {
 *      return [
 *          [
 *              'amount',
 *              'integer',
 *              'min' => 1,
 *              'tooSmall' => new ErrorMessage('Item can has only positive quantity', XXX)
 *          ],
 *      ];
 * }
 * ```
 * in any method:
 * ```
 * public function validatePassword($attribute)
 * {
 *      ...
 *      /** @var ErrorListInterface $errorsList
 *      $errorsList = \Yii::createObject(ErrorListInterface::class);
 *
 *      $this->addError($attribute, $errorsList
 *          ->createErrorMessage(ErrorList::CREDENTIALS_INVALID)
 *          ->setParams(['email' => $this->email])
 *      );
 * }
 * ```
 */
declare(strict_types=1);

namespace common\components\validation;

use JsonSerializable;
use yii\validators\Validator;

class ErrorMessage implements JsonSerializable
{
    /** @var string */
    protected $message;

    /** @var int */
    protected $code;

    /** @var array */
    protected $params = [];

    /**
     * ValidationError constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code = ErrorListInterface::ERR_BASIC)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * Formats a message using the I18N, or simple strtr if `\Yii::$app` is not available.
     * Copied from validator for formatting custom errors
     * @see Validator::formatMessage()
     *
     * @param string $message
     * @param array $params
     * @return string
     */
    protected function formatMessage(string $message, array $params): string
    {
        if (\Yii::$app !== null) {
            return \Yii::$app->getI18n()->format($message, $params, \Yii::$app->language);
        }

        $placeholders = [];
        foreach ($params as $name => $value) {
            $placeholders['{' . $name . '}'] = $value;
        }

        return ($placeholders === []) ? $message : strtr($message, $placeholders);
    }

    /**
     * String representation of the error
     * @return string
     */
    public function __toString()
    {
        return $this->formatMessage($this->message, $this->params);
    }

    /**
     * JSON representation
     * used in client side validation
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }

    /**
     * Get error message
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get error code
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }
}
