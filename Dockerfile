FROM php:7.4-cli

RUN apt-get update \
    && apt-get install -y iproute2 iputils-ping gawk \
    && apt-get clean

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN useradd chess -u 1000 -m

COPY ./ /app/
COPY ./docker/php.ini /usr/local/etc/php/php.ini

USER    root
WORKDIR /app

ENTRYPOINT ["/app/docker/entrypoint.sh"]