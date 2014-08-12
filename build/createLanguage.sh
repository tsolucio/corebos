#/bin/bash
echo
if [ $1. = "." -o $2. = "." ]
then
	echo "This script creates a new language from and existing one"
	echo "It requires two parameters:"
	echo " - reference language prefix: es_es for example"
	echo " - destination language prefix: it_it for example"
	echo
	echo "It must be executed from the root of the coreBOS installation"
	echo "You can read more about it here: http://corebos.org/documentation/doku.php?id=en:devel:translating&#starting_a_new_language"
else
	reffiles=`find . -name "*$1*"`
	if [ "$reffiles." = "." ]
	then
		echo "Reference language files cannot be found"
	else
		cnt=`find . -name "*$1*" | wc -l`
		if [ $cnt -lt 65 ]
		then
			echo "Low number of reference files: $cnt. Your reference language is probably missing some translations!"
		else
			for lng in $reffiles
			do
				dstfile=`echo $lng | sed s/$1/$2/`
				if [ ! -f $dstfile ]
				then
					cp $lng $dstfile
				fi
			done
		fi
	fi
fi
echo
