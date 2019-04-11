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

echo '
<script src="modules/EtiquetasOO/clipboard.min.js"></script>
<script>
function go_tab(obj){
	location.href = "#"+obj.options[obj.selectedIndex].value;
}
</script>';

if (isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') {
	$current_language = $_SESSION['authenticated_user_language'];
} else {
	$current_language = $default_language;
}
//set module and application string arrays based upon selected language
$app_strings = return_application_language($current_language);
$app_list_strings = return_app_list_strings_language($current_language);
$eoo_strings = return_module_language($current_language, $currentModule);
//echo '<pre>';var_dump($app_strings);echo '</pre>';
echo '<div style="margin-left: 15px;">';
echo '<h1>'.$eoo_strings['EOO_Title'].'</h1>';

$SQL = "SELECT tabid, name, tablabel FROM vtiger_tab WHERE name='Users' or isentitytype";
$res_tab = $adb->query($SQL);
$num_tabs = $adb->num_rows($res_tab);
$arr_options = array();
echo '<span style="font-size:large">' . $eoo_strings['ElijaModulo'].'</span>&nbsp;<select onchange="go_tab(this);">';
while ($tab = $adb->fetch_array($res_tab)) {
	if ($tab['tablabel'] == 'PBXManager') {
		continue;
	}
	$etiq_tab = (empty($app_strings[$tab['tablabel']]) ? $tab['tablabel'] : $app_strings[$tab['tablabel']] );
	$arr_options[$tab['name']] = $etiq_tab;
	//echo '<option value="'.$tab['name'].'">'.$etiq_tab.'</option>';
}
foreach ($special_modules as $sp_key => $sp_value) {
	$etiq_tab = $sp_key;
	$arr_options[$sp_key] = $etiq_tab;
	//echo '<option value="'.$sp_value.'">'.$etiq_tab.'</option>';
}
array_multisort($arr_options);
foreach ($arr_options as $op_key => $op_value) {
	echo '<option value="'.$op_key.'">'.$op_value.'</option>';
}
echo '</select><br/>';
echo '<br/><span style="font-size:large"><a href="#speciallabels"><b>' . $eoo_strings['SpecialVars'] . '</b></a></span><br/><hr>';
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
	echo '<li><a name="'.$tab['name'].'"></a><h2>'.$etiq_tab.'</h2>';
	echo '<table border="0">';
	$SQL_FIELDS = 'SELECT fieldname,fieldlabel FROM vtiger_field WHERE presence IN (0,2) AND tabid=?';
	$res_field = $adb->pquery($SQL_FIELDS, array($tab['tabid']));
	while ($field = $adb->fetch_array($res_field)) {
		$etiqueta = (empty($mod_strings[$field['fieldlabel']]) ? $field['fieldlabel'] : $mod_strings[$field['fieldlabel']]);
		echo '<tr><td><b>'.$etiqueta.'</b>:</td><td>{'.$tab['name'].'.'.$field['fieldname'].'}';
		echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$tab['name'].'.'.$field['fieldname'].'}">' . $btnclose;
		echo '</td></tr>';
	}
	echo '</table>';
	echo '<h3>'.$eoo_strings['RelatedModules'].'</h3>';
	echo '<ul>';
	if (array_key_exists($tab['name'], $related_module)) {
		echo '<li>'.$eoo_strings['OneEntity'];
		echo '<table>';
		foreach ($related_module[$tab['name']] as $rel_key => $rel_value) {
			$etiq_reltab = (empty($app_strings[$rel_key]) ? $rel_key : $app_strings[$rel_key] );
			if (!in_array($etiq_reltab, $arr_options)) {
				continue;
			}
			echo '<tr><td><a href="#'.$rel_key.'"><b>'.$etiq_reltab.':</b></a></td><td>{'.$rel_key.'}';
			echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$rel_key.'}">' . $btnclose;
			echo '</td></tr>';
		}
		echo '</table>';
		echo '</li>';
	}
	$SQL_REL = 'SELECT related_tabid, label FROM vtiger_relatedlists WHERE tabid=? AND related_tabid<>0 AND name<>\'get_history\'';
	$res_rel = $adb->pquery($SQL_REL, array($tab['tabid']));
	if ($adb->num_rows($res_rel) > 0) {
		echo '<li>'.$eoo_strings['VariosEntity'];
		echo '<table>';
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
			echo '<tr><td><a href="#'.$rel_name.'"><b>'.$etiq_reltab.':</b></a></td><td>{'.$rel_name.'}';
			echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$rel_name.'}">' . $btnclose;
			echo '</td></tr>';
		}
		echo '</table>';
		echo '</li>';
	}
	echo '</ul>';
	echo '<a onclick="history.go(-1);">'.$eoo_strings['Back'].'</a>&nbsp;|&nbsp;<a href="#top">'.$eoo_strings['Up'].'</a>';
	echo '</li>';
}
foreach ($special_modules as $sp_key => $sp_value) {
	//$mod_strings = return_module_language($current_language, $sp_value);
	$etiq_tab = $sp_key;
	echo '<li><a name="'.$sp_key.'"></a><h2>'.$etiq_tab.'</h2>';
	echo '<table border="0">';
	if (is_array($sp_value)) {
		foreach ($sp_value as $sp_valmod) {
			$mod_strings = return_module_language($current_language, $sp_valmod);
			$SQL_FIELDS = "SELECT fieldname,fieldlabel FROM vtiger_field WHERE presence IN (0,2) AND tabid=?";
			$res_field = $adb->pquery($SQL_FIELDS, array($sp_tab[$sp_valmod]));
			echo '<tr><td colspan="2"><h3>'.$sp_valmod.'</h3></td></tr>';
			while ($field = $adb->fetch_array($res_field)) {
				$etiqueta = (empty($mod_strings[$field['fieldlabel']]) ? $field['fieldlabel'] : $mod_strings[$field['fieldlabel']]);
				echo '<tr><td><b>'.$etiqueta.'</b>:</td><td>{'.$sp_key.'.'.$field['fieldname'].'}';
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
			echo '<tr><td><b>'.$etiqueta.'</b>:</td><td>{'.$sp_key.'.'.$field['fieldname'].'}';
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
	echo '<a onclick="history.go(-1);" style="cursor: pointer;">'.$eoo_strings['Back'].'</a>&nbsp;|&nbsp;<a href="#top">'.$eoo_strings['Up'].'</a>';
	echo '</li>';
}
include 'modules/evvtgendoc/commands_' . substr($current_language, 0, 2) . '.php';
echo '<li><a name="speciallabels"></a><h2>'.$eoo_strings['SpecialVars'].'</h2>';
echo '<table border="0">';
echo '<tr><td><b>'.$foreachGD.'</b>:</td><td>'.$foreachGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="'.$foreachGD.'}'.$foreachEndGD.'">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>'.$ifexistsGD.'</b>:</td><td>'.$ifexistsGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="'.$ifexistsGD.'}'.$ifexistsEndGD.'">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>'.$ifnotexistsGD.'</b>:</td><td>'.$ifnotexistsGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="'.$ifnotexistsGD.'}'.$ifnotexistsEndGD.'">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>'.$imageGD.'</b>:</td><td>'.$imageGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="'.$imageGD.'}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>'.$includeGD.'</b>:</td><td>'.$includeGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="'.$includeGD.'}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>'.$insertindexGD.'</b>:</td><td>'.$insertindexGD.'';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="'.$insertindexGD.'">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>'.$dateGD.'</b>:</td><td>{'.$dateGD.':'.$eoo_strings['format'].'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$dateGD.'}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>'.$repeticionGD.'</b>:</td><td>{'.$repeticionGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$repeticionGD.'}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>'.$expressionGD.'</b>:</td><td>{'.$expressionGD.$eoo_strings['WorkflowExpresion'].'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$expressionGD.'}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>'.$lineGD.'</b>:</td><td>{'.$lineGD.'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{'.$lineGD.'}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>Organization</b>:</td><td>{Organization.organizationname}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.organizationname}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>Organization</b>:</td><td>{Organization.address}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.address}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>Organization</b>:</td><td>{Organization.city}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.city}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>Organization</b>:</td><td>{Organization.state}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.state}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>Organization</b>:</td><td>{Organization.code}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.code}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>Organization</b>:</td><td>{Organization.country}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.country}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>Organization</b>:</td><td>{Organization.phone}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.phone}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>Organization</b>:</td><td>{Organization.fax}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.fax}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>Organization</b>:</td><td>{Organization.website}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.website}">' . $btnclose;
echo '</td></tr>';
echo '<tr><td><b>Organization</b>:</td><td>{Organization.'.$eoo_strings['field'].'}';
echo '&nbsp;&nbsp;<button class="btn" data-clipboard-text="{Organization.}">' . $btnclose;
echo '</td></tr>';
echo '</table>';
echo '<a onclick="history.go(-1);" style="cursor: pointer;">'.$eoo_strings['Back'].'</a>&nbsp;|&nbsp;<a href="#top">'.$eoo_strings['Up'].'</a>';
echo '</li>';
echo '</ul>';
echo '</div>';
?>
<script>
new Clipboard('.btn');
</script>
