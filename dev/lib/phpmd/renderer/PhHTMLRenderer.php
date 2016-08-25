<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace dev\lib\phpmd\renderer;

use PHPMD\AbstractRenderer;
use PHPMD\Report;

/**
 * Class PhHTMLRenderer
 */
class PhHTMLRenderer extends AbstractRenderer
{
    /**
     * This method will be called on all renderers before the engine starts the
     * real report processing.
     */
    public function start()
    {
        $writer = $this->getWriter();

        $writer->write('<html><head><title>PHPMD</title></head><body>');
        $writer->write(PHP_EOL);
        $writer->write('<center><h1>PHPMD report</h1></center>');
        $writer->write('<center><h2>Problems found</h2></center>');
        $writer->write(PHP_EOL);
        $writer->write('<table align="center" cellspacing="0" cellpadding="3">');
        $writer->write('<tr>');
        $writer->write('<th>#</th><th>File</th><th>Line</th><th>Problem</th>');
        $writer->write('</tr>');
        $writer->write(PHP_EOL);
    }

    /**
     * This method will be called when the engine has finished the source analysis
     * phase.
     *
     * @param \PHPMD\Report $report
     */
    public function renderReport(Report $report)
    {
        $index = 0;

        $writer = $this->getWriter();
        foreach ($report->getRuleViolations() as $violation) {
            $writer->write('<tr');
            if (++$index % 2 === 1) {
                $writer->write(' bgcolor="lightgrey"');
            }
            $writer->write('>');
            $writer->write(PHP_EOL);

            $writer->write('<td align="center">');
            $writer->write($index);
            $writer->write('</td>');
            $writer->write(PHP_EOL);

            $writer->write('<td>');

            $prjFile = str_replace('/data/home/user/www/gym-go-web.loc/prj/', '', $violation->getFileName());

            $writer->write(
                sprintf(
                    '<a 
                        target="_blank" 
                        href="http://phabricator.gbksoft.net/diffusion/11/browse/master/%1$s$'
                        . $violation->getBeginLine() . '-' . $violation->getEndLine() . '"
                        >%1$s</a>',
                    $prjFile
                )

            );

            $writer->write('</td>');
            $writer->write(PHP_EOL);

            $writer->write('<td align="center" width="5%">');
            $writer->write($violation->getBeginLine());
            $writer->write('</td>');
            $writer->write(PHP_EOL);

            $writer->write('<td>');
            if ($violation->getRule()->getExternalInfoUrl()) {
                $writer->write('<a href="');
                $writer->write($violation->getRule()->getExternalInfoUrl());
                $writer->write('">');
            }

            $writer->write(htmlentities($violation->getDescription()));
            if ($violation->getRule()->getExternalInfoUrl()) {
                $writer->write('</a>');
            }

            $writer->write('</td>');
            $writer->write(PHP_EOL);

            $writer->write('</tr>');
            $writer->write(PHP_EOL);
        }

        $writer->write('</table>');

        $this->glomProcessingErrors($report);
    }

    /**
     * This method will be called the engine has finished the report processing
     * for all registered renderers.
     */
    public function end()
    {
        $writer = $this->getWriter();
        $writer->write('</body></html>');
    }

    /**
     * This method will render a html table with occurred processing errors.
     *
     * @param \PHPMD\Report $report
     */
    private function glomProcessingErrors(Report $report)
    {
        if (false === $report->hasErrors()) {
            return;
        }

        $writer = $this->getWriter();

        $writer->write('<hr />');
        $writer->write('<center><h3>Processing errors</h3></center>');
        $writer->write('<table align="center" cellspacing="0" cellpadding="3">');
        $writer->write('<tr><th>File</th><th>Problem</th></tr>');

        $index = 0;
        foreach ($report->getErrors() as $error) {
            $writer->write('<tr');
            if (++$index % 2 === 1) {
                $writer->write(' bgcolor="lightgrey"');
            }
            $writer->write('>');
            $writer->write('<td>' . $error->getFile() . '</td>');
            $writer->write('<td>' . htmlentities($error->getMessage()) . '</td>');
            $writer->write('</tr>' . PHP_EOL);
        }

        $writer->write('</table>');
    }
}
