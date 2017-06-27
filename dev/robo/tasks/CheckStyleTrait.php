<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace robo\tasks;

/**
 * Trait CheckStyleTrait
 */
trait CheckStyleTrait
{
    /**
     * @return \Robo\Result
     */
    public function style()
    {
        /** @var $this \Robo\Tasks */
        $this->say("Style check is started...");

        $directories = [
            __DIR__ . '/../../../common',
            __DIR__ . '/../../../console',
            __DIR__ . '/../../../rest',
            __DIR__ . '/../../../backend',
        ];

        $PHPCsTask = $this->getPHPCsTask()
            ->directories($directories)
            ->extensions('php')
            ->report('emacs') // @see https://github.com/squizlabs/PHP_CodeSniffer/wiki/Reporting
            ->standard(__DIR__ . '/../../../dev/etc/phpcs/standard/ruleset.xml')
            ;
        $PHPCpd = $this->getPHPCpdTask()
            ->directories($directories)
            ;
        $PHPMd = $this->getPHPMdTask()
            ->directories($directories)
            ->format('text')
            ->standard(__DIR__ . '/../../../dev/etc/phpmd/rules/rules.xml')
            ->suffixes('php')
        ;

        return $this->collectionBuilder()
            ->addTask($PHPMd)
            ->addTask($PHPCpd)
            ->addTask($PHPCsTask)
            ->run()
        ;
    }

    /**
     * @return PHPCodesniffer
     */
    public function getPHPCsTask()
    {
        return $this->task(PHPCodesniffer::class, $pathToPhpCs = null);
    }

    /**
     * @return PHPCpd
     */
    public function getPHPCpdTask()
    {
        return $this->task(PHPCpd::class, $pathToPhpCs = null);
    }

    /**
     * @return PHPMd
     */
    public function getPHPMdTask()
    {
        return $this->task(PHPMd::class, $pathToPhpCs = null);
    }
}
