Yii2 base
=====

Конфигурирование проекта через .env
-------------------

##### Добавляем конфигурацию в файл .env :
* Создаем файл .env (или другой файл с суффиксом .env - например .dev.env)
* Переносим базовый набор настроек из файла .env.example, в файл который Вы создали (!!ВАЖНО переменные которые присутствуют в .env.example являются обязательными для использования и должны присутствовать в .env или должны быть удалены из конфигурационных файлов *.php среды)
* Для создания новых настроек необходимо использовать шаблон в названии плейсхолдера - {{SOME_KEY}}, вместо которого будет вставлено значение из .env
```
    SOME_KEY=<some value>
```


##### Как это работает:
 Скрипт вычитывает конфигурацию из выбранного .env файла
 При нахождении {{SOME_KEY}}, выполняет замену данного плейсхолдера на значение

> Все плейсхолдеры которые не удалось заменить на значения, в конце работы скрипта будут показаны.
> Файл также используется для `docker-compose`

Детали настройки окружения
-------------------
[Смотреть тут](dev/README.md)

Структура каталогов
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
