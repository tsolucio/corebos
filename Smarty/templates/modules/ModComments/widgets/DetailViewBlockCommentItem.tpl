{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
<div class="dataField" style="padding-top: 10px; display: none;" valign="top">
<p style="word-break: break-all;">
	{$COMMENTMODEL->content()|@nl2br}
</p>

</div>
<div class="dataLabel" style="border-bottom: 1px dotted rgb(204, 204, 204);padding-bottom: 5px;" valign="top">
	<font color="darkred">

		{$MOD.LBL_AUTHOR}: {$COMMENTMODEL->author()} {$MOD.LBL_ON_DATE} {$COMMENTMODEL->timestamp()}

	</font>
</div>