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
var userFirstDayOfWeek = {$USER_FIRST_DOW};
var userCurrencySeparator = "{$USER_CURRENCY_SEPARATOR}";
var userDecimalSeparator = "{$USER_DECIMAL_FORMAT}";
var userNumberOfDecimals = "{$USER_NUMBER_DECIMALS}";
var gVTuserLanguage = "{$USER_LANGUAGE}";
if (typeof(Storage) !== "undefined") {ldelim}
	var corebos_browsertabID = sessionStorage.corebos_browsertabID ? sessionStorage.corebos_browsertabID : sessionStorage.corebos_browsertabID = Math.random().toString().substring(2);
	window.addEventListener('beforeunload', function(event) {ldelim}
		document.cookie = "corebos_browsertabID="+corebos_browsertabID;
	{rdelim});
{rdelim}
</script>
