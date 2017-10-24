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
 * Class PHPCpd
 */
class PHPCpd extends BaseTask implements CommandInterface, PrintedInterface
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
     * @var int
     */
    protected $minLines = 150;

    /**
     * PHPCpd constructor.
     *
     * @param string $pathToPhpCpd
     * @throws TaskException
     */
    public function __construct($pathToPhpCpd = null)
    {
        $this->command = $pathToPhpCpd;
        if (!$this->command && file_exists($filename = __DIR__ . '/../../../vendor/bin/phpcpd')) {
            $this->command = $filename;
        }
        if (!$this->command) {
            throw new TaskException(
                __CLASS__, 'Neither local phpcpd nor global composer installation not found'
            );
        }
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
     * @param int $minLines
     * @return $this
     */
    public function minLines($minLines)
    {
        $this->minLines = (int) $minLines;
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
        $this->args($this->directories)
            ->option('min-lines', $this->minLines);

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
