<?php

namespace common\components\behaviors;

use yii\behaviors\TimestampBehavior as BaseTimestampBehavior;

/**
 * Class TimestampBehavior
 */
class TimestampBehavior extends BaseTimestampBehavior
{
    /**
     * {@inheritdoc}
     */
    public $createdAtAttribute = 'createdAt';

    /**
     * {@inheritdoc}
     */
    public $updatedAtAttribute = 'updatedAt';
}
