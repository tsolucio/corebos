<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module       : EtiquetasOO
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
include_once 'config.inc.php';
require_once 'include/logging.php';
require_once 'include/utils/utils.php';
require_once 'modules/evvtgendoc/compile.php';
require_once 'Smarty_setup.php';

function outputOOModuleHeader($module) {
	$i18n = getTranslatedString($module, $module);
	$icon = getModuleIcon($module);
	echo '<div class="slds-page-header" style="width:100%">
<div class="slds-page-header__row">
<div class="slds-p-right_medium">
<div class="slds-media">
<div class="slds-media__figure">
<span class="slds-icon_container slds-icon-standard-contact" title="'.$i18n.'">
<svg class="slds-icon slds-page-header__icon" aria-hidden="true">
<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="include/LD/assets/icons/'.$icon['__ICONLibrary'].'-sprite/svg/symbols.svg#'.$icon['__ICONName'].'"></use>
</svg>
<span class="slds-assistive-text">'.$i18n.'</span>
</span>
</div>
<div class="slds-media__body">
<div class="slds-page-header__name">
<div class="slds-page-header__name-title">
<span class="slds-page-header__title slds-truncate" title="'.$i18n.'">'.$i18n.'</span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>';
}

function outputOONoIconHeader($i18n) {
	echo '<div class="slds-page-header" style="width:100%">
<div class="slds-page-header__row">
<div class="slds-p-right_medium">
<div class="slds-media__body">
<div class="slds-page-header__name">
<div class="slds-page-header__name-title">
<span class="slds-page-header__title slds-truncate" title="'.$i18n.'">'.$i18n.'</span>
</div>
</div>
</div>
</div>
</div>
</div>';
}

$smarty = new vtigerCRM_Smarty();

echo '
<script src="modules/EtiquetasOO/clipboard.min.js"></script>
<script>
function go_tab(obj){
	location.href = "#"+obj.options[obj.selectedIndex].value;
}
</script>';
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
include 'modules/cbupdater/forcedButtons.php';
$smarty->assign('CHECK', $tool_buttons);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
$smarty->display('Buttons_List.tpl');
echo '<div id="oocontainer" style="width:96%;margin: 5px auto">';
if (isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') {
	$current_language = $_SESSION['authenticated_user_language'];
} else {
	$current_language = $default_language;
}
//set module and application string arrays based upon selected language
$app_strings = return_application_language($current_language);
$app_list_strings = return_app_list_strings_language($current_language);
$eoo_strings = return_module_language($current_language, $currentModule);

$SQL = "SELECT tabid, name, tablabel FROM vtiger_tab WHERE name='Users' or isentitytype";
$res_tab = $adb->query($SQL);
$num_tabs = $adb->num_rows($res_tab);
$arr_options = array();
echo '<div class="slds-form-element__control slds-card">
<div class="slds-select_container" style="width:90%;margin: 5px auto;">
	<select name="oomodule" id="oomodule" onchange="go_tab(this);" class="slds-select slds-page-header__meta-text">';
while ($tab = $adb->fetch_array($res_tab)) {
	if ($tab['tablabel'] == 'PBXManager') {
		continue;
	}
	$etiq_tab = (empty($app_strings[$tab['tablabel']]) ? $tab['tablabel'] : $app_strings[$tab['tablabel']] );
	$arr_options[$tab['name']] = $etiq_tab;
}
foreach ($special_modules as $sp_key => $sp_value) {
	$etiq_tab = $sp_key;
	$arr_options[$sp_key] = $etiq_tab;
}
array_multisort($arr_options);
echo '<option value="top">'.$eoo_strings['ElijaModulo'].'</option>';
echo '<option value="speciallabels">**'.$eoo_strings['SpecialVars'].'**</option>';
foreach ($arr_options as $op_key => $op_value) {
	echo '<option value="'.$op_key.'">'.$op_value.'</option>';
}
echo '</select></div></div>';
echo '<hr>';
$btnclose = '<img src="modules/EtiquetasOO/copyclipboard.png" alt="'.getTranslatedString('Copy', 'EtiquetasOO').'" style="height:12px;vertical-align:middle"></button>';
$res_tab = $adb->query($SQL);
echo '<ul>';
while ($tab = $adb->fetch_array($res_tab)) {
	if ($tab['tablabel'] == 'PBXManager') {
		continue;
	}
	$sp_tab[$tab['name']] = $tab['tabid'];
	$mod_strings = return_module_language($current_language, $tab['name']);
	$etiq_tab = (empty($app_strings[$tab['tablabel']]) ? $tab['tablabel'] : $app_strings[$tab['tablabel']] );
	echo '<li><a name="'.$tab['name'].'"></a>';
	outputOOModuleHeader($tab['name']);
	echo '<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-card">';
	$SQL_FIELDS = 'SELECT fieldname,fieldlabel FROM vtiger_field WHERE presence IN (0,2) AND tabid=?';
	$res_field = $adb->pquery($SQL_FIELDS, array($tab['tabid']));
	while ($field = $adb->fetch_array($res_field)) {
		$etiqueta = (empty($mod_strings[$field['fieldlabel']]) ? $field['fieldlabel'] : $mod_strings[$field['fieldlabel']]);
		echo '<tr class="slds-hint-parent slds-page-header__meta-text" data-clipboard-text="{'.$tab['name'].'.'.$field['fieldname'].'}"><td style="width:350px;"><b>'.$etiqueta.'</b>:</td><td>{'.$tab['name'].'.'.$field['fieldname'].'}';
		echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$tab['name'].'.'.$field['fieldname'].'}">' . $btnclose;
		echo '</td></tr>';
	}
	echo '</table>';
	echo '<div class="slds-notify_alert slds-theme_info slds-theme_alert-texture slds-text-heading_small slds-m-bottom_xx-small slds-m-top_xx-small" style="justify-content: unset;">'
		.$eoo_strings['RelatedModules'].'</div>';
	echo '<ul>';
	if (array_key_exists($tab['name'], $related_module)) {
		echo '<li>';
		echo '<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-card">';
		echo '<thead><th colspan="2" class="slds-page-header__meta-text">'.$eoo_strings['OneEntity'].'</th></thead><tbody>';
		foreach ($related_module[$tab['name']] as $rel_key => $rel_value) {
			$etiq_reltab = (empty($app_strings[$rel_key]) ? $rel_key : $app_strings[$rel_key] );
			if (!in_array($etiq_reltab, $arr_options)) {
				continue;
			}
			echo '<tr class="slds-hint-parent slds-page-header__meta-text" data-clipboard-text="{'.$rel_key.'}"><td style="width:350px;"><a href="#'.$rel_key.'"><b>'.$etiq_reltab.':</b></a></<a><td>{'.$rel_key.'}';
			echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$rel_key.'}">' . $btnclose;
			echo '</td></tr>';
		}
		echo '</tbody></table>';
		echo '</li>';
	}
	$SQL_REL = 'SELECT related_tabid, label FROM vtiger_relatedlists WHERE tabid=? AND related_tabid<>0 AND name<>\'get_history\'';
	$res_rel = $adb->pquery($SQL_REL, array($tab['tabid']));
	if ($adb->num_rows($res_rel) > 0) {
		echo '<li>';
		echo '<table class="slds-table slds-table_cell-buffer slds-table_bordered slds-card">';
		echo '<thead><th colspan="2" class="slds-page-header__meta-text">'.getTranslatedString($eoo_strings['VariosEntity']).'</th></thead><tbody>';
		while ($rel_varios = $adb->fetch_array($res_rel)) {
			$SQL_RELN = "SELECT name, tablabel FROM vtiger_tab WHERE tabid=?";
			$res_reln = $adb->pquery($SQL_RELN, array($rel_varios['related_tabid']));
			$rel_label = $adb->query_result($res_reln, 0, 'tablabel');
			if (!array_key_exists($rel_label, $arr_options)) {
				continue;
			}
			if (array_key_exists($rel_varios['label'], $special_modules)) {
				$rel_name = $rel_varios['label'];
				if (empty($app_strings[$rel_varios['label']])) {
					$etiq_reltab = $rel_varios['label'];
				} else {
					$etiq_reltab = $app_strings[$rel_varios['label']];
				}
			} else {
				$rel_name = $adb->query_result($res_reln, 0, 'name');
				if (empty($app_strings[$rel_varios['label']])) {
					if (empty($app_strings[$rel_label])) {
						$etiq_reltab = $rel_label;
					} else {
						$etiq_reltab = $app_strings[$rel_label];
					}
				} else {
					$etiq_reltab = $app_strings[$rel_varios['label']];
				}
			}
			echo '<tr class="slds-hint-parent slds-page-header__meta-text" data-clipboard-text="{'.$rel_name.'}"><td style="width:350px;"><a href="#'.$rel_name.'"><b>'.$etiq_reltab.':</b></a></td><td>{'.$rel_name.'}';
			echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$rel_name.'}">' . $btnclose;
			echo '</td></tr>';
		}
		echo '</tbody></table>';
		echo '</li>';
	}
	echo '</ul>';
	echo '<div class="slds-m-around_small"><a onclick="history.go(-1);" class="slds-text-heading_small slds-badge">'.$eoo_strings['Back'].'</a>';
	echo '<a href="#top"class="slds-text-heading_small slds-badge">'.$eoo_strings['Up'].'</a></div>';
	echo '</li>';
}
foreach ($special_modules as $sp_key => $sp_value) {
	$etiq_tab = $sp_key;
	echo '<li><a name="'.$sp_key.'"></a>';
	outputOONoIconHeader($etiq_tab);
	echo '<table class="slds-table slds-table_cell-buffer slds-table_bordered">';
	if (is_array($sp_value)) {
		foreach ($sp_value as $sp_valmod) {
			$mod_strings = return_module_language($current_language, $sp_valmod);
			$SQL_FIELDS = "SELECT fieldname,fieldlabel FROM vtiger_field WHERE presence IN (0,2) AND tabid=?";
			$res_field = $adb->pquery($SQL_FIELDS, array($sp_tab[$sp_valmod]));
			echo '<tr><td colspan="2">';
			echo '<div class="slds-page-header__col-meta">
			<h3 class="slds-text-heading_small"><u>'.$sp_valmod.'</u></h3>
			</div>';
			echo '</td></tr>';
			while ($field = $adb->fetch_array($res_field)) {
				$etiqueta = (empty($mod_strings[$field['fieldlabel']]) ? $field['fieldlabel'] : $mod_strings[$field['fieldlabel']]);
				echo '<tr class="slds-hint-parent" data-clipboard-text="{'.$sp_key.'.'.$field['fieldname'].'}"><td style="width:350px;"><b>'.$etiqueta.'</b>:</td><td>{'.$sp_key.'.'.$field['fieldname'].'}';
				echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$sp_key.'.'.$field['fieldname'].'}">' . $btnclose;
				echo '</td></tr>';
			}
		}
	} else {
		$mod_strings = return_module_language($current_language, $sp_value);
		$SQL_FIELDS = 'SELECT fieldname,fieldlabel FROM vtiger_field WHERE presence IN (0,2) AND tabid=?';
		$res_field = $adb->pquery($SQL_FIELDS, array($sp_tab[$sp_value]));
		while ($field = $adb->fetch_array($res_field)) {
			$etiqueta = (empty($mod_strings[$field['fieldlabel']]) ? $field['fieldlabel'] : $mod_strings[$field['fieldlabel']]);
			echo '<tr class="slds-hint-parent" data-clipboard-text="{'.$sp_key.'.'.$field['fieldname'].'}"><td style="width:350px;"><b>'.$etiqueta.'</b>:</td><td>{'.$sp_key.'.'.$field['fieldname'].'}';
			echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$sp_key.'.'.$field['fieldname'].'}">' . $btnclose;
			echo '</td></tr>';
		}
	}
	echo '</table>';
	if (array_key_exists($sp_key, $related_module)) {
		echo '<h3>'.$eoo_strings['RelatedModules'].'</h3>';
		echo '<ul>';
		foreach ($related_module[$tab['name']] as $rel_key => $rel_value) {
			$etiq_reltab = (empty($app_strings[$rel_key]) ? $rel_key : $app_strings[$rel_key] );
			echo '<li><a href="#'.$rel_key.'"><b>'.$etiq_reltab.':</b></a> {'.$rel_key.'}';
			echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$rel_key.'}">' . $btnclose;
			echo '</li>';
		}
		echo '</ul>';
	}
	echo '<a onclick="history.go(-1);" class="slds-text-heading_small">'.$eoo_strings['Back'].'</a><span class="slds-text-heading_small">&nbsp;|&nbsp;</span><a href="#top"class="slds-text-heading_small">'.$eoo_strings['Up'].'</a>';
	echo '</li>';
}
include 'modules/evvtgendoc/commands_' . substr($current_language, 0, 2) . '.php';
echo '<li><a name="speciallabels"></a>';
outputOONoIconHeader($eoo_strings['SpecialVars']);
echo '<table class="slds-table slds-table_cell-buffer slds-table_bordered">';
echo '<tr class="slds-hint-parent" data-clipboard-text="'.$foreachGD.'}'.$foreachEndGD.'"><td style="width:350px;"><b>'.$foreachGD.'</b>:</td><td>'.$foreachGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="'.$foreachGD.'}'.$foreachEndGD.'">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="'.$ifexistsGD.'}'.$ifexistsEndGD.'"><td style="width:350px;"><b>'.$ifexistsGD.'</b>:</td><td>'.$ifexistsGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="'.$ifexistsGD.'}'.$ifexistsEndGD.'">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="'.$ifnotexistsGD.'}'.$ifnotexistsEndGD.'"><td style="width:350px;"><b>'.$ifnotexistsGD.'</b>:</td><td>'.$ifnotexistsGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="'.$ifnotexistsGD.'}'.$ifnotexistsEndGD.'">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="'.$imageGD.'}"><td style="width:350px;"><b>'.$imageGD.'</b>:</td><td>'.$imageGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="'.$imageGD.'}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="'.$includeGD.'}"><td style="width:350px;"><b>'.$includeGD.'</b>:</td><td>'.$includeGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="'.$includeGD.'}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="'.$insertindexGD.'"><td style="width:350px;"><b>'.$insertindexGD.'</b>:</td><td>'.$insertindexGD.'';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="'.$insertindexGD.'">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{'.$dateGD.'}"><td style="width:350px;"><b>'.$dateGD.'</b>:</td><td>{'.$dateGD.':'.$eoo_strings['format'].'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$dateGD.'}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{'.$repeticionGD.'}"><td style="width:350px;"><b>'.$repeticionGD.'</b>:</td><td>{'.$repeticionGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$repeticionGD.'}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{'.$expressionGD.'}"><td style="width:350px;"><b>'.$expressionGD.'</b>:</td><td>{'.$expressionGD.$eoo_strings['WorkflowExpresion'].'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$expressionGD.'}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{'.$lineGD.'}"><td style="width:350px;"><b>'.$lineGD.'</b>:</td><td>{'.$lineGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$lineGD.'}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{Organization.organizationname}"><td style="width:350px;"><b>Organization</b>:</td><td>{Organization.organizationname}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.organizationname}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{Organization.address}"><td style="width:350px;"><b>Organization</b>:</td><td>{Organization.address}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.address}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{Organization.city}"><td style="width:350px;"><b>Organization</b>:</td><td>{Organization.city}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.city}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{Organization.state}"><td style="width:350px;"><b>Organization</b>:</td><td>{Organization.state}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.state}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{Organization.code}"><td style="width:350px;"><b>Organization</b>:</td><td>{Organization.code}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.code}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{Organization.country}"><td style="width:350px;"><b>Organization</b>:</td><td>{Organization.country}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.country}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{Organization.phone}"><td style="width:350px;"><b>Organization</b>:</td><td>{Organization.phone}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.phone}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{Organization.fax}"><td style="width:350px;"><b>Organization</b>:</td><td>{Organization.fax}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.fax}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{Organization.website}"><td style="width:350px;"><b>Organization</b>:</td><td>{Organization.website}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.website}">' . $btnclose;
echo '</td></tr>';
echo '<tr class="slds-hint-parent" data-clipboard-text="{Organization.}"><td style="width:350px;"><b>Organization</b>:</td><td>{Organization.'.$eoo_strings['field'].'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.}">' . $btnclose;
echo '</td></tr>';
echo '</table>';
echo '<a onclick="history.go(-1);" class="slds-text-heading_small">'.$eoo_strings['Back'].'</a><span class="slds-text-heading_small">&nbsp;|&nbsp;</span><a href="#top"class="slds-text-heading_small">'.$eoo_strings['Up'].'</a>';
echo '</li>';
echo '</ul>';
echo '</div>';
echo '</div>'; // container
?>
<script>
new Clipboard('.btn');
new Clipboard('.slds-hint-parent');
</script>
