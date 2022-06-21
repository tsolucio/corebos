#!/usr/bin/env bash

set -e

SOURCE=$(dirname $0)
COMPOSER="$SOURCE/../composer.json"
LATEST=$(php -r "echo array_pop(json_decode(file_get_contents('$COMPOSER'), true)['extra']['supported-versions']);")

BINARIES=${APACHE_TIKA_BINARIES:-bin}
VERSION=${APACHE_TIKA_VERSION:-$LATEST}
MIRROR="https://archive.apache.org"

mkdir --parents $BINARIES

if [[ "$VERSION" =~ ^1.(26|27|28) ]]; then
    APP_URL="$MIRROR/dist/tika/$VERSION/tika-app-$VERSION.jar"
    SERVER_URL="$MIRROR/dist/tika/$VERSION/tika-server-$VERSION.jar"
elif [[ "$VERSION" =~ ^1 ]]; then
    APP_URL="$MIRROR/dist/tika/tika-app-$VERSION.jar"
    SERVER_URL="$MIRROR/dist/tika/tika-server-$VERSION.jar"
else
    APP_URL="$MIRROR/dist/tika/$VERSION/tika-app-$VERSION.jar"
    SERVER_URL="$MIRROR/dist/tika/$VERSION/tika-server-standard-$VERSION.jar"
fi

if [ ! -f "$BINARIES/tika-app-$VERSION.jar" ]; then
    wget "$APP_URL" -nv -O "$BINARIES/tika-app-$VERSION.jar"
fi

if [ ! -f "$BINARIES/tika-server-$VERSION.jar" ]; then
    wget "$SERVER_URL" -nv -O "$BINARIES/tika-server-$VERSION.jar"
fi