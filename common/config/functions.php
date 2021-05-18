<?php
/*
 * Some functions, that required inside config
 */

use yii\queue\file\Queue as FileQueue;
use yii\queue\LogBehavior;
use yii\queue\sqs\Queue as SqsQueue;

/**
 * Generate config for queue
 * @param string $name
 * @return array
 */
function getQueueConfigByName($name)
{
    return getenv('USE_FILE_QUEUE') ? [
        'class' => FileQueue::class,
        'path' => '@common/queue-' . $name,
        'as log' => LogBehavior::class
        ] : [
        'class' => SqsQueue::class,
        'url' => 'https://sqs.' . (getenv('AMAZON_SQS_REGION') ?: 'ap-southeast-1') . '.amazonaws.com/' .
            getenv('AMAZON_ACCOUNT') . '/' . ENV . '-' . $name,
        'key' => getenv('AMAZON_ACCESS_KEY') ?: '',
        'secret' => getenv('AMAZON_SECRET_KEY') ?: '',
        'region' => getenv('AMAZON_SQS_REGION') ?: 'ap-southeast-1',
        'as log' => LogBehavior::class
        ];
}
