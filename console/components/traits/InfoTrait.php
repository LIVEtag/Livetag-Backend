<?php

namespace console\components\traits;

use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;

/**
 * Some basic action detais echo
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
trait InfoTrait
{
    /**
     * @return int
     */
    protected function start()
    {
        $time = time();
        $this->stdout('START ' . PHP_EOL, Console::FG_GREEN);
        return $time;
    }

    /**
     * @param $time
     * @return int
     */
    protected function end($time)
    {
        $this->stdout('END in ' . (time() - $time) . ' sec' . PHP_EOL, Console::FG_GREEN);
        return ExitCode::OK;
    }

    /**
     * Display memory usage
     */
    protected function memoryUsage()
    {
        echo Table::widget([
            'headers' => ['Memory', 'Amount'],
            'rows' => [
                ['Current', $this->formatBytes(memory_get_usage())],
                ['Max', $this->formatBytes(memory_get_peak_usage())],
            ],
        ]);
    }

    /**
     * @see http://php.net/manual/en/function.filesize.php
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    protected function formatBytes($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = (int) floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}
