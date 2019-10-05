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
build/InstallRESTChanges.php
build/WebserviceVQLParser
include/install/resources/gdinfo.php
include/install/resources/utils.php
include/utils/DBHealthCheck.php
install
install.php
modules/Users/authTypes/adldap_test.php
modules/Migration"
for f in $FILES
do
	if [ -e $f ]
	then
		echo "Deleting $f"
		rm -rf $f
	fi
done

echo
echo "Deactivate all modules you are not using. Besides being more secure the application will be faster."
echo "Optionally you can"
echo
echo "=> Deactivate and eliminate (completely uninstall) these modules:"
echo " - evvtApps  as far as I know nobody is using this"
echo
echo "=> Eliminate the SOAP interface extensions you are not using:"
echo " - soap/customerportal.php  (note: there is a global variable to deactivate this one)"
echo " - soap/vtigerolservice.php  (I don't think this one is used at all, it is all done with webservice now)"
echo
echo "=> Deactivate webservice access with the Webservice_Enabled global variable if you are not using this interface."
echo
echo "=> If you are not using the OmniPay Payment Gateway you can delete the Pay.php script"
echo
