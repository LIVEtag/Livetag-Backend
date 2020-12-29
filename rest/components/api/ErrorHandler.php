<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\components\api;

use yii\web\ErrorHandler as WebErrorHandler;
use yii\base\Exception;
use yii\base\ErrorException;
use yii\base\UserException;
use yii\web\HttpException;

/**
 * Class ErrorHandler
 *
 * Replace and extends base Error Handler
 */
class ErrorHandler extends WebErrorHandler
{
    /**
     * Converts an exception into an array.
     * @param \Exception|\Error $exception the exception being converted
     * @return array the array representation of the exception.
     * @throws \yii\base\InvalidConfigException
     */
    protected function convertExceptionToArray($exception)
    {
        if (!YII_DEBUG && !$exception instanceof UserException && !$exception instanceof HttpException) {
            $exception = new HttpException(
                500,
                \Yii::t('yii', 'An internal server error occurred.')
            );
        }

        $array = [
            'name' => ($exception instanceof Exception || $exception instanceof ErrorException)
                ? $exception->getName()
                : 'Exception',
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];
        if ($exception instanceof HttpException) {
            $array['status'] = 'error';
            $array['code'] = $exception->statusCode;
        }

        if (YII_DEBUG) {
            $array['type'] = get_class($exception);
            if (!$exception instanceof UserException) {
                $array['file'] = $exception->getFile();
                $array['line'] = $exception->getLine();
                $array['stack-trace'] = explode("\n", $exception->getTraceAsString());
                if ($exception instanceof \yii\db\Exception) {
                    $array['error-info'] = $exception->errorInfo;
                }
            }
        }
        $prev = $exception->getPrevious();
        if ($prev !== null) {
            $array['previous'] = $this->convertExceptionToArray($prev);
        }

        return $array;
    }
}
