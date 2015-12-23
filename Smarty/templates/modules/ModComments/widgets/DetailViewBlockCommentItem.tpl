{***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved. 
 ************************************************************************************}
<!-- div class="dataField" style="width: 99%; padding-top: 5px;" valign="top" -->
	<table  width="100%" style="font-size:90%;"> <!-- class="dvtCellLabel" -->
	<tr>
	<td width="70%" style="font-weight:normal;">
	{$COMMENTMODEL->content()|@nl2br}
	</td>
	<td align="right" valign="top" style="font-weight:normal;font-color:darkred;">
	{$COMMENTMODEL->timestamp()} <strong>{$COMMENTMODEL->author()}</strong>
	</td>
	</tr>
	</table>
<!-- /div -->
<div class="dataLabel" style="border-bottom: 1px dotted rgb(204, 204, 204); width: 99%; padding-bottom: 5px;" valign="top">
</div>

