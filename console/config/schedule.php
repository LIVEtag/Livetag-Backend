<?php
use omnilight\scheduling\Schedule;

/**
 * @var Schedule $schedule
 */
$schedule->command('access-token/clear-expired')->daily();
$schedule->command('stream-session/check-active-streams')->everyMinute();
//$schedule->call(function (\yii\console\Application $app) {})->everyFiveMinutes();
