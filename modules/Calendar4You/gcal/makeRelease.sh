#!/bin/bash

VERSION=0.6.7
TMPDIR=/tmp/google-api-php-client
RELFILE=/tmp/google-api-php-client-${VERSION}.tar.gz

rm -f $RELFILE
rm -rf $TMPDIR
mkdir $TMPDIR
cp -r * $TMPDIR
cd $TMPDIR
find . -name ".*" -exec rm -rf {} \; &>/dev/null
find . -name "makeRelease.sh" -exec rm -rf {} \; &>/dev/null
find . -name "local_*" -exec rm -rf {} \; &>/dev/null
find . -name "static" -exec rm -rf {} \; &>/dev/null
find . -name ".idea" -exec rm -rf {} \; &>/dev/null
find . -name ".svn" -exec rm -rf {} \; &>/dev/null
cd ..
tar c google-api-php-client | gzip > $RELFILE
rm -rf $TMPDIR
