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
            $params = $this->renameArrayKeys($params, ['attribute' => 'attr']);
            $this->beforeFormatMessage($message, $params);
            $message->setParams($params);
        } else {
            $message = parent::formatMessage($message, $params);
        }
        return $message;
    }

    /**
     * Format message pre-processor
     * @param ErrorMessage $message
     * @param $params
     * @SuppressWarnings("PMD.UnusedFormalParameter")
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

    /**
     * Rename $source array key to new values from $names
     * @param array $source
     * @param array $names
     * @return array
     */
    protected function renameArrayKeys(array $source, array $names): array
    {
        $destination = [];
        foreach ($source as $key => $value) {
            $destination[array_key_exists($key, $names) ? $names[$key] : $key] = $value;
        }
        return $destination;
    }
}
