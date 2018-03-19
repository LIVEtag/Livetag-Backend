<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'Base project');

// Environments variables
set('yii_environment', getenv('YII_BUILD_ENV'));

$variables = [];
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $variables = (array)parse_ini_file($envFile);
}

set(
    'env',
    array_merge(
        $variables,
        [
            'YII_PROJECT_ROOT' => __DIR__,
        ]
    )
);

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

    run("{{bin/git}} log --pretty=format:'%h %an(%ae) - %B' --abbrev-commit --date=short -1 > {{release_path}}/version.txt");
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
    writeln(sprintf('<info>{{application}} is UP! Domain: %s</info>', getenv('MAIN_DOMAIN')));
})->desc('Success task')
    ->local()
    ->setPrivate()
    ->shallow();

task('gitlab:final', function () {
    run('chown -R www-data:www-data /var/www/html/*');
})->desc('Final ownership fix')
    ->setPrivate()
    ->shallow();

task('yii:build', function () {
    writeln('Start project build');
    set('release_path', __DIR__);
    set('deploy_path', __DIR__);

    invoke('deploy:init');
    invoke('deploy:run_migrations');
})->local()
    ->desc('Gitlab CI tasks bundle');

task('gitlab:deploy', function () {
    writeln('Start project deploy');

    set('release_path', __DIR__);
    set('deploy_path', __DIR__);

    writeln('Move files');

    invoke('gitlab:symlink');
    invoke('gitlab:version');
    invoke('gitlab:yii');
    invoke('gitlab:cron');
})->desc('Deploy project on Gitlab CI/CD');

after('gitlab:deploy', 'gitlab:final');
after('gitlab:final', 'gitlab:success');
