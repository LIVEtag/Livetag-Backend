<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\components\validation\validators;

use yii\validators\IpValidator as BaseValidator;
use rest\components\validation\ErrorList;
use rest\components\validation\ValidationErrorTrait;

class IpValidator extends BaseValidator
{
    use ValidationErrorTrait;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->message === null) {
            $this->message = $this->errorList->createErrorMessage(ErrorList::IP_INVALID);
        }
        if ($this->ipv6NotAllowed === null) {
            $this->ipv6NotAllowed = $this->errorList->createErrorMessage(ErrorList::IP_V6_NOT_ALLOWED);
        }
        if ($this->ipv4NotAllowed === null) {
            $this->ipv4NotAllowed = $this->errorList->createErrorMessage(ErrorList::IP_V4_NOT_ALLOWED);
        }
        if ($this->wrongCidr === null) {
            $this->wrongCidr = $this->errorList->createErrorMessage(ErrorList::IP_WRONG_CIDR);
        }
        if ($this->noSubnet === null) {
            $this->noSubnet = $this->errorList->createErrorMessage(ErrorList::IP_NO_SUBNET);
        }
        if ($this->hasSubnet === null) {
            $this->hasSubnet = $this->errorList->createErrorMessage(ErrorList::IP_HAS_SUBNET);
        }
        if ($this->notInRange === null) {
            $this->notInRange = $this->errorList->createErrorMessage(ErrorList::IP_NOT_IN_RANGE);
        }
        parent::init();
    }
}
