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
 *          ['amount', 'integer', 'min' => 1, 'tooSmall' => new ErrorMessage('Item can has only positive quantity', XXX)],
 *      ];
 * }
 * ```
 * in any method:
 * ```
 * public function validatePassword($attribute)
 * {
 *      ...
 *      $this->addError('password', new ErrorMessage('Email or Password is not correct', XXX));
 * }
 * ```
 */
declare(strict_types=1);

namespace rest\components\validation;

use JsonSerializable;

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
    public function __construct(string $message, int $code)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * String representation of the error
     * @return string
     */
    public function __toString()
    {
        return $this->message;
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
     * Set error message
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
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
     * Set error code
     * @param int $code
     * @return $this
     */
    public function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
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
