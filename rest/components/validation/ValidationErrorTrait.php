<?php
declare(strict_types=1);

namespace rest\components\validation;

/**
 * Trait ValidationErrorTrait
 * @property-read ErrorListInterface $errorList
 */
trait ValidationErrorTrait
{
    /**
     * Formats a message using the I18N, or simple strtr if `\Yii::$app` is not available.
     * @param ErrorMessage|string $message
     * @param array $params
     * @return ErrorMessage|string
     */
    protected function formatMessage($message, $params)
    {
        if ($message instanceof ErrorMessage) {
            if (isset($params['attribute'])) {
                $params['attr'] = $params['attribute'];
                unset($params['attribute']);
            }
            $this->beforeFormatMessage($message, $params);
            $message
                ->setMessage(parent::formatMessage((string) $message, $params))
                ->setParams($params);
        } else {
            $message = parent::formatMessage($message, $params);
        }
        return $message;
    }

    /**
     * @param ErrorMessage $message
     * @param $params
     */
    protected function beforeFormatMessage(ErrorMessage $message, array &$params): void
    {
    }

    /**
     * @return ErrorListInterface
     * @throws \yii\base\InvalidConfigException
     */
    protected function getErrorList(): ErrorListInterface
    {
        return \Yii::createObject(ErrorListInterface::class);
    }
}
