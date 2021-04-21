<?php

namespace common\helpers;

use Throwable;
use Yii;
use yii\base\Arrayable;
use yii\helpers\Json;

class LogHelper
{
    const TAG_STREAM_SESSION_ID = 'streamSessionId';

    /**
     * log error in sentry format
     * @param string $message
     * @param string $category
     * @param array $extra
     * @param array $tags
     */
    public static function error($message, $category, $extra = [], $tags = [])
    {
        Yii::error(['msg' => $message, 'extra' => $extra, 'tags' => $tags], $category);
    }

    /**
     * log warning in sentry format
     * @param string $message
     * @param string $category
     * @param array $extra
     * @param array $tags
     */
    public static function warning($message, $category, $extra = [], $tags = [])
    {
        Yii::warning(['msg' => $message, 'extra' => $extra, 'tags' => $tags], $category);
    }

    /**
     * log info message in sentry format
     * @param string $message
     * @param string $category
     * @param array $extra
     * @param array $tags
     */
    public static function info($message, $category, $extra = [], $tags = [])
    {
        Yii::info(['msg' => $message, 'extra' => $extra, 'tags' => $tags], $category);
    }

    /**
     * Generate default extra data for loging exceptions
     *
     * @param Arrayable $model
     * @param Throwable $ex
     * @return array
     */
    public static function extraForException(Arrayable $model, Throwable $ex)
    {
        return [
            'error' => $ex->getMessage(),
            'model' => Json::encode($model->toArray(), JSON_PRETTY_PRINT),
            'trace' => $ex->getTraceAsString(),
        ];
    }

    /**
     * Generate default extra data for loging model errors
     *
     * @param Arrayable $model
     * @param Throwable $ex
     * @return array
     */
    public static function extraForModelError(Arrayable $model)
    {
        return [
            'model' => Json::encode($model->toArray(), JSON_PRETTY_PRINT),
            'errors' => $model->getErrors()
        ];
    }
}
