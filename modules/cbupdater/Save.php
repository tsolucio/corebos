<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule, $adb;

if (!empty($_REQUEST['record'])) {
	$record = vtlib_purify($_REQUEST['record']);
	$cbu = $adb->pquery('select appcs from vtiger_cbupdater where cbupdaterid=?', array($record));
	if ($cbu && $adb->num_rows($cbu)>0) {
		if ($cbu->fields['appcs']=='1') {
			header('Location: index.php?action=DetailView&module=cbupdater&record='.urlencode($record));
		} else {
			$__cbSaveSendHeader = false;
			$_REQUEST['pathfilename'] = $_REQUEST['classname'] = '';
			$_REQUEST['filename'] = uniqid();
			require_once 'modules/Vtiger/Save.php';
			$adb->pquery('update vtiger_cbupdater set filename=cbupd_no,appcs=? where cbupdaterid=?', array('0', $focus->id));
			if (!empty($_REQUEST['saverepeat'])) {
				$sesreq = coreBOS_Session::get('saverepeatRequest', array());
				$sesreq['CANCELGO'] = 'index.php?' . $req->getReturnURL() . $search;
				coreBOS_Session::set('saverepeatRequest', $sesreq);
				header('Location: index.php?action=EditView&saverepeat=1&module='.$currentModule);
			} else {
				if (coreBOS_Session::has('ME1x1Info')) {
					$ME1x1Info = coreBOS_Session::get('ME1x1Info', array());
					if (count($ME1x1Info['pending'])==1) {
						coreBOS_Session::delete('ME1x1Info');// we are done
						header('Location: index.php?' . $req->getReturnURL() . $search);
					} else {
						array_shift($ME1x1Info['pending']); // this one is done
						$ME1x1Info['processed'][] = $ME1x1Info['next'];
						$ME1x1Info['next'] = $ME1x1Info['pending'][0];
						coreBOS_Session::set('ME1x1Info', $ME1x1Info);
						$ME1x1Info = coreBOS_Session::get('ME1x1Info', array());
						header('Location: index.php?action=EditView&record='.$ME1x1Info['pending'][0].'&module='.$currentModule);
					}
				} else {
					header('Location: index.php?' . $req->getReturnURL() . $search);
				}
			}
			die();
		}
	} else {
		require_once 'Smarty_setup.php';
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
		$smarty->assign('OPERATION_MESSAGE', getTranslatedString('LBL_PERMISSION'));
		$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	}
} else {
	$__cbSaveSendHeader = false;
	$_REQUEST['pathfilename'] = $_REQUEST['classname'] = '';
	$_REQUEST['filename'] = uniqid();
	require_once 'modules/Vtiger/Save.php';
	$adb->pquery('update vtiger_cbupdater set filename=cbupd_no,appcs=? where cbupdaterid=?', array('0', $focus->id));
	if (!empty($_REQUEST['saverepeat'])) {
		$sesreq = coreBOS_Session::get('saverepeatRequest', array());
		$sesreq['CANCELGO'] = 'index.php?' . $req->getReturnURL() . $search;
		coreBOS_Session::set('saverepeatRequest', $sesreq);
		header('Location: index.php?action=EditView&saverepeat=1&module='.$currentModule);
	} else {
		if (coreBOS_Session::has('ME1x1Info')) {
			$ME1x1Info = coreBOS_Session::get('ME1x1Info', array());
			if (count($ME1x1Info['pending'])==1) {
				coreBOS_Session::delete('ME1x1Info');// we are done
				header('Location: index.php?' . $req->getReturnURL() . $search);
			} else {
				array_shift($ME1x1Info['pending']); // this one is done
				$ME1x1Info['processed'][] = $ME1x1Info['next'];
				$ME1x1Info['next'] = $ME1x1Info['pending'][0];
				coreBOS_Session::set('ME1x1Info', $ME1x1Info);
				$ME1x1Info = coreBOS_Session::get('ME1x1Info', array());
				header('Location: index.php?action=EditView&record='.$ME1x1Info['pending'][0].'&module='.$currentModule);
			}
		} else {
			header('Location: index.php?' . $req->getReturnURL() . $search);
		}
	}
	die();
}
?>
