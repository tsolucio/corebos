#!/bin/bash
#
# Este script necessita per a funcionar en Debian que estiguen
# instalats els paquets openoffice.org-writer/libreoffice-writer,
# openoffice.org-java-common/libreoffice-java-common, ure i
# default-jdk.
#

# Paths
URE_PATH=/usr/lib/ure
OOO_PATH=/usr/lib/openoffice/basis-link/program
LO_PATH=/usr/lib/libreoffice/program
GENDOC_PATH=$(readlink -f "$(dirname "$0")")

CLASSPATH=$GENDOC_PATH:$URE_PATH/share/java/juh.jar:$URE_PATH/share/java/jurt.jar:$URE_PATH/share/java/ridl.jar:$OOO_PATH/classes/unoil.jar:$LO_PATH/classes/unoil.jar

# Compile class if it doesn't exist
if [ ! -f "$GENDOC_PATH/UnoService.class" -o "$GENDOC_PATH/UnoService.java" -nt "$GENDOC_PATH/UnoService.class" ]
then
  javac -cp $CLASSPATH $GENDOC_PATH/UnoService.java
fi

# Execute
cd $GENDOC_PATH
java -cp $CLASSPATH UnoService "$@"
