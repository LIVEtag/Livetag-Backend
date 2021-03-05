<?php
/*
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace console\controllers;

use common\helpers\LogHelper;
use common\models\Stream\StreamSession;
use console\components\traits\InfoTrait;
use Yii;
use yii\console\Controller;
use yii\console\widgets\Table;
use yii\helpers\Console;
use const PHP_EOL;

/**
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
class StreamSessionController extends Controller
{
    use InfoTrait;
    /**
     * End Soon notification (10 minutes)
     */
    const NOTIFY_INTERVAL = 600;

    /**
     * Check all active streams (by status only). Executes every minute
     *  - if 10 minutes before end - fire event
     *  - if expired date more than curernt - change session status to stop
     */
    public function actionCheckActiveStreams()
    {
        $time = $this->start();
        //original time to comapare (rounded to minutes).
        $now = $this->toMinutes($time);

        $streamSessionQuery = StreamSession::find()->byStatus(StreamSession::STATUS_ACTIVE);
        $this->stdout('Found ' . ($streamSessionQuery->count()) . ' Active Streams' . PHP_EOL, Console::FG_GREEN);

        /** @var StreamSession $streamSession */
        foreach ($streamSessionQuery->each() as $streamSession) {
            if (!$streamSession->getExpiredAt()) {
                continue;
            }
            $endSoon = $this->getEndSoonTimestamp($streamSession->getExpiredAt());
            echo PHP_EOL . Table::widget([
                'rows' => [
                    ['Id', $streamSession->getId()],
                    ['Shop Id', $streamSession->getShopId()],
                    ['Created At', $this->displayDate($this->toMinutes($streamSession->getCreatedAt()))],
                    ['Expired At', $this->displayDate($this->toMinutes($streamSession->getExpiredAt()))],
                    ['Now', $this->displayDate($now)],
                    ['End Soon Notification', $this->displayDate($endSoon)],
                ],
            ]);

            if ($streamSession->getExpiredAt() < $time) {
                $this->stdout('StreamSession Expired by time. Change status to STOPPED.' . PHP_EOL, Console::FG_PURPLE);
                if ($streamSession->stop()) {
                    $this->stdout('StreamSession STOPPED' . PHP_EOL, Console::FG_GREEN);
                } else {
                    $message = 'Failed to update StreamSession status to STOPPED ';
                    $this->stdout($message . PHP_EOL, Console::FG_RED);
                    LogHelper::error($message, StreamSession::LOG_CATEGORY, LogHelper::extraForModelError($streamSession));
                }
            } elseif ($now === $endSoon) {
                $this->stdout('StreamSession END SOON. Notify!.' . PHP_EOL, Console::FG_PURPLE);
                $streamSession->trigger(StreamSession::EVENT_END_SOON);
            } else {
                $this->stdout('StreamSession in progress. No actions required' . PHP_EOL, Console::FG_GREEN);
            }
        }

        $this->memoryUsage();
        return $this->end($time);
    }

    /**
     * Get end soon timestamp (rounded to minutes)
     * @param int $timestamp
     * @return int
     */
    public function getEndSoonTimestamp(int $timestamp): int
    {
        return $this->toMinutes($timestamp - self::NOTIFY_INTERVAL);
    }

    /**
     * Format timestamp to display
     * @param int $timestamp
     * @return string
     */
    protected function displayDate(int $timestamp): string
    {
        return Yii::$app->formatter->asDateTime($timestamp);
    }

    /**
     * Round timestamp to minutes (for further calculations)
     * @param int $timestamp
     * @return int
     */
    protected function toMinutes(int $timestamp): int
    {
        return $timestamp - ($timestamp % 60);
    }
}
