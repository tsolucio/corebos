#!/usr/bin/env bash

SOURCE=$(dirname $0)
COMPOSER="$SOURCE/../composer.json"
LATEST=$(php -r "echo array_pop(json_decode(file_get_contents('$COMPOSER'), true)['extra']['supported-versions']);")

BINARIES=${APACHE_TIKA_BINARIES:-bin}
VERSION=${APACHE_TIKA_VERSION:-$LATEST}
MIRROR="https://archive.apache.org"

mkdir --parents $BINARIES

if [ ! -f "$BINARIES/tika-app-$VERSION.jar" ]; then
    wget "$MIRROR/dist/tika/tika-app-$VERSION.jar" -O "$BINARIES/tika-app-$VERSION.jar"
fi

if [ ! -f "$BINARIES/tika-server-$VERSION.jar" ]; then
    wget "$MIRROR/dist/tika/tika-server-$VERSION.jar" -O "$BINARIES/tika-server-$VERSION.jar"
fi
