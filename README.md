Конфигурирование проекта через .env
================= 
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
 
>Все плейсхолдеры которые не удалось заменить на значения, в конце работы скрипта будут показаны.


Yii 2 Advanced Project Template
===============================

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
UI - https://github.com/jensoleg/swagger-ui
Для валидации swagger используются инструмент https://github.com/apigee-127/swagger-tools.
Валидатор указываем в файле params.php модуля swagger

```php
return [
    'rest.swaggerDebugUrl' => 'http://' . parse_url('http://' . Yii::getAlias('@backend.domain'))['host']
        . ':8080/debug?url=http://' . Yii::getAlias('@rest.domain') . '/swagger/main/json',
    'rest.swaggerValidatorUrl' => 'http://' . parse_url('http://' . Yii::getAlias('@backend.domain'))['host']
        . ':8080/validate?url=http://' . Yii::getAlias('@rest.domain') . '/swagger/main/json',
];
```
Run swagger validator
------------
Для запуска валидатора надо из папки /node/swagger/validator выполнить комманду
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
    'rest.swaggerDebugUrl' => 'http://' . parse_url('http://' . Yii::getAlias('@backend.domain'))['host']
        . ':8888/debug?url=http://' . Yii::getAlias('@rest.domain') . '/swagger/main/json',
    'rest.swaggerValidatorUrl' => 'http://' . parse_url('http://' . Yii::getAlias('@backend.domain'))['host']
        . ':8888/validate?url=http://' . Yii::getAlias('@rest.domain') . '/swagger/main/json',
];
```