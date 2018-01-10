<?php
/*************************************************************************************************
* Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************
*  Module       : cbupdater
*  Version      : 5.5.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

// Turn on debugging level
$Vtiger_Utils_Log = true;

require_once 'include/utils/utils.php';
include_once('vtlib/Vtiger/Module.php');
require_once('vtlib/Vtiger/Package.php');
include_once('modules/cbupdater/cbupdater.php');
require_once('modules/com_vtiger_workflow/include.inc');
require_once('modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc');
require_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');
require_once('include/events/include.inc');
global $current_user,$adb,$app_strings;
set_time_limit(0);
ini_set('memory_limit', '1024M');

if (empty($current_user) or !is_admin($current_user)) {
	$current_user = Users::getActiveAdminUser();
}
if (empty($current_language)) {
	if (isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') {
		$current_language = $_SESSION['authenticated_user_language'];
	} else {
		if (!empty($current_user->language)) {
			$current_language = $current_user->language;
		} else {
			$current_language = $default_language;
		}
	}
}
if (empty($app_strings)) {
	$app_strings = return_application_language($current_language);
}

class cbupdaterWorker {
	public $cbupdid;
	public $cbupd_no;
	public $author = '';
	public $filename;
	public $classname;
	public $execstate = false;
	public $systemupdate = false;
	public $perspective = false;
	public $blocked = false;
	public $execdate;
	public $updError = false;
	public $query_count=0;
	public $success_query_count=0;
	public $failure_query_count=0;
	public $success_query_array=array();
	public $failure_query_array=array();

	public function __construct() {
		global $adb,$log,$current_user;
		echo "<table width=80% align=center border=1>";
		$reflector = new ReflectionClass(get_class($this));
		$fname = basename($reflector->getFileName(), '.php');
		$cburs = $adb->pquery('select * from vtiger_cbupdater where filename=? and classname=?', array($fname, get_class($this)));
		if ($cburs and $adb->num_rows($cburs)>0) {  // it exists, we load it
			$cbu = $adb->fetch_array($cburs);
			$this->cbupdid = $cbu['cbupdaterid'];
			$this->cbupd_no = $cbu['cbupd_no'];
			$this->author = $cbu['author'];
			$this->filename = $cbu['filename'];
			$this->classname = $cbu['classname'];
			$this->execstate = $cbu['execstate'];
			$this->systemupdate = ($cbu['systemupdate'] == '1');
			$this->perspective = ((bool)(isset($cbu['perspective']) && $cbu['perspective'] == '1'));
			$this->blocked = ((bool)(isset($cbu['blocked']) && $cbu['blocked'] == '1'));
			$this->execdate = $cbu['execdate'];
			$this->updError = false;
		} else {  // it doesn't exist, we fail because it MUST exist
			$this->sendError();
		}
	}

	public function applyChange() {
		if ($this->isBlocked()) {
			return true;
		}
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			// do your magic here
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

	public function undoChange() {
		if ($this->isBlocked()) {
			return true;
		}
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isSystemUpdate()) {
			$this->sendMsg('Changeset '.get_class($this).' is a system update, it cannot be undone!');
		} else {
			if ($this->isApplied()) {
				// undo your magic here
				$this->sendMsg('Changeset '.get_class($this).' undone!');
				$this->markUndone();
			} else {
				$this->sendMsg('Changeset '.get_class($this).' not applied!');
			}
		}
		$this->finishExecution();
	}

	public function isApplied() {
		return ($this->execstate=='Executed');
	}

	public function isSystemUpdate() {
		return $this->systemupdate;
	}

	public function isBlocked() {
		return $this->blocked;
	}

	public function isContinuous() {
		return ($this->execstate=='Continuous');
	}

	public function hasError() {
		return ($this->updError or empty($this->cbupdid));
	}

	public function markApplied($stoponerror = true) {
		if ($this->isBlocked()) {
			return true;
		}
		if ($this->hasError() and $stoponerror) {
			$this->sendError();
		}
		global $adb,$log;
		if ($this->isContinuous()) {
			$adb->pquery('update vtiger_cbupdater set execdate=CURDATE() where cbupdaterid=?', array($this->cbupdid));
		} else {
			$adb->pquery('update vtiger_cbupdater set execstate=?,execdate=CURDATE() where cbupdaterid=?', array('Executed',$this->cbupdid));
		}
		$this->execstate = 'Executed';
	}

	public function markUndone($stoponerror = true) {
		if ($this->isBlocked() or $this->isContinuous()) {
			return true;
		}
		if ($this->hasError() and $stoponerror) {
			$this->sendError();
		}
		global $adb,$log;
		$adb->pquery('update vtiger_cbupdater set execstate=?,execdate=NULL where cbupdaterid=?', array('Pending',$this->cbupdid));
		$this->execstate = 'Pending';
	}

	public function ExecuteQuery($query, $params = array()) {
		global $adb,$log;
		$paramstring = (count($params)>0 ? '&nbsp;&nbsp;'.print_r($params, true) : '');
		$status = $adb->pquery($query, $params);
		$this->query_count++;
		if (is_object($status)) {
			echo '
		<tr width="100%">
		<td width="10%">'.get_class($status).'</td>
		<td width="10%"><span style="color:green"> S </span></td>
		<td width="80%">'.$query.$paramstring.'</td>
		</tr>';
			$success_query_array[$this->success_query_count++] = $query;
			$log->debug("Query Success ==> $query");
		} else {
			echo '
		<tr width="100%">
		<td width="25%">'.$status.'</td>
		<td width="5%"><span style="color:red"> F </span></td>
		<td width="70%">'.$query.$paramstring.'</td>
		</tr>';
			$this->failure_query_array[$this->failure_query_count++] = $query.$paramstring;
			$this->updError = true;
			$log->debug("Query Failed ==> $query \n Error is ==> [".$adb->database->ErrorNo()."]".$adb->database->ErrorMsg());
		}
	}

	public function deleteWorkflow($wfid) {
		$this->ExecuteQuery('DELETE FROM com_vtiger_workflowtasks WHERE workflow_id=?', array($wfid));
		$this->ExecuteQuery('DELETE FROM com_vtiger_workflows WHERE workflow_id=?', array($wfid));
	}

	/* Given an array of field definitions this method will create or activate the fields.
	 * The layout is an array of Module Name, Block Name and Field Definition
		array(
			'{modulename}' => array(
				'{blockname}' => array(
					'{fieldname}' => array(
						'columntype'=>'decimal(10,3)',
						'typeofdata'=>'NN~O',
						'uitype'=>'1',
						'displaytype'=>'3',
						'label'=>'', // optional, if empty fieldname will be used
						'massedit' => 0 | 1  // optional, if empty 0 will be set
						'mods'=>array(module names), // used if uitype 10
						'vals'=>array(picklist values), // used if uitype 15, 16 or 33
					),
				)),
	* See changeset addLeadEmailOptOutAndConversionRelatedFields for an example
	*/
	public function massCreateFields($fieldLayout) {
		global $adb;
		foreach ($fieldLayout as $mod => $blocks) {
			$moduleInstance = Vtiger_Module::getInstance($mod);
			if ($moduleInstance) {
				foreach ($blocks as $blockname => $fields) {
					$block = Vtiger_Block::getInstance($blockname, $moduleInstance);
					if (!$block) {
						$block = new VTiger_Block();
						$block->label = $blockname;
						$moduleInstance->addBlock($block);
					}
					foreach ($fields as $fieldname => $fieldinfo) {
						$field = Vtiger_Field::getInstance($fieldname, $moduleInstance);
						if ($field) {
							$adb->pquery('update vtiger_field set presence=2 where fieldid=?', array($field->id));
						} else {
							$fname = strtolower($fieldname);
							$fname = str_replace(' ', '_', $fname);
							$newfield = new Vtiger_Field();
							$newfield->name = $fname;
							$newfield->label = (empty($fieldinfo['label']) ? $fname : $fieldinfo['label']);
							$newfield->column = $fname;
							$newfield->columntype = $fieldinfo['columntype'];
							$newfield->typeofdata = $fieldinfo['typeofdata'];
							$newfield->uitype = $fieldinfo['uitype'];
							$newfield->displaytype = (empty($fieldinfo['displaytype']) ? '1' : $fieldinfo['displaytype']);
							$newfield->masseditable = (empty($fieldinfo['massedit']) ? '0' : $fieldinfo['massedit']);
							$block->addField($newfield);
							if ($fieldinfo['uitype']=='10' and !empty($fieldinfo['mods'])) {
								$newfield->setRelatedModules($fieldinfo['mods']);
							}
							if ($fieldinfo['uitype']=='15' || $fieldinfo['uitype']=='16' || $fieldinfo['uitype']=='33') {
								if (empty($fieldinfo['vals'])) {
									$newfield->setPicklistValues(array('--None--'));
								} else {
									$newfield->setPicklistValues($fieldinfo['vals']);
								}
							}
						}
					}
				}
			} else {
				$this->sendMsg('Module not found: '.$mod.'!');
			}
		}
	}

	/* Given an array of field definitions this method will hide the fields.
	 * The layout is an array of Module Name and Field Definition
		array(
			'{modulename}' => array(
					'{fieldname1}',
					'{fieldname2}',
					'{fieldname3}',
			)
		),
	*/
	public function massHideFields($fieldLayout) {
		global $adb;
		foreach ($fieldLayout as $module => $fields) {
			$moduleInstance = Vtiger_Module::getInstance($module);
			if ($moduleInstance) {
				foreach ($fields as $field) {
					$field = Vtiger_Field::getInstance($field, $moduleInstance);
					if ($field) {
						$this->ExecuteQuery('UPDATE vtiger_field SET presence = 1 WHERE fieldid=?', array($field->id));
					}
				}
			} else {
				$this->sendMsg('Module not found: '.$module.'!');
			}
		}
	}

	/* Given an array of field definitions this method will delete the fields.
	 * The layout is an array of Module Name and Field Definition
		array(
			'{modulename}' => array(
					'{fieldname1}',
					'{fieldname2}',
					'{fieldname3}',
			)
		),
	*/
	public function massDeleteFields($fieldLayout) {
		global $adb;
		foreach ($fieldLayout as $module => $fields) {
			$moduleInstance = Vtiger_Module::getInstance($module);
			if ($moduleInstance) {
				foreach ($fields as $field) {
					$field = Vtiger_Field::getInstance($field, $moduleInstance);
					if ($field) {
						$field->delete();
					}
				}
			} else {
				$this->sendMsg('Module not found: '.$module.'!');
			}
		}
	}

	/* Given an array of field names this method will move the fields to the specified Block.
	 * The layout is an array of Module Name and Field Names
		array(
			'{modulename}' => array(
					'{fieldname1}',
					'{fieldname2}',
					'{fieldname3}',
			)
		),
	*/
	public function massMoveFieldsToBlock($fieldLayout, $newblock) {
		global $adb;
		foreach ($fieldLayout as $module => $fields) {
			$moduleInstance = Vtiger_Module::getInstance($module);
			$block = Vtiger_Block::getInstance($newblock, $moduleInstance);
			if ($moduleInstance && $block) {
				foreach ($fields as $field) {
					$field = Vtiger_Field::getInstance($field, $moduleInstance);
					if ($field) {
						$this->ExecuteQuery('UPDATE vtiger_field SET block = ? WHERE fieldid=?', array($block->id, $field->id));
					}
				}
			} else {
				$this->sendMsg('Module not found: '.$module.'!');
			}
		}
	}

	/* Given an array of blocks and field names this method will sort the fields in the given order
	 * All unspecified fields in the block will be moved to the end
	 * Any field that is not in the block will be ignored
	 * The layout is an array of Module Name, block and Field Names
		array(
			'{modulename}' => array(
				{block1} => array(
					'{fieldname1}',
					'{fieldname2}',
					'{fieldnamen}',
				),
				{block2} => array(
					'{fieldname1}',
					'{fieldname2}',
					'{fieldnamen}',
				),
			)
		),
	*/
	public function orderFieldsInBlocks($fieldLayout) {
		global $adb;
		foreach ($fieldLayout as $module => $blocks) {
			$moduleInstance = Vtiger_Module::getInstance($module);
			if ($moduleInstance) {
				foreach ($blocks as $blockname => $fields) {
					$block = Vtiger_Block::getInstance($blockname, $moduleInstance);
					if ($block) {
						$currentSequence = array();
						$rs = $adb->pquery('select fieldname, fieldid from vtiger_field where block=? order by sequence', array($block->id));
						while ($fld = $adb->fetch_array($rs)) {
							$currentSequence[$fld['fieldname']] = $fld['fieldid'];
						}
						$seq = 1;
						foreach ($fields as $fname) {
							$field = Vtiger_Field::getInstance($fname, $moduleInstance);
							if ($field) {
								if ($field->block->id == $block->id) {
									$this->ExecuteQuery('UPDATE vtiger_field SET sequence = ? WHERE fieldid=?', array($seq, $field->id));
									$seq++;
									if (isset($currentSequence[$field->name])) {
										unset($currentSequence[$field->name]);
									}
								}
							}
						}
						foreach ($currentSequence as $fname => $fid) {
							$this->ExecuteQuery('UPDATE vtiger_field SET sequence = ? WHERE fieldid=?', array($seq, $fid));
							$seq++;
						}
					} else {
						$this->sendMsg('Block ' . $blockname . ' not found in Module ' . $module . '!');
					}
				}
			} else {
				$this->sendMsg('Module not found: '.$module.'!');
			}
		}
	}

	/* convert a text field into a picklist
	 * It will fill the picklist with the distinct values in the text field
	 * @param fieldname
	 * @param module
	 * @param boolean multiple indicate if the new picklist with be multiple (true) or simple (false)
	 */
	public function convertTextFieldToPicklist($fieldname, $module, $multiple = false) {
		global $adb,$log;
		if (!empty($fieldname) && !empty($module)) {
			$moduleInstance = Vtiger_Module::getInstance($module);
			if ($moduleInstance) {
				$field = Vtiger_Field::getInstance($fieldname, $moduleInstance);
				if ($field) {
					$data =$adb->pquery('SELECT uitype,tablename FROM vtiger_field WHERE fieldid = ? and tabid=?', array($field->id, $moduleInstance->id));
					$uitype =$adb->query_result($data, 0, 'uitype');
					if ($uitype==1) {
						$table = $adb->query_result($data, 0, 'tablename');
						$adb->query("update $table set $fieldname = '--None--' where $fieldname is null or trim($fieldname)=''");
						$picklistvalues=$adb->query("Select distinct $fieldname from $table");
						$list=array();
						for ($i=0; $i<$adb->num_rows($picklistvalues); $i++) {
							$list[]=$adb->query_result($picklistvalues, $i, $fieldname);
						}
						if ($multiple) {
							$adb->pquery('update vtiger_field set uitype=33 where fieldid=? and tabid=?', array($field->id, $moduleInstance->id));
						} else {
							$adb->pquery('update vtiger_field set uitype=15 where fieldid=? and tabid=?', array($field->id, $moduleInstance->id));
						}
						$field->setPicklistValues($list);
					} else {
						$this->sendMsg("<b>The field $fieldname should be uitype 1.</b><br>");
					}
				} else {
					$this->sendMsg("<b>Failed to find $fieldname field.</b><br>");
				}
			} else {
				$this->sendMsg("<b>Failed to find $module module.</b><br>");
			}
		} else {
			$this->sendMsg("<b>The fieldname and module parameters can't be empty</b><br>");
		}
	}

	public function installManifestModule($module) {
		$package = new Vtiger_Package();
		ob_start();
		$rdo = $package->importManifest("modules/$module/manifest.xml");
		$out = ob_get_contents();
		ob_end_clean();
		$this->sendMsg($out);
		if ($rdo) {
			$this->sendMsg("$module installed!");
		} else {
			$this->sendMsgError("ERROR installing $module!");
		}
	}

	public function isModuleInstalled($module) {
		global $adb;
		$tabrs = $adb->pquery('select count(*) from vtiger_tab where name=?', array($module));
		return ($tabrs and $adb->query_result($tabrs, 0, 0)==1);
	}

	public function sendMsg($msg) {
		echo '<tr width="100%"><td colspan=3>'.$msg.'</td></tr>';
	}

	public function sendMsgError($msg) {
		echo '<tr width="100%"><td colspan=3><span style="color:red">'.$msg.'</span></td></tr>';
		$this->updError = true;
	}

	public function sendError() {
		$this->updError = true;
		echo '<tr width="100%"><td colspan=3<span style="color:red">ERROR: Class called without update record in application!!</span></td></tr></table>';
		die();
	}

	public function finishExecution() {
		echo '</table>';
		if (count($this->failure_query_array)>0) {
			echo <<<EOT
<br /><br />
<b style="color:#FF0000">Failed Queries Log</b>
<div id="failedLog" style="border:1px solid #666666;width:90%;position:relative;height:200px;overflow:auto;left:5%;top:10px;">
EOT;
			foreach ($this->failure_query_array as $failed_query) {
				echo '<br><span style="color:red">'.$failed_query.'</span>';
			}
			echo '</div>';
		}
		echo <<<EOT
<br /><br />
<table width="35%" border="0" cellpadding="5" cellspacing="0" align="center" class="small">
	<tr>
	<td width="75%" align="right" nowrap>Total Number of queries executed : </td>
	<td width="25%" align="left"><b>{$this->query_count}</b></td>
	</tr>
	<tr>
	<td align="right">Queries Successed : </td>
	<td align="left"><b style="color:#006600;">{$this->success_query_count}</b></td>
	</tr>
	<tr>
	<td align="right">Queries Failed : </td>
	<td align="left"><b style="color:#FF0000;">{$this->failure_query_count}</b></td>
	</tr>
</table>
EOT;
	}
}
?>