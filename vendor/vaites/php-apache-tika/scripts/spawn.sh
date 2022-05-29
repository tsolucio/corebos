#!/usr/bin/env bash

set -e

SOURCE=$(dirname $0)
COMPOSER="$SOURCE/../composer.json"
LATEST=$(php -r "echo array_pop(json_decode(file_get_contents('$COMPOSER'), true)['extra']['supported-versions']);")

PORT=${APACHE_TIKA_PORT:-9998}
BINARIES=${APACHE_TIKA_BINARIES:-bin}
VERSION=${APACHE_TIKA_VERSION:-$LATEST}

{
    java --add-modules java.se.ee -version > /dev/null &&
    JAVA='java --add-modules java.se.ee'
} || {
    JAVA='java'
}

if [ $(ps aux | grep -c tika-server-$VERSION) -lt 2 ]; then
    $JAVA -version

    if [[ "$VERSION" =~ ^1 ]]; then
        COMMAND="$JAVA -jar $BINARIES/tika-server-$VERSION.jar -p $PORT -enableUnsecureFeatures -enableFileUrl"
    else
        COMMAND="$JAVA -jar $BINARIES/tika-server-$VERSION.jar -p $PORT"
    fi

    if [ "$1" == "--foreground" ]; then
        echo "Starting Tika Server $VERSION in foreground"
        $COMMAND
    else
        echo "Starting Tika Server $VERSION in background"
        $COMMAND 2> /tmp/tika-server-$VERSION.log &
        sleep 5
    fi
else
    echo "Tika Server $VERSION already running"
fi
