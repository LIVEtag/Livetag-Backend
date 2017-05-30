<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace robo\tasks;

use Robo\Common\ExecOneCommand;
use Robo\Contract\CommandInterface;
use Robo\Contract\PrintedInterface;
use Robo\Exception\TaskException;
use Robo\Task\BaseTask;

/**
 * Class PHPCodesniffer
 */
class PHPCodesniffer extends BaseTask implements CommandInterface, PrintedInterface
{
    use ExecOneCommand;

    /**
     * @var string
     */
    protected $command;

    /**
     * Directory of test files or single test file to run.
     * Appended to the command and arguments.
     *
     * @var string[]
     */
    protected $directories = [];

    /**
     * PHPCodesniffer constructor.
     *
     * @param string $pathToPhpCs
     * @throws TaskException
     */
    public function __construct($pathToPhpCs = null)
    {
        $this->command = $pathToPhpCs;
        if (!$this->command && file_exists($filename = __DIR__ . '/../../../vendor/bin/phpcs')) {
            $this->command = $filename;
        }
        if (!$this->command) {
            throw new TaskException(
                __CLASS__, 'Neither local phpcs nor global composer installation not found'
            );
        }
    }

    /**
     * @param string $option
     * @param string $value
     *
     * @return $this
     */
    public function option($option, $value = null)
    {
        if ($option !== null && strpos($option, '-') !== 0) {
            $option = "--$option";
        }

        $this->arguments .= null === $option ? '' : " " . $option;
        $this->arguments .= null === $value ? '' : "=" . static::escape($value);

        return $this;
    }

    /**
     * @param string $standard
     *
     * @return $this
     */
    public function standard($standard)
    {
        $this->option('standard', $standard);
        return $this;
    }

    /**
     * @param string $report
     *
     * @return $this
     */
    public function report($report)
    {
        $this->option('report', $report);
        return $this;
    }

    /**
     * @param string $reportFile
     *
     * @return $this
     */
    public function reportFile($reportFile)
    {
        $this->option('report-file', $reportFile);
        return $this;
    }

    /**
     * @param string $extensions
     *
     * @return $this
     */
    public function extensions($extensions)
    {
        $this->option('extensions', $extensions);
        return $this;
    }

    /**
     * @param array $directories
     * @return $this
     */
    public function directories(array $directories)
    {
        $this->directories = $directories;
        return $this;
    }

    /**
     * Returns command that can be executed.
     * This method is used to pass generated command from one task to another.
     *
     * @return string
     */
    public function getCommand()
    {
        $this->args($this->directories);
        return $this->command . $this->arguments;
    }

    /**
     * @return bool
     */
    public function getPrinted()
    {
        return $this->isPrinted;
    }

    /**
     * @return \Robo\Result
     */
    public function run()
    {
        return $this->executeCommand($this->getCommand());
    }
}
