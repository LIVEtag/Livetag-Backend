# Swagger UI
Swagger UI теперь не является частью основного проекта, также в CI системе он доступен по отдельному URL,
а deploy выполняется отдельной задачей только для Dev и Test окружений.
Сам файл json берется из развернутого проекта соответствующего окружения.
Детали смотреть в файле `.gitlab-ci.yml` (задачи `dev:swagger` и `test:swagger`)
Для локального доступа для Swagger UI подымается отдельный контейнер,
который доступен на порту `:8080` (конфигурацию можно посмотреть в `docker-compose.yml`),
а сам файл json берется из также из развернутого проекта.

## Documentation

#### Usage
- [Installation](docs/usage/installation.md)
- [Configuration](docs/usage/configuration.md)
- [CORS](docs/usage/cors.md)
- [OAuth2](docs/usage/oauth2.md)
- [Deep Linking](docs/usage/deep-linking.md)
- [Limitations](docs/usage/limitations.md)
- [Version detection](docs/usage/version-detection.md)

#### Customization
- [Overview](docs/customization/overview.md)
- [Plugin API](docs/customization/plugin-api.md)
- [Custom layout](docs/customization/custom-layout.md)
