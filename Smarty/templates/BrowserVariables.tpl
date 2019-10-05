{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
 ********************************************************************************/
-->*}
<script>
var gVTModule = "{$GVTMODULE}";
var gVTTheme  = "{$THEME}";
var gVTUserID = "{$CURRENT_USER_ID}";
var default_charset = "{$DEFAULT_CHARSET}";
var userDateFormat = "{$USER_DATE_FORMAT}";
var userHourFormat = "{$USER_HOUR_FORMAT}";
var userFirstDayOfWeek = {$USER_FIRST_DOW};
var userCurrencySeparator = "{$USER_CURRENCY_SEPARATOR}";
var userDecimalSeparator = "{$USER_DECIMAL_FORMAT}";
var userNumberOfDecimals = "{$USER_NUMBER_DECIMALS}";
var gVTuserLanguage = "{$USER_LANGUAGE}";
var gServiceWorkermd5 = '{$SW_MD5}';
var goldcorebos_browsertabID = '{$corebos_browsertabID}';
if (typeof(Storage) !== "undefined") {ldelim}
	if (sessionStorage.corebos_browsertabID) {
		var corebos_browsertabID = sessionStorage.corebos_browsertabID;
	} else {
		sessionStorage.corebos_browsertabID = Math.random().toString().substring(2);
		var corebos_browsertabID = sessionStorage.corebos_browsertabID;
		fetch('index.php?module=Utilities&action=UtilitiesAjax&file=ExecuteFunctions&functiontocall=updateBrowserTabSession&newtabssid=' + corebos_browsertabID + '&oldtabssid=' + goldcorebos_browsertabID, {
			credentials: 'same-origin'
		});
	}
	window.addEventListener('beforeunload', function(event) {ldelim}
		document.cookie = "corebos_browsertabID="+corebos_browsertabID;
	{rdelim});
{rdelim}
</script>
