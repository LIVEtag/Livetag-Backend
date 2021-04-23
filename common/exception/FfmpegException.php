<?php
namespace common\exception;

use Exception;
use Throwable;

/**
 * Class FfmpegException
 * @see https://stackoverflow.com/a/3272635/1202097
 */
class FfmpegException extends Exception
{
    /**
     * array of errors
     * @var array
     */
    protected $errors = [];

    /**
     * Command, that was executed
     * @var string
     */
    protected $command = '';

    /**
     * @param array $errors - errors from ffmpeg
     * @inheritdoc
     */
    public function __construct(string $message = '', $command = '', $errors = [], int $code = 0, Throwable $previous = null)
    {
        $this->command = $command;
        $this->errors = is_array($errors) ? $errors : []; //just in case - simple do not store if not array passed
        parent::__construct($message, $code, $previous);
    }

    /**
     * get ffmpeg errors
     * @return array
     */
    public function getFfmpegErros(): array
    {
        return $this->errors;
    }

    /**
     * Get executed command
     * @return string
     */
    public function getCommand(): string
    {
        return (string) $this->command;
    }
}
