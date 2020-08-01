<?php
$Vtiger_Utils_Log = false;
include_once 'vtlib/Vtiger/Module.php';
require_once 'Smarty_setup.php';
$crmid = vtlib_purify($_REQUEST['recordid']);
$mod = getSalesEntityType($crmid);
if (!empty($crmid) && isPermitted($mod, 'Save', $crmid)=='yes') {
	sendPPJavascript();
	if (!empty($_REQUEST['ppaction'])) {
		switch ($_REQUEST['ppaction']) {
			case 'del':
				$adb->pquery('update vtiger_portalinfo set user_password="", isactive=0 where id=?', array($crmid));
				$adb->pquery('update vtiger_customerdetails set portal=? where customerid=?', array('0', $crmid));
				break;
			case 'sav':
				if (!empty($_REQUEST['ppasswd'])) {
					$pppasswd = vtlib_purify(urldecode($_REQUEST['ppasswd']));
					$adb->pquery('update vtiger_portalinfo set user_password=? where id=?', array($pppasswd, $crmid));
				}
				break;
			default:
				break;
		}
	}
	$rs = $adb->pquery('select user_password from vtiger_portalinfo where id=?', array($crmid));
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('APP', $app_strings);
	if (empty($rs->fields) || empty($rs->fields['user_password'])) {
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
		$smarty->assign('ERROR_MESSAGE', getTranslatedString('ppnotset', 'Contacts'));
	} else {
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
		$smarty->assign('ERROR_MESSAGE', getTranslatedString('ppset', 'Contacts'));
	}
	if (empty($_REQUEST['ppaction'])) {
		echo '<div id="ppinfo">';
	}
	$smarty->display('applicationmessage.tpl');
	?>
<div class="slds-grid">
<div class="slds-col">
	<button class="slds-truncate slds-button slds-button_neutral" title="<?php echo getTranslatedString('LBL_RESET_PASSWORD'); ?>" onclick="doppSave()">
		<svg class="slds-icon slds-icon_x-small slds-icon-text-default slds-m-right_x-small" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#reset_password"></use>
		</svg>
		<?php echo getTranslatedString('LBL_RESET_PASSWORD'); ?>
	</button>
</div>
<div class="slds-col">
	<button class="slds-truncate slds-button slds-button_text-destructive" title="<?php echo getTranslatedString('LBL_DELETE'); ?>" onclick="doppDelete()">
		<svg class="slds-icon slds-icon_x-small slds-icon-text-default slds-m-right_x-small" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#delete"></use>
		</svg>
		<?php echo getTranslatedString('LBL_DELETE'); ?>
	</button>
</div>
</div>
<div class="slds-form-element slds-m-top_x-small">
<div class="slds-form-element__control">
<textarea id="ppasswd" name="ppasswd" class="slds-textarea"></textarea>
</div>
</div>
	<?php
	if (empty($_REQUEST['ppaction'])) {
		echo '</div>';
	}
}

function sendPPJavascript() {
	?>
	<script>
	window.doppDelete = function () {
		var params = `&${csrfMagicName}=${csrfMagicToken}`;
		return fetch(
			'index.php?module=Contacts&action=ContactsAjax&file=PortalUserPasswordManagement&ppaction=del&recordid='+document.getElementById('record').value,
			{
				method: 'post',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				credentials: 'same-origin',
				body: params
			}
		).then(response => response.text())
		.then(answer => {
			document.getElementById('ppinfo').innerHTML = answer;
		});
	}
	window.doppSave = function () {
		var params = `&${csrfMagicName}=${csrfMagicToken}`;
		params += '&ppasswd='+encodeURIComponent(document.getElementById('ppasswd').value);
		return fetch(
			'index.php?module=Contacts&action=ContactsAjax&file=PortalUserPasswordManagement&ppaction=sav&recordid='+document.getElementById('record').value,
			{
				method: 'post',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				credentials: 'same-origin',
				body: params
			}
		).then(response => response.text())
		.then(answer => {
			document.getElementById('ppinfo').innerHTML = answer;
		});
	}
	</script>
	<?php
}
