<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace Deployer;

use Symfony\Component\Console\Input\InputOption;
use Dotenv\Dotenv;

require 'recipe/common.php';

// Global
set('application', 'Livetag');
set('default_timeout', 0.0);

// Environments variables
set('yii_environment', getenv('YII_BUILD_ENV'));

$variables = [];
if (file_exists(__DIR__ . '/.env')) {
    $variables = Dotenv::createArrayBacked(__DIR__)->load();
}

define('YII_PROJECT_ROOT', __DIR__);

set(
    'env',
    array_merge(
        $variables,
        [
            'YII_PROJECT_ROOT' => YII_PROJECT_ROOT,
        ]
    )
);

if (file_exists(YII_PROJECT_ROOT . '/.dep/hosts.yml')) {
    $data = file_get_contents(YII_PROJECT_ROOT . '/.dep/hosts.yml');
    foreach ($variables as $key => $variable) {
        $data = str_replace("{{" . strtoupper($key) . "}}", $variable, $data);
    }

    file_put_contents(YII_PROJECT_ROOT . '/.dep/hosts.tmp.yml', $data);
    inventory(YII_PROJECT_ROOT . '/.dep/hosts.tmp.yml');
}

// Tasks
task('deploy:init', function () {
    run('{{bin/php}} {{release_path}}/init --env={{yii_environment}} --overwrite=All');
})->desc('Initialization');

task('deploy:run_migrations', function () {
    run('{{bin/php}} {{release_path}}/yii migrate up --interactive=0');
})->desc('Run migrations');

task('gitlab:symlink', function () {
    writeln('Create link from {{source_path}} to {{dist_path}}');

    run('{{bin/symlink}} {{source_path}} {{dist_path}}');
})->desc('Creating symlink to project')
    ->setPrivate();

task('gitlab:version', function () {
    writeln('Create version.txt file');

    run("{{bin/git}} log --pretty=format:'%H' --abbrev-commit --date=short -1 > {{release_path}}/version.txt");
})->desc('Create version file')
    ->setPrivate();

task('gitlab:cron', function () {
    writeln('Setup Cron tasks');

    cd('{{release_path}}');

    run('cat ./.cron > /var/spool/cron/crontabs/www-data');
    run('echo >> /var/spool/cron/crontabs/www-data');
    run('chown www-data:crontab /var/spool/cron/crontabs/www-data');
    run('chmod 600 /var/spool/cron/crontabs/www-data');
    run('cron');
})->desc('Create cron task')
    ->setPrivate();

task('gitlab:success', function () {
    writeln(sprintf('<info>{{application}} is UP! Domain: %s</info>', getenv('BUILD_URL')));
})->desc('Success task')
    ->local()
    ->setPrivate()
    ->shallow();

task('gitlab:final', function () {
    run('chown -R www-data:www-data /var/www/html/*');
    invoke('common:unlink');
})->desc('Final ownership fix')
    ->setPrivate()
    ->shallow();

task('yii:build', function () {
    writeln('Start project build');
    set('release_path', YII_PROJECT_ROOT);
    set('deploy_path', YII_PROJECT_ROOT);

    invoke('deploy:init');
    invoke('deploy:run_migrations');
})->local()
    ->desc('Gitlab CI tasks bundle');

task('common:unlink', function () {
    $hostsFile = YII_PROJECT_ROOT . '/.dep/hosts.tml.yml';
    if (!file_exists($hostsFile)) {
        return;
    }
    unlink($hostsFile);
})->setPrivate();

task('gitlab:deploy', function () {
    writeln('Start project deploy');

    set('release_path', YII_PROJECT_ROOT);
    set('deploy_path', YII_PROJECT_ROOT);
    set('source_path', YII_PROJECT_ROOT);
    set('dist_path', '/var/www/html');

    writeln('Move files');

    invoke('gitlab:symlink');
    invoke('gitlab:version');
    invoke('yii:build');
    invoke('gitlab:cron');
    invoke('common:unlink');

})->desc('Deploy project on Gitlab CI/CD');

after('gitlab:deploy', 'gitlab:final');
after('gitlab:final', 'gitlab:success');

task('deploy:version', function () {
    writeln('Create version.txt file');
    cd('{{release_path}}');
    run("{{bin/git}} log --pretty=format:'%h %an(%ae) - %B' --abbrev-commit --date=short -1 > {{release_path}}/version.txt");
})->desc('Create version file')
    ->setPrivate();

task('deploy:upload', function () {
    upload('{{release_path}}/*', '{{deploy_path}}', ['options' => ['exclude' => "--exclude 'vendor/'"]]);
})->setPrivate();

task('deploy:composer', function () {

    cd("{{deploy_path}}");

    $noDev = "";
    if (strtolower(get('yii_environment')) == "live") {
        $noDev = "--no-dev";
    }
    run("{{bin/composer}} install $noDev");
});

task('deploy:run_migrations', function () {
    run('{{bin/php}} {{deploy_path}}/yii migrate up --interactive=0');
})->desc('Run migrations');

task('deploy:fixtures', function () {
    set('deploy_path', YII_PROJECT_ROOT);
    run('{{bin/php}} {{deploy_path}}/yii fixture/load "*" --interactive=0');
})->desc('Fill DB with fixtures');

$testPaths = [
    YII_PROJECT_ROOT . '/common',
    YII_PROJECT_ROOT . '/console',
    YII_PROJECT_ROOT . '/rest',
    YII_PROJECT_ROOT . '/backend',
];

task('tests:php_md', function () use ($testPaths) {
    $params = [
        implode(',', $testPaths),
        'xml '. YII_PROJECT_ROOT .'/dev/etc/phpmd/rules/rules.xml',
        '--suffixes php',
        '--exclude backend/web,rest/web,/views/,/gii/generators/,/backend/generators/,/migrations/,common/tests,backend/tests,rest/tests',
    ];
    run('php '. YII_PROJECT_ROOT .'/vendor/bin/phpmd ' . implode(' ', $params));
})->desc('PHP MD static tests');

task('tests:php_cpd', function () use ($testPaths) {
    $params = [
        implode(' ', $testPaths),
        '--min-lines 50',
        '--exclude tests',
    ];
    run('php '. YII_PROJECT_ROOT .'/vendor/bin/phpcpd ' . implode(' ', $params));
})->desc('PHP CPD static tests');;

task('tests:php_cs', function ()  use ($testPaths) {
    $params = [
        '--standard='. YII_PROJECT_ROOT .'/dev/etc/phpcs/standard/ruleset.xml',
        '--report=checkstyle',
        '--extensions=php',
        '-qn',
        implode(' ', $testPaths),
    ];
    run('php '. YII_PROJECT_ROOT .'/vendor/bin/phpcs ' . implode(' ', $params));
})->desc('PHP CS static tests');

task('tests:php_sa', function ()  use ($testPaths) {
    // solution until PHPCS_SecurityAudit rule exclude-pattern will be fixed
    // https://github.com/FloeDesignTechnologies/phpcs-security-audit/issues/45
    run('if [ ! -d "'.
        YII_PROJECT_ROOT .'/vendor/squizlabs/php_codesniffer/src/Standards/PHPCS_SecurityAudit" ]; then
        if [ -d "'. YII_PROJECT_ROOT .'/vendor/pheromone" ]; then
            ln -s '. YII_PROJECT_ROOT .'/vendor/pheromone/phpcs-security-audit/Security '
        . YII_PROJECT_ROOT .'/vendor/squizlabs/php_codesniffer/src/Standards/PHPCS_SecurityAudit
        fi
    fi
    ');

    $params = [
        '--standard='. YII_PROJECT_ROOT .'/dev/etc/phpcs/standard/security.xml',
        '--extensions=php',
        '--ignore='.YII_PROJECT_ROOT.'/rest/tests/*',
        implode(' ', $testPaths),
    ];
    run('php '. YII_PROJECT_ROOT .'/vendor/bin/phpcs ' . implode(' ', $params));
})->desc('PHP CS security audit tests');

task('tests:codeception', function () {
    run('php '. YII_PROJECT_ROOT .'/vendor/bin/codecept run');
})->desc('Codeception tests');

task('tests', function() {
    invoke('tests:php_md');
    invoke('tests:php_cpd');
    invoke('tests:php_cs');
    invoke('tests:php_sa');
});

task('deploy', function () {
    set('deploy_path', YII_PROJECT_ROOT);
    set('release_path', YII_PROJECT_ROOT);

    invoke('deploy:version');
    invoke('tests');
    invoke('deploy:prepare');
    invoke('deploy:lock');
    invoke('deploy:release');
    invoke('deploy:upload');
    invoke('deploy:symlink');
    invoke('deploy:composer');
    invoke('deploy:init');
    invoke('deploy:run_migrations');
    invoke('deploy:unlock');
    invoke('cleanup');
});

after('deploy', 'common:unlink');