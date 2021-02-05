<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Stream;

use common\models\Stream\StreamSession as BaseModel;
use yii\helpers\ArrayHelper;

/**
 * Represents the backend version of `common\models\Stream\StreamSession`.
 */
class StreamSession extends BaseModel
{

    /**
     * Display stream duration
     * @return string|null
     */
    public function getDuration(): ?string
    {
        if (!$this->getStartedAt()) {
            return null;
        }
        $endTimestamp = $this->getStoppedAt() ?: time();
        $duration = $endTimestamp - $this->getStartedAt();
        return gmdate("H:i:s", $duration);
    }

    /**
     * Get all entities as indexed array
     * @return array [id=>key] array of entities
     */
    public static function getIndexedArray(): array
    {
        return self::find()->select(["CONCAT(id,' - ', sessionId) AS text", 'id'])->indexBy('id')->column();
    }

    /**
     * @return string
     */
    public function getStatusName(): ?string
    {
        return ArrayHelper::getValue(self::STATUSES, $this->status);
    }
}
