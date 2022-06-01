<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/AutoNumberPrefix/AutoNumberPrefix.php';

class PrefixEvent extends VTEventHandler {

	public function handleEvent($handlerType, $entityData) {
	}

	public function handleFilter($handlerType, $parameter) {
		global $adb, $currentModule, $default_charset, $current_user;
		if ($handlerType=='corebos.filter.ModuleSeqNumber.set') {
			/****  Parameters:
			 * $mode = configure, $module, $req_str, $req_no,
			 * $result => value to be returned
			 * $returnResult => true if returned value should be used
			 */
			$check = $adb->pquery(
				'select autonumberprefixid,current from vtiger_autonumberprefix where semodule=? and prefix=? and active=1 order by default1 DESC LIMIT 1',
				array($parameter[1], $parameter[2])
			);
			if ($adb->num_rows($check) == 0) {
				$focus = new AutoNumberPrefix();
				$focus->id = '';
				$focus->mode = '';
				$focus->column_fields['prefix']   =$parameter[2];
				$focus->column_fields['semodule'] =$parameter[1];
				$focus->column_fields['format']   =$parameter[3];
				$focus->column_fields['active']   =1;
				$focus->column_fields['current']  =$parameter[3];
				$focus->column_fields['default1'] =1;
				$focus->column_fields['assigned_user_id']=1;
				$focus->save('AutoNumberPrefix');
				$parameter[4]=true;
			} elseif ($adb->num_rows($check) != 0) {
				$num_check = $adb->query_result($check, 0, 'current');
				$req_no = $parameter[3];
				if ($req_no < $num_check) {
					$parameter[4]=false;
				} else {
					$anpid = $adb->query_result($check, 0, 'autonumberprefixid');
					$adb->pquery('UPDATE vtiger_autonumberprefix SET current=? where autonumberprefixid=?', array($parameter[3], $anpid));
					$parameter[4]=true;
				}
			}
			$parameter[5]=true;
		} elseif ($handlerType=='corebos.filter.ModuleSeqNumber.increment') {
			/****  Parameters:
			 * $mode = increment, $module, $req_str, $req_no
			 * $result => value to be returned // overrided with crmid for workflow expressions
			 * $returnResult => true if returned value should be used
			 */
			$check = $adb->pquery(
				'select autonumberprefixid,current,prefix,format,isworkflowexpression
				from vtiger_autonumberprefix
				where semodule=? and active=1 order by default1 DESC LIMIT 1 FOR UPDATE',
				array($parameter[1])
			);
			$anpid = $adb->query_result($check, 0, 'autonumberprefixid');
			$prefix = $adb->query_result($check, 0, 'prefix');
			$prefix = html_entity_decode($prefix, ENT_QUOTES, $default_charset);
			$curid = $adb->query_result($check, 0, 'current');
			$format = $adb->query_result($check, 0, 'format');
			$format = html_entity_decode($format, ENT_QUOTES, $default_charset);
			$isworkflowexpression = $adb->query_result($check, 0, 'isworkflowexpression');
			if ($isworkflowexpression && is_numeric($parameter[4])) {
				$format = sprintf($format, $curid);
				$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($format)));
				$expression = $parser->expression();
				$exprEvaluater = new VTFieldExpressionEvaluater($expression);
				$entityId = vtws_getEntityId($parameter[1]).'x'.$parameter[4];
				$entity = new VTWorkflowEntity($current_user, $entityId);
				$prev_inv_no = $prefix . $exprEvaluater->evaluate($entity);
			} else {
				if (is_numeric($format)) {
					$fmtlen = strlen($format);
					$temp = str_repeat('0', $fmtlen);
					$numchars = max(strlen($curid), $fmtlen);
					$prev_inv_no = $prefix . substr($temp.$curid, -$numchars);
				} else {
					$prev_inv_no = $prefix . sprintf(date($format, time()), $curid);
				}
			}
			$adb->pquery('UPDATE vtiger_autonumberprefix SET current=current+1 where autonumberprefixid=?', array($anpid));
			$parameter[4]=decode_html($prev_inv_no);
			$parameter[5]=true;
		} elseif ($handlerType=='corebos.filter.ModuleSeqNumber.get') {
			/****  Parameters:
			 * $mod_seq_string, $mod_seq_prefix, $mod_seq_no,
			 * $doNative => true if default functionality is to be invoked, false if not
			 */
			$parameter[0] = $adb->pquery(
				'SELECT prefix, current from vtiger_autonumberprefix where semodule = ? and active=1 order by default1 DESC LIMIT 1',
				array($currentModule)
			);
			$parameter[1] = $adb->query_result($parameter[0], 0, 'prefix');
			$parameter[2] = $adb->query_result($parameter[0], 0, 'current');
			$parameter[3] = false;
		} elseif ($handlerType=='corebos.filter.ModuleSeqNumber.fillempty') {
			/****  Parameters:
			 * $module
			 * $result => value to be returned
			 * $returnResult => true if returned value should be used
			 */
			$focus = CRMEntity::getInstance($parameter[0]);

			$tabid = getTabid($parameter[0]);
			$fieldinfo = $adb->pquery('SELECT * FROM vtiger_field WHERE tabid = ? AND uitype = 4', array($tabid));

			$returninfo = array();
			$returninfo['totalrecords'] = $returninfo['updatedrecords'] = 0;
			if ($fieldinfo && $adb->num_rows($fieldinfo)) {
				// TODO: We assume that there will be only one field per module
				$fld_table = $adb->query_result($fieldinfo, 0, 'tablename');
				$fld_column = $adb->query_result($fieldinfo, 0, 'columnname');

				$records = $adb->query('SELECT '.$focus->table_index.' AS recordid FROM '.$focus->table_name." WHERE $fld_column = '' OR $fld_column is NULL");

				if ($records && $adb->num_rows($records)) {
					$returninfo['totalrecords'] = $adb->num_rows($records);
					$returninfo['updatedrecords'] = 0;

					$check = $adb->pquery(
						'select autonumberprefixid,current,prefix,format,isworkflowexpression
						from vtiger_autonumberprefix
						where semodule=? and active=1 order by default1 DESC LIMIT 1 FOR UPDATE',
						array($parameter[0])
					);
					$prefix = $adb->query_result($check, 0, 'prefix');
					$prefix = html_entity_decode($prefix, ENT_QUOTES, $default_charset);
					$curid = $adb->query_result($check, 0, 'current');
					$anpid = $adb->query_result($check, 0, 'autonumberprefixid');
					$format = $adb->query_result($check, 0, 'format');
					$format = html_entity_decode($format, ENT_QUOTES, $default_charset);
					$isworkflowexpression = $adb->query_result($check, 0, 'isworkflowexpression');
					$wsid = vtws_getEntityId($parameter[0]).'x';
					$old_cur_id = $curid;
					while ($recordinfo = $adb->fetch_array($records)) {
						if ($isworkflowexpression) {
							$formatc = sprintf($format, $curid);
							$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($formatc)));
							$expression = $parser->expression();
							$exprEvaluater = new VTFieldExpressionEvaluater($expression);
							$entityId = $wsid.$recordinfo['recordid'];
							$entity = new VTWorkflowEntity($current_user, $entityId);
							$prev_inv_no = $prefix . $exprEvaluater->evaluate($entity);
						} else {
							if (is_numeric($format)) {
								$fmtlen = strlen($format);
								$temp = str_repeat('0', $fmtlen);
								$numchars = max(strlen($curid), $fmtlen);
								$anval = $prefix . substr($temp.$curid, -$numchars);
							} else {
								$anval = $prefix . sprintf(date($format, time()), $curid);
							}
						}
						$curid++;
						$adb->pquery("UPDATE $fld_table SET $fld_column = ? WHERE $focus->table_index = ?", array($anval, $recordinfo['recordid']));
						$returninfo['updatedrecords'] = $returninfo['updatedrecords'] + 1;
					}
					if ($old_cur_id != $curid) {
						$adb->pquery('UPDATE vtiger_autonumberprefix set current=? where autonumberprefixid=?', array($curid,$anpid));
					}
				}
			}
			$parameter[1]=$returninfo;
			$parameter[2]=true;
		}
		return $parameter;
	}
}
?>
