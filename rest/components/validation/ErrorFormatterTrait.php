<?php
declare(strict_types=1);

namespace rest\components\validation;

trait ErrorFormatterTrait
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
            $message
                ->setMessage(parent::formatMessage((string) $message, $params))
                ->setParams($params);
        } else {
            $message = parent::formatMessage($message, $params);
        }
        return $message;
    }
}
