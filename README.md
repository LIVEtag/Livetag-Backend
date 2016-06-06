================= Робота з файлом .env ===================== 
Добавляємо конфігурацию в файл .env:
1) создаємо файл .env (або любий файл з розширенням .env, наприклад .dev.env)
2) копіруємо переменні з файла .env.example в ваш файл.
УВАГА. Перемінні в файлі .env.example є бажаними для заповлення.
3) при созданії нових перемінних використовуйте шаблон {{SOME_KEY}}
(лише букви в верхнему регістрі і підчеркнування), также не забудьте 
добавити переменну в ваш .env
```
    SOME_KEY="some value"
```
Принцип роботи. 
Коли скрипт знаходить шаблон пр. {{SOME_KEY}}, він починає шукати файли з 
розширенням .env. Якщо файл лише 1 то він буде вибраний по умовчанню, якщо їх більше 1 
то попросить вибрати конкретний файл для конфігурації. Наприклад:  
```
  [0] => .dev.env
  [1] => .env

    Your choice [0-1, or "q" to quit]
```
можна зробити exit по команді "q"
УВАГА. В випадку неправильного вибора, виведется ошибка:
```
    Wrong answer!!! Try again 
```
Якщо значення перемінної не буде знайдено в файлі .env то після роботи скрипта 
незнайдені ключи виведутся в консоль. Якщо файл .env не буде знайдено, то виведутся всі незнайдені ключи. 
Зразок:
```
    Your application may not work correctly! 
    Next agrument not found: 
        {{DB_PASSWORD}}
    Recommended add this agrument to your file .env or create file
```
============================================================


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
