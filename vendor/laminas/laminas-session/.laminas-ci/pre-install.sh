#!/bin/bash

WORKING_DIRECTORY=$2
JOB=$3
PHP_VERSION=$(echo "${JOB}" | jq -r '.php')

${WORKING_DIRECTORY}/.laminas-ci/install-mongodb-extension-from-source.sh "${PHP_VERSION}" || exit 1
