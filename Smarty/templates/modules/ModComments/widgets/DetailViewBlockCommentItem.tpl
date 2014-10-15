{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<div class="dataField" style="width: 99%; padding-top: 10px;" valign="top">
	{$COMMENTMODEL->content()|@nl2br}
</div>
<div class="dataLabel" style="border-bottom: 1px dotted rgb(204, 204, 204); width: 99%; padding-bottom: 5px;" valign="top">
	<font color="darkred">
		{$MOD.LBL_AUTHOR}: {$COMMENTMODEL->author()} {$MOD.LBL_ON} {$COMMENTMODEL->timestamp()}
	</font>
</div>