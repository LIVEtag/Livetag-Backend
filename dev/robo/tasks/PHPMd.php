<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace robo\tasks;

use Robo\Common\ExecOneCommand;
use Robo\Contract\CommandInterface;
use Robo\Contract\PrintedInterface;
use Robo\Exception\TaskException;
use Robo\Task\BaseTask;

/**
 * Class PHPMd
 */
class PHPMd extends BaseTask implements CommandInterface, PrintedInterface
{
    use ExecOneCommand;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string[]
     */
    protected $directories;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $standard;

    /**
     * @var string
     */
    protected $suffixes;

    /**
     * @var string[]
     */
    protected $exclude;

    /**
     * PHPMd constructor.
     *
     * @param string $pathToPhpMd
     * @throws TaskException
     */
    public function __construct($pathToPhpMd = null)
    {
        $this->command = $pathToPhpMd;
        if (!$this->command && file_exists($filename = __DIR__ . '/../../../vendor/bin/phpmd')) {
            $this->command = $filename;
        }
        if (!$this->command) {
            throw new TaskException(
                __CLASS__, 'Neither local phpmd nor global composer installation not found'
            );
        }
    }

    /**
     * @param array $directories
     * @return $this
     */
    public function directories(array $directories)
    {
        $this->directories = (array) $directories;
        return $this;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function format($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param string $standard
     * @return $this
     */
    public function standard($standard)
    {
        $this->standard = $standard;
        return $this;
    }

    /**
     * @param string $suffixes
     * @return $this
     */
    public function suffixes($suffixes)
    {
        $this->suffixes = $suffixes;
        return $this;
    }

    /**
     * @param array $exclude
     * @return $this
     */
    public function exclude(array $exclude)
    {
        $this->exclude = $exclude;
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
        $this->option(null, implode(',', $this->directories))
            ->option(null, $this->format)
            ->option(null, $this->standard)
            ->option('suffixes', $this->suffixes)
            ->option('exclude', implode(',', $this->exclude));

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
