<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\modules\swagger\controllers\actions\Main;

use Gbksoft\Shell\Command;
use Gbksoft\Shell\Command\Argument;
use Gbksoft\Shell\Command\Flag;
use Gbksoft\Shell\Command\Option;
use Gbksoft\Shell\CommandBuilder;
use yii\base\Action;
use yii\web\Response;

/**
 * Class HistoryAction
 */
class HistoryAction extends Action
{
    /**
     * Run action
     * @param string $hash
     * @throws \yii\base\ExitException
     */
    public function run($hash = null)
    {
        \Yii::$app->response->format = Response::FORMAT_HTML;
        $builder = new CommandBuilder();
        $pathToJson = \Yii::getAlias('@swagger') . '/config/swagger.json';
        $ansi2html = \Yii::getAlias('@swagger') . '/console/ansi2html.sh';


        // set executable external!!!
        // @chmod(__DIR__ . '/../ansi2html.sh', 0755);

        if ($hash && preg_match('#[a-z0-9]{4,40}#', $hash)) {
            $commands = [];
            $commands[] = $builder->setCommand(new Command('cd'))
                ->addArgument(new Argument(dirname($pathToJson)))
                ->build();

            $commands[] = $builder->setCommand(new Command('git'))
                ->addArgument(new Argument('log'))
                ->addFlag(new Flag('--color'))
                ->addFlag(new Flag('-p'))
                ->addFlag(new Flag('-1'))
                ->addArgument(new Argument($hash))
                ->addFlag(new Flag('--'))
                ->addArgument(new Argument('./' . basename($pathToJson)))
                ->build();

            echo shell_exec(implode('; ', $commands) . ' | ' . $ansi2html);
            \Yii::$app->end();
        }

        $format = '<tr class="log-item">';
        $format .= '<td style="cursor: pointer;" class="log-hash">%h</td>';
        $format .= '<td class="log-date">%ad</td>';
        $format .= '<td class="log-short-comment">%s</td>';
        $format .= '<td class="log-full-comment">%b</td>';
        $format .= '</tr>';

        $commands = [];

        $commands[] = $builder->setCommand(new Command('cd'))
            ->addArgument(new Argument(dirname($pathToJson)))
            ->build();

        $commands[] = $builder->setCommand(new Command('git'))
            ->addArgument(new Argument('log'))
            ->addFlag(new Flag('--color'))
            ->addOption(new Option('--pretty', 'format:' . $format))
            ->addFlag(new Flag('--no-merges'))
            ->addFlag(new Flag('-10'))
            ->addFlag(new Flag('--'))
            ->addArgument(new Argument('./' . basename($pathToJson)))
            ->build();
        echo '<table>';
        echo stripslashes(shell_exec(implode('; ', $commands)));
        echo '</table>';
        \Yii::$app->end();
    }
}
