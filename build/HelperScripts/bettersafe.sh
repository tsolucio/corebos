FILES="build/cbFooter.inc
build/cbHeader.inc
build/code_license.tpl
build/coreBOSTests
build/createLanguage.sh
build/HelperScripts
build/migrate6
build/migrate_from_vt6.php
build/missingLanguage.sh
build/oo-merge
build/tests
build/InstallRESTChanges.php
build/WebserviceVQLParser
include/php_writeexcel
include/prototype-1.4.0
include/scriptaculous
include/Zend
Image
modules/Migration"
for f in $FILES
do
	echo "Deleting $f"
	rm -rf $f
done

echo
echo "Deactivate all modules you are not using. Besides being more secure the application will be faster."
echo "Optionally you can deactivate and eliminate (completely uninstall) these modules:"
echo " - FieldFormulas  this module is totally obsolete > move any rules you have there to workflows"
echo " - evvtApps  as far as I know nobody is using this"
echo