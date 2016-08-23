Yii2 base
=====
[![Build Status](https://phpci.gbksoft.net/buildStatus/icon?job=gurzhiy-an-yii2-base)](https://phpci.gbksoft.net/job/gurzhiy-an-yii2-base/)

Конфигурирование проекта через .env
-------------------

##### Добавляем конфигурацию в файл .env :
* Создаем файл .env (или другой файл с суффиксом .env - например .dev.env)
* Переносим базовый набор настроек из файла .env.example, в файл который Вы создали (!!ВАЖНО переменные которые присутствуют в .env.example являются обязательными для использования и должны присутствовать в .env или должны быть удалены из конфигурационных файлов *.php среды)
* Для создания новых настроек необходимо использовать шаблон в названии плейсхолдера - {{SOME_KEY}}, вместо которого будет вставлено значение из .env
```
    SOME_KEY="some value"
```


##### Как это работает:
 Скрипт вычитывает конфигурацию из выбранного .env файла
 При нахождении {{SOME_KEY}}, выполняет замену данного плейсхолдера на значение

> Все плейсхолдеры которые не удалось заменить на значения, в конце работы скрипта будут показаны.


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
```


Swagger UI
------------

```php
[http|https]://<domain>/swagger
```
Run swagger validator
------------

```shell
cd node/swagger/validator
node index.js
```
or
```shell
node index.js 8888
```

После
```
Заменить url в **Swagger UI** на [http|https]://localhost:<port>
```

Console commands
-------------

```shell
This is Yii version 2.0.9.

The following commands are available:

- access-token                  Class AccessTokenController
    access-token/clear-expired  Clear all expired data from current application

- asset                         Allows you to combine and compress your JavaScript and CSS files.
    asset/compress (default)    Combines and compresses the asset files according to the given configuration.
    asset/template              Creates template of configuration file for [[actionCompress]].

- cache                         Allows you to flush cache.
    cache/flush                 Flushes given cache components.
    cache/flush-all             Flushes all caches registered in the system.
    cache/flush-schema          Clears DB schema cache for a given connection component.
    cache/index (default)       Lists the caches that can be flushed.

- fixture                       Manages fixture data loading and unloading.
    fixture/load (default)      Loads the specified fixture data.
    fixture/unload              Unloads the specified fixtures.

- gii                           This is the command line version of Gii - a code generator.
    gii/controller              Controller Generator
    gii/crud                    CRUD Generator
    gii/extension               Extension Generator
    gii/form                    Form Generator
    gii/index (default)
    gii/model                   Model Generator
    gii/module                  Module Generator

- help                          Provides help information about console commands.
    help/index (default)        Displays available commands or the detailed information

- message                       Extracts messages to be translated from source files.
    message/config              Creates a configuration file for the "extract" command using command line options specified
    message/config-template     Creates a configuration file template for the "extract" command.
    message/extract (default)   Extracts messages to be translated from source code.

- migrate                       Manages application migrations.
    migrate/create              Creates a new migration.
    migrate/down                Downgrades the application by reverting old migrations.
    migrate/history             Displays the migration history.
    migrate/mark                Modifies the migration history to the specified version.
    migrate/new                 Displays the un-applied new migrations.
    migrate/redo                Redoes the last few migrations.
    migrate/to                  Upgrades or downgrades till the specified version.
    migrate/up (default)        Upgrades the application by applying new migrations.

- serve                         Runs PHP built-in web server
    serve/index (default)       Runs PHP built-in web server

- user                          Class UserController
    user/create                 Create new user
```
