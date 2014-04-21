#!/bin/sh
if [ "$1." = "." -o "$2." = "." ]
then
  echo
  echo Missing parameters language code or package name
  echo
  echo "pack_lang.sh es_es Spanish"
  echo
  exit
fi
cd ..
if [ -f vtigerversion.php -a -d modules ]
then
	if [ -f build/$2/manifest.xml ]
	then
		pck=build/$2.zip
		if [ -f $pck ]
		then
			rm $pck
		fi
		shortcode=$(echo es_es | cut -c-2)
		langphpfiles=$(find . -name $1.lang.php)
		langjsfiles=$(find . -name $1.lang.js)
		cp build/$2/manifest.xml .
		zip -r -x "*.svn*" "*schema.xml" "*~" @ $pck manifest.xml jscalendar/calendar-setup.js jscalendar/lang/calendar-$shortcode.js modules/Emails/language/phpmailer.lang-$1.php $langphpfiles $langjsfiles
		rm manifest.xml
		echo
		echo Language package prepared in $pck
	else
		echo
		echo "Language pack manifest.xml file not found!"
		echo "It should be in build/$2/manifest.xml"
	fi
else
	echo
	echo "coreBOS root directory not found!"
	echo "This script must be executed from inside the build directory hanging from the root of your coreBOS install."
fi
cd build
echo
