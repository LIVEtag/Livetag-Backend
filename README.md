Yii2 base
=====

General requirements
-------------------
PHP 7.1+
MySQL 5.6+
Git
Composer
Docker - If running via docker


Project setup via .env
-------------------

### How to add configuration to .env file:
* Make .env file (or other file with .env suffix - like .dev.env)
* Move base set of options from .env.example to file you made (!!IMPORTANT variables present in .env.example are required and must be in .env file or have to be removed from *.php environment configuration )
* To create new configuration option use template in placeholder's name - {{SOME_KEY}}, will be replaced with value from .env file
* !!!IMPORTANT Do not use quotes in .env file
```
    SOME_KEY=<some value>
```
##### Most common .env variables
```
    MAIN_DOMAIN= application domain
    REST_DOMAIN= API domain
    BACKEND_DOMAIN= admin domain
    DEPLOY_DOMAIN= domain to deploy remotely 
    DEPLOY_USER= deploy user when deploy remotely
    DEPLOY_PORT=deploy port when deploy remotely
    DEPLOY_KEY_PATH= deploy key when deploy remotely
    DEPLOY_PATH= deploy path when deploy remotely
    DB_USERNAME= db username
    DB_PASSWORD= db password
    DB_NAME= db name
    DB_HOST= db host
    DB_PORT= db post
    DB_ROOT_PASSWORD= db password when need more permissions (default root username  is root)
    DB_TEST_HOST= test env host
    DB_TEST_NAME= test env name
    DB_TEST_PORT= test env port

```


### How this works?:
 Script reads configuration from chosen .env file
 On finding {{SOME_KEY}}, replacing this placeholder with value
 

> All placeholders unable to replace will be shown at the end of work
> File used for `docker-compose` also

Environment configuration details
-------------------
[See here](dev/README.md)

Environmental variables details for Gitlab CI
-------------------
[See here](.gitlab-ci/README.md)

Swagger usage
-------------------
[See here](swagger-ui/README.md)

GIT Workflow
-------------------
[See here](https://wiki.gbksoft.net/git:workflow:prod).



Installation instructions
-------------------

#### Locally (not recommended)

Clone a project repository

Make a copy of .env.example to .env and fill all the configuration variables

Run `php init --env={env} --overwrite=All` (env - environemnt name, like dev, test, etc.)

Run `composer install`

Run `php yii migrate --no-interactive`

#### Locally via docker

Clone a project repository

Make a copy of .env.example to .env and fill all the configuration variables

Run `docker-compose up`

On demand run `docker login`

#### On GitLab

Get access to repository from admin

Copy variables from .env.example to CI/CD -> Settings -> variables

Configure .gitlab-ci.yml file


