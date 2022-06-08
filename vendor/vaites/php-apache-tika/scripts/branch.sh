#!/usr/bin/env bash

rm -rf vendor
rm -f composer.lock
git checkout $1
composer install