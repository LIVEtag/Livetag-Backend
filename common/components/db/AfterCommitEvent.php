<?php
/*
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\db;

use yii\base\Event;

/**
 * AfterCommitEvent represents the information available in [[BaseActiveRecord::EVENT_AFTER_COMMIT_INSERT]]
 * and [[BaseActiveRecord::EVENT_AFTER_COMMIT_UPDATE]].
 */
class AfterCommitEvent extends Event
{
    /**
     * @var array The attribute values that had changed and were saved.
     */
    public $changedAttributes;
}
