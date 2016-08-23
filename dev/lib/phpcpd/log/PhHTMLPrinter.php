<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace dev\lib\phpcpd\log;

use PHPMD\Writer\StreamWriter;
use SebastianBergmann\PHPCPD\CodeCloneMap;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class PhHTMLPrinter
 */
class PhHTMLPrinter
{
    /**
     * @var StreamWriter
     */
    private $writer;

    /**
     * @var int
     */
    private $lines = 0;

    /**
     * @var array
     */
    private $files = [];

    /**
     * Prints a result set from Detector::copyPasteDetection().
     *
     * @param InputInterface $input
     * @param CodeCloneMap $clones
     */
    public function printResult(InputInterface $input, CodeCloneMap $clones)
    {
        $this->setWriter(new StreamWriter($input->getOption('report-file')));

        $this->lines  = 0;
        $this->files = [];
        $numClones = count($clones);

        $writer = $this->getWriter();

        $writer->write('<html><head><title>PHPCPD</title></head><body>');
        $writer->write(PHP_EOL);

        if ($numClones > 0) {

            $table = $this->getTableContent($clones);

            $writer->write('<center><h1>PHPCPD report</h1></center>');
            $writer->write('<center><h3>' . sprintf(
                    "Found %d exact clones with %d duplicated lines in %d files:\n\n",
                    $numClones,
                    $this->lines,
                    count($this->files)
                ) . '</h3></center>');
            $writer->write(PHP_EOL);

            $writer->write($table);
        }

        $writer->write(PHP_EOL);
        $writer->write('<center><h3>' . sprintf(
                "%s%s duplicated lines out of %d total lines of code.\n\n",
                $numClones > 0 ? "\n" : '',
                $clones->getPercentage(),
                $clones->getNumLines()
            ) . '</h3></center>');
        $writer->write(PHP_EOL);

        $writer->write('</body></html>');
    }

    /**
     * @return StreamWriter
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * @param StreamWriter $writer
     */
    private function setWriter(StreamWriter $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @param CodeCloneMap $clones
     * @return string
     */
    private function getTableContent(CodeCloneMap $clones)
    {
        $index = 0;
        $buffer = '';

        $buffer .= ('<table align="center" cellspacing="0" cellpadding="3">');
        $buffer .= ('<tr>');
        $buffer .= ('<th>#</th><th>File</th><th>Duplicate</th>');
        $buffer .= ('</tr>');
        $buffer .= (PHP_EOL);

        foreach ($clones as $clone) {

            $buffer .= ('<tr');

            foreach ($clone->getFiles() as $file) {
                $filename = $file->getName();

                if (!isset($this->files[$filename])) {
                    $this->files[$filename] = true;
                }
            }

            $this->lines  += $clone->getSize() * (count($clone->getFiles()) - 1);

            if (++$index % 2 === 1) {
                $buffer .= (' bgcolor="lightgrey"');
            }
            $buffer .= ('>');
            $buffer .= (PHP_EOL);

            $buffer .= ('<td align="center">');
            $buffer .= ($index);
            $buffer .= ('</td>');
            $buffer .= (PHP_EOL);

            $buffer .= ('<td>');

            $links = '';
            $names = '';
            foreach ($clone->getFiles() as $file) {

                $prjFile = str_replace('/data/home/user/www/gym-go-web.loc/prj/', '', $file->getName());
                $links .= sprintf(
                    '<div><a 
                        target="_blank" 
                        href="http://phabricator.gbksoft.net/diffusion/11/browse/master/%1$s$'
                    . $file->getStartLine() . '-' . ($file->getStartLine() + $clone->getSize()) . '"
                        >%1$s</a></div>',
                    $prjFile
                );

                $names .= '<li>' . sprintf(
                    "%s:%d-%d ",
                    $prjFile,
                    $file->getStartLine(),
                    $file->getStartLine() + $clone->getSize()
                ) . '</li>';
            }

            $buffer .= ('<td>' . $links . '</td>');
            $buffer .= (PHP_EOL);

            $buffer .= ('<td><ul>' . $names . '</ul></td>');
            $buffer .= (PHP_EOL);
        }

        return $buffer;
    }
}
