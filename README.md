Yii2 base
=====

General requirements
-------------------
PHP 7.3
MySQL 5.7
Git
Composer
Docker - If running via docker


Project setup via .env
-------------------

### How to add configuration to .env file:
* Make .env file
* Move base set of options from .env.example to file you made
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
 Script reads configuration from chosen .env file with [phpdotenv](https://github.com/vlucas/phpdotenv)

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

#### Locally

Clone a project repository

Make a copy of .env.example to .env and fill all the configuration variables

#### Locally via docker

Run `docker-compose up`

On demand run `docker login`

Enter to container `docker exec -it --user=www-data livetag_php bash`

Run `composer install`

Run `php init --env={env} --overwrite=All` (env - environemnt name, like dev, test, etc.)

Run `php yii migrate --no-interactive`

If need run `./yii fixture/load "*"`

#### On GitLab

Get access to repository from admin

Copy variables from .env.example to CI/CD -> Settings -> variables

Configure .gitlab-ci.yml file


###### Functional branches

**develop** - main development branch

**login-by-phone** - User includes phone with ability to sign in/sign up with it

**login-by-username** - User includes username with ability to sign in with it

**centrifugo** - Connect centrifugo service to project with basic chat implementation

**confirm-email** - Add basic functionallity of email confirmation

**ext-filestorage** - File storage based on S3 with optional image resize class

**pdf-generator** - Added service to create pdf from html

**pdf-generator-queue** - Added service to create pdf from html via queue

**GB-120-inApp-subscriptions** - In App purchases and subscriptions base version, for details see `common/components/purchase/README.md`
