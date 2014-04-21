#!/bin/sh
if [ "$1." = "." ]
then
  echo
  echo Missing parameter package name
  echo
  echo "pack.sh ModuleName"
  echo
  exit
fi
DIR=$(dirname $(readlink -f $0))
cd $DIR
if [ -d $1 ]
then
	if [ -f $1/manifest.xml -a -d $1/modules ]
	then
		if [ -f $1.zip ]
		then
			rm $1.zip
		fi
		cd $1
		mname=$(basename $1)
		zip -r -x "*.svn*" "*schema.xml" "*~" @ ../$mname.zip .
		cd $DIR
	else
		echo "$1 is not a Package directory"
	fi
else
	echo "$1 is not a directory"
fi
