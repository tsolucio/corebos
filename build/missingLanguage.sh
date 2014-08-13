#/bin/bash
echo
if [ $1. = "." ]
then
	echo "This script looks for missing language files respect to english which must exist
	echo "It requires one parameter:"
	echo " - language prefix: es_es for example"
	echo
	echo "It must be executed from the root of the coreBOS installation"
	echo "You can read more about it here: http://corebos.org/documentation/doku.php?id=en:devel:translating&#starting_a_new_language"
else
	reffiles=`find . -name "*en_us.lang*"`
	if [ "$reffiles." = "." ]
	then
		echo "English language files cannot be found!!"
	else
		for lng in $reffiles
		do
			dstfile=`echo $lng | sed s/en_us/$1/`
			if [ ! -f $dstfile ]
			then
				echo $dstfile
			fi
		done
		echo "The files above are missing. If there are no files, none are missing :-)"
	fi
fi
echo
