<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace dev\lib\phpcpd\cli;

use SebastianBergmann\PHPCPD\Log\PMD;
use tests\lib\phpcpd\log\PhHTMLPrinter;
use SebastianBergmann\FinderFacade\FinderFacade;
use SebastianBergmann\PHPCPD\CLI\Command as BaseCommand;
use SebastianBergmann\PHPCPD\Detector\Detector;
use SebastianBergmann\PHPCPD\Detector\Strategy\DefaultStrategy;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command
 */
class Command extends BaseCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('phpcpd')
            ->setDefinition(
                [
                    new InputArgument(
                        'values',
                        InputArgument::IS_ARRAY,
                        'Files and directories to analyze'
                    )
                ]
            )
            ->addOption(
                'exclude',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Exclude a directory from code analysis (must be relative to source)'
            )
            ->addOption(
                'log-pmd',
                null,
                InputOption::VALUE_REQUIRED,
                'Write result in PMD-CPD XML format to file'
            )
            ->addOption(
                'min-lines',
                null,
                InputOption::VALUE_REQUIRED,
                'Minimum number of identical lines',
                5
            )
            ->addOption(
                'report-file',
                null,
                InputOption::VALUE_REQUIRED,
                'Report file'
            )
            ->addOption(
                'min-tokens',
                null,
                InputOption::VALUE_REQUIRED,
                'Minimum number of identical tokens',
                70
            )
            ->addOption(
                'fuzzy',
                null,
                InputOption::VALUE_NONE,
                'Fuzz variable names'
            )
            ->addOption(
                'progress',
                null,
                InputOption::VALUE_NONE,
                'Show progress bar'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|integer null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new FinderFacade(
            $input->getArgument('values'),
            $input->getOption('exclude'),
            ['*.php'],
            []
        );

        $files = $finder->findFiles();

        if (empty($files)) {
            $output->writeln('No files found to scan');
            exit(1);
        }

        $progressHelper = null;

        if ($input->getOption('progress')) {
            $progressHelper = $this->getHelperSet()->get('progress');
            $progressHelper->start($output, count($files));
        }

        $strategy = new DefaultStrategy;
        $detector = new Detector($strategy, $progressHelper);

        $clones = $detector->copyPasteDetection(
            $files,
            $input->getOption('min-lines'),
            $input->getOption('min-tokens'),
            $input->getOption('fuzzy')
        );

        if ($input->getOption('progress')) {
            $progressHelper->finish();
            $output->writeln('');
        }

        $logPmd = $input->getOption('log-pmd');

        if ($logPmd) {
            $pmd = new PMD($logPmd);
            $pmd->processClones($clones);
            unset($pmd);
        } else {
            $printer = new PhHTMLPrinter();
            $printer->printResult($input, $clones);
            unset($printer);
        }

        print \PHP_Timer::resourceUsage() . "\n";

        if (count($clones) > 0) {
            exit(1);
        }
    }
}
