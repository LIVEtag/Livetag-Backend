Yii 2 Advanced Project Template
===============================

Yii 2 Advanced Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers.

The template includes three tiers: front end, back end, and console, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

DIRECTORY STRUCTURE
-------------------

```
rest
    common/              contains classes used in all version of REST API
    components/          contains components classes
    config/              contains rest configurations
    modules/             contains modules classes
    runtime/             contains files generated during runtime
    web/                 contains the entry script and Web resources
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in all areas backend and frontend and REST
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
tests                    contains various tests for the advanced application
    codeception/         contains tests developed with Codeception PHP Testing Framework
```

Settings swagger validator
------------
Для валидации swagger используются инструмент https://github.com/apigee-127/swagger-tools.
Валидатор указываем в файле params.php модуля swagger

```php
return [
    'rest.swaggerDebugUrl' => 'http://' . parse_url(\yii\helpers\Url::home(true))['host'] . ':8080/debug',
    'rest.swaggerValidatorUrl' => 'http://' . parse_url(\yii\helpers\Url::home(true))['host'] . ':8080',
];
```
Run swagger validator
------------
Для запуска валидатора надо из папки /swagger-validator выполнить комманду
```
    node index.js
```
По умолчанию валидатор будет слушать порт 8080, и валидировать файл
```
/rest/modules/swagger/config/swagger.json
```
Запуск валидатора на определенном порту (например 8888)
```
node index.js 8888
```
При изменении порта надо поменять настройки валидатора
```php
return [
    'rest.swaggerDebugUrl' => 'http://' . parse_url(\yii\helpers\Url::home(true))['host'] . ':8888/debug',
    'rest.swaggerValidatorUrl' => 'http://' . parse_url(\yii\helpers\Url::home(true))['host'] . ':8888',
];
```