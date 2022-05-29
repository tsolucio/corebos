#!/bin/bash

PHP_VERSION="$1"

if ! [[ "${PHP_VERSION}" =~ 8\.1 ]]; then
  echo "MongoDB extension is only installed from source for PHP 8.1, ${PHP_VERSION} detected."
  exit 0;
fi

set +e

CURRENT_WORKING_DIRECTORY=$(pwd)

cd $TMPDIR
git clone https://github.com/mongodb/mongo-php-driver
cd mongo-php-driver

git submodule deinit --all -f
git submodule update --init
phpize
./configure
make
make install

echo "extension=mongodb.so" > /etc/php/${PHP_VERSION}/mods-available/mongodb.ini

cd $CURRENT_WORKING_DIRECTORY

