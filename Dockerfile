ARG REGISTRY_PUBLIC=docker.io

FROM ${REGISTRY_PUBLIC}/composer/composer:2-bin AS composer_upstream
FROM ${REGISTRY_PUBLIC}/library/php:8.3-fpm-alpine AS php_upstream
FROM ${REGISTRY_PUBLIC}/mlocati/php-extension-installer:latest AS mlocati


# Установка зависимостей для работы приложения
FROM php_upstream AS app_base

USER root

WORKDIR /var/www/html

COPY --from=mlocati --link /usr/bin/install-php-extensions /usr/local/bin/

RUN apk update && \
    apk add \
      git \
      acl \
      protoc \
      protobuf-dev \
      bash \
      supervisor \
  ;

RUN set -eux; \
    install-php-extensions \
        xml \
        apcu \
        intl \
        opcache \
        zip \
        memcached \
        pgsql \
        pdo_pgsql \
        pdo_mysql \
        xsl \
        http \
        sockets \
        xdebug \
        redis \
        pcntl \
    ;

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer_upstream --link /composer /usr/bin/composer

COPY --link --chmod=755 ./docker/php-fpm/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link ./docker/supervisord/supervisord.conf /etc/supervisor/conf.d/worker.conf

RUN chmod +x /usr/local/bin/docker-entrypoint

FROM app_base AS app

ARG APP_ENV=Development

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY --link docker/php-fpm/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

COPY --link . ./

RUN set -eux; \
    if [ -f composer.json ]; then \
        composer install; \
        composer clear-cache; \
    fi

RUN rm -Rf docker/

ENTRYPOINT ["docker-entrypoint"]
