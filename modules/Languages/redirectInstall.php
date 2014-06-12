<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Pius Tschümperlin ep-t.ch
 ********************************************************************************/

require_once('modules/Languages/Config.inc.php');
require_once('config.php');
require_once('include/database/PearDatabase.php');

$line_break=chr(10);

/* add db table
  *	
  */
$adb->createTables("schema/ModLanguageSchema.xml");

/* add language pack en_us to db	
  *	INSERT INTO `vtiger_languages` VALUES (1, 'en_us', 'UTF-8', 'US English', 'v5.0.4 Validation', 'Vtiger Core Team', ' * The contents of this file are subject to the SugarCRM Public License Version 1.1.2\r\n * ("License"); You may not use this file except in compliance with the\r\n * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL\r\n * Software distributed under the License is distributed on an  "AS IS"  basis,\r\n * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for\r\n * the specific language governing rights and limitations under the License.\r\n * The Original Code is:  SugarCRM Open Source\r\n * The Initial Developer of the Original Code is SugarCRM, Inc.\r\n * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;\r\n * All Rights Reserved.', '2007-09-30', '2007-09-30 00:00:00');
  */
$dbQuery="SELECT * FROM vtiger_languages";
$result = $adb->query($dbQuery);
$row = $adb->fetch_array($result);
if(!$row) $adb->query( "INSERT INTO `vtiger_languages` VALUES (1, 'en_us', 'UTF-8', 'US English', 'v5.0.4 Validation', 'Vtiger Core Team', ' * The contents of this file are subject to the SugarCRM Public License Version 1.1.2\r\n * (\"License\"); You may not use this file except in compliance with the\r\n * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL\r\n * Software distributed under the License is distributed on an  \"AS IS\"  basis,\r\n * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for\r\n * the specific language governing rights and limitations under the License.\r\n * The Original Code is:  SugarCRM Open Source\r\n * The Initial Developer of the Original Code is SugarCRM, Inc.\r\n * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;\r\n * All Rights Reserved.', '2007-09-30', '2007-09-30 00:00:00', 3);");

/* modules/Settings/language/en_us.lang.php
 * add to the array mod_strings:
 
  	'LBL_LANGUAGES'=>'Edit Language Pack',
  	'LBL_LANGUAGES_DESCRIPTION'=>'Manage Languages Packs',
 * 
 */
	$filename='modules/Settings/language/en_us.lang.php';
	if(file_exists($filename) && is_writable($filename)){
		$ConfigfileContent = file_get_contents($filename,FILE_TEXT);
		
		if (!preg_match('/LBL_LANGUAGES/',$ConfigfileContent)) {
			$add = $line_break.$line_break."//Added for Module Languages".
				   $line_break."'LBL_LANGUAGES'=>'Edit Language Pack',".
				   $line_break."'LBL_LANGUAGES_DESCRIPTION'=>'Manage Language Packs',".
				   $line_break;
			
			$ConfigfileContent = preg_replace('/(mod_strings\s*=\s*array.*[\'"]),?\s*(\);)/is', "$1,".$add."$2", $ConfigfileContent);
			
			if(file_exists($filename) && is_writable($filename)){
					if ($make_backups == true) {
						@unlink($filename.'.bak');
						@copy($filename, $filename.'.bak');
					}
					$fd = fopen($filename, 'w');
					fwrite($fd, $ConfigfileContent);
					fclose($fd);
			}
		}
	}

/* Smarty/templates/SetMenu.tpl
  * add after
  *	{if  $smarty.request.action eq 'PickList' ||  $smarty.request.action eq 'SettingsAjax'}
  *		<tr><td class="settingsTabSelected" nowrap><a href="index.php?module=Settings&action=PickList&parenttab=Settings">{$MOD.LBL_PICKLIST_EDITOR}</a></td></tr>						     {else}
  *		<tr><td class="settingsTabList" nowrap><a href="index.php?module=Settings&action=PickList&parenttab=Settings">{$MOD.LBL_PICKLIST_EDITOR}</a></td></tr>
  *	{/if}

	{if $smarty.request.action eq 'ListPackages'}
		<tr><td class="settingsTabSelected" nowrap><a href="index.php?module=Languages&action=ListPackages&parenttab=Settings">{$MOD.LBL_LANGUAGES}</a></td></tr>
	{else}
		<tr><td class="settingsTabList" nowrap><a href="index.php?module=Languages&action=ListPackages&parenttab=Settings">{$MOD.LBL_LANGUAGES}</a></td></tr>
	{/if}
	
 * 
 */
	$filename='Smarty/templates/SetMenu.tpl';
	if(file_exists($filename) && is_writable($filename)){
		$ConfigfileContent = file_get_contents($filename,FILE_TEXT);
		
		if (!preg_match('/\'ListPackages\'/',$ConfigfileContent)) {
			$add = $line_break.$line_break.'{if $smarty.request.action eq \'ListPackages\'}'.
		           $line_break.'<tr><td class="settingsTabSelected" nowrap><a href="index.php?module=Languages&action=ListPackages&parenttab=Settings">{$MOD.LBL_LANGUAGES}</a></td></tr>'.
		           $line_break.'	{else}'.
		           $line_break.'<tr><td class="settingsTabList" nowrap><a href="index.php?module=Languages&action=ListPackages&parenttab=Settings">{$MOD.LBL_LANGUAGES}</a></td></tr>'.
	               $line_break.'{/if}';
			
			$pattern = '/({if\s*\$smarty.request.action\s*eq\s*\'PickList\'.*\s*.*\s*.*\s*{\/if})/i';
			
			$ConfigfileContent = preg_replace($pattern, "$1".$add, $ConfigfileContent);
			
			if(file_exists($filename) && is_writable($filename)){
					if ($make_backups == true) {
						@unlink($filename.'.bak');
						@copy($filename, $filename.'.bak');
					}
					$fd = fopen($filename, 'w');
					fwrite($fd, $ConfigfileContent);
					fclose($fd);
			}
		}
	}
	
/* Smarty/templates/Settings.tpl
  * add after
  * 		<td width=25% valign=top>
  *			<!-- icon 10-->
  *			<table border=0 cellspacing=0 cellpadding=5 width=100%>
  *				<tr>
  *
  *					<td rowspan=2 valign=top><a href="index.php?module=Settings&action=PickList&parenttab=Settings"><img border=0 src="{$IMAGE_PATH}picklist.gif" alt="{$MOD.LBL_PICKLIST_EDITOR}" title="{$MOD.LBL_PICKLIST_EDITOR}"></a></td>
  *					<td class=big valign=top><a href="index.php?module=Settings&action=PickList&parenttab=Settings">{$MOD.LBL_PICKLIST_EDITOR}</a></td>
  *				</tr>
  *				<tr>
  *					<td class="small" valign=top>{$MOD.LBL_PICKLIST_DESCRIPTION}</td>	
  *				</tr>
  *			</table>
  *		</td>

                      <td width=25% valign=top>
			<table border=0 cellspacing=0 cellpadding=5 width=100%>
				<tr>
					<td rowspan=2 valign=top><a href="index.php?module=Languages&action=ListPackages&parenttab=Settings"><img src="{$IMAGE_PATH}languages.gif" alt="{$MOD.LBL_LANGUAGES}" width="48" height="48" border=0 title="{$MOD.LBL_LANGUAGES}"></a></td>
					<td class=big valign=top><a href="index.php?module=Languages&action=ListPackages&parenttab=Settings">{$MOD.LBL_LANGUAGES}</a></td>
				</tr>
				<tr>
					<td class="small" valign=top>{$MOD.LBL_LANGUAGES_DESCRIPTION}</td>
				</tr>
			</table> 
		</td>

 * 
 */
	$filename='Smarty/templates/Settings.tpl';
	if(file_exists($filename) && is_writable($filename)){
		$ConfigfileContent = file_get_contents($filename,FILE_TEXT);
		
		if (!preg_match('/LBL_LANGUAGES_DESCRIPTION/',$ConfigfileContent)) {
			$add = $line_break.$line_break.'                      <td width=25% valign=top>'.
		           $line_break.'			<table border=0 cellspacing=0 cellpadding=5 width=100%>'.
		           $line_break.'				<tr>'.
		           $line_break.'					<td rowspan=2 valign=top><a href="index.php?module=Languages&action=ListPackages&parenttab=Settings"><img src="{$IMAGE_PATH}languages.gif" alt="{$MOD.LBL_LANGUAGES}" width="48" height="48" border=0 title="{$MOD.LBL_LANGUAGES}"></a></td>'.
	               $line_break.'					<td class=big valign=top><a href="index.php?module=Languages&action=ListPackages&parenttab=Settings">{$MOD.LBL_LANGUAGES}</a></td>'.
	               $line_break.'				</tr>'.
	               $line_break.'				<tr>'.
	               $line_break.'					<td class="small" valign=top>{$MOD.LBL_LANGUAGES_DESCRIPTION}</td>'.
	               $line_break.'				</tr>'.
	               $line_break.'			</table>'.
	               $line_break.'		</td>';
			
			$pattern = '/({\$MOD.LBL_PICKLIST_DESCRIPTION}<\/td>\s*<\/tr>\s*<\/table>\s*<\/td>)/i';
			
			$ConfigfileContent = preg_replace($pattern, "$1".$add, $ConfigfileContent);
			
			if(file_exists($filename) && is_writable($filename)){
					if ($make_backups == true) {
						@unlink($filename.'.bak');
						@copy($filename, $filename.'.bak');
					}
					$fd = fopen($filename, 'w');
					fwrite($fd, $ConfigfileContent);
					fclose($fd);
			}
		}
	}
 
	header("Location:index.php?module=Languages&action=ListPackages&parenttab=Settings");
?>