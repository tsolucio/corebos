FROM alpine:3.7

RUN mkdir /www

RUN mkdir -p /run/php

RUN apk add bash php7-fpm php7-cli php7-phar php7-tokenizer php7-simplexml

COPY . /www

RUN apk update
RUN apk upgrade
RUN apk add --update curl openssl

RUN chmod +x /www/docker/phpcs.phar
RUN cp /www/docker/phpcs.phar /usr/local/bin

RUN apk add --update nodejs nodejs-npm

RUN npm install -g eslint

WORKDIR /www
