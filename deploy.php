<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace Deployer;

require 'recipe/common.php';

// Global
set('application', 'Base project');
set('default_timeout', 0.0);

// Environments variables
set('yii_environment', getenv('YII_BUILD_ENV'));
set('design_repo', getenv('DESIGN_REPO_URL'));

$variables = [];
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $variables = (array)parse_ini_file($envFile);
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

function designDirectory () {
    $url = get('design_repo');
    $data = parse_url($url);
    $path = $data['path'] ?? null;

    if ($path === null) {
        throw new \RuntimeException('"path" key does not exists.');
    }
    [$dist,] = explode('.', $path);

    return basename($dist);
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

    // If are You using external a repository for frontend components, you must uncomment this code
    // set('design_directory', designDirectory());
    // set('design_path', YII_PROJECT_ROOT . '/design/' . get('design_directory'));
    // invoke('design:prepare');

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

task('deploy:design_clone', function () {
    run('rm -rf {{design_path}}');
    cd(\dirname(get('design_path')));
    run('git clone {{design_repo}}');
})->desc('Clone design project');

task('deploy:npm_install', function () {
    if (\file_exists(get('design_path'))) {
        cd('{{design_path}}');
        run('npm install');
    } else {
        writeln('No design dir found');
    }
})->desc('Install node modules');

task('deploy:npm_build', function () {
    if (\file_exists(get('design_path'))) {
        writeln('Npm build project');
        cd('{{design_path}}');
        run('npm run build:prod');
    }
})->desc('Build frontend');

task('design:prepare', function () {
    invoke('deploy:design_clone');
    invoke('deploy:npm_install');
    invoke('deploy:npm_build');
})->desc('Prepare design');

$testPaths = [
    YII_PROJECT_ROOT . '/common',
    YII_PROJECT_ROOT . '/console',
    YII_PROJECT_ROOT . '/rest',
    YII_PROJECT_ROOT . '/backend',
    YII_PROJECT_ROOT . '/frontend'
];

task('tests:php_md', function () use ($testPaths) {
    $params = [
        implode(',', $testPaths),
        'xml '. YII_PROJECT_ROOT .'/dev/etc/phpmd/rules/rules.xml',
        '--suffixes php',
        '--exclude backend/web,frontend/web,rest/web,/views/,/gii/generators/,/migrations/,common/tests,frontend/tests,backend/tests,rest/tests/_support',
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

$gitlabOptions = [
    'db_ip' => 'IP of mysql',
    'token' => 'Gitlab private token',
    'var_name' => 'Variable Name',
    'project' => 'Gitlab project ID'
];

foreach ($gitlabOptions as $optionKey => $option) {
    option($optionKey, null, InputOption::VALUE_REQUIRED, $option);
}

task('external_config', function () use ($gitlabOptions) {
    foreach ($gitlabOptions as $key => $gitlabOption) {
        ${$key} = input()->getOption($key);
    }
    //TODO create a console command within a project
    run('curl --request PUT --header "PRIVATE-TOKEN: '. $token .'" "https://gitlab.gbksoft.net/api/v4/projects/'. $project .'/variables/'. $var_name .'" --form "value='. $db_ip .'"');
});