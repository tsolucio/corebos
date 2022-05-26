FROM alpine:3.15.4

RUN mkdir /www

RUN mkdir -p /run/php

RUN apk add bash php7-fpm php7-cli php7-phar php7-tokenizer php7-simplexml php7-imap php7-xmlwriter php7-mbstring php7-dom

COPY . /www

RUN apk update
RUN apk upgrade
RUN apk add --update curl openssl

RUN chmod +x /www/docker/phpcs.phar
RUN cp /www/docker/phpcs.phar /usr/local/bin

RUN apk add --update nodejs npm
RUN npm config set unsafe-perm true
RUN npm install -g eslint

WORKDIR /www
