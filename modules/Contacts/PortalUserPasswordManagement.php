<?php
$Vtiger_Utils_Log = false;
include_once 'vtlib/Vtiger/Module.php';
require_once 'Smarty_setup.php';
$crmid = vtlib_purify($_REQUEST['recordid']);
$mod = getSalesEntityType($crmid);
if (!empty($crmid) && isPermitted($mod, 'Save', $crmid)=='yes') {
	$addError = false;
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('APP', $app_strings);
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
					$passhistory = $adb->pquery(
						'select 1 from password_history where crmid=? and crmtype=? and pass=?',
						array($crmid, 'C', $pppasswd)
					);
					if (!$passhistory || $adb->num_rows($passhistory) > 0) {
						$addError = true;
						$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
						$smarty->assign('ERROR_MESSAGE', getTranslatedString('ERR_PASSWORD_REPEATED', 'Users'));
					} else {
						$adb->pquery(
							'insert into password_history values (?,?,?)',
							array($crmid, 'C', $pppasswd)
						);
						$adb->pquery('update vtiger_portalinfo set user_password=? where id=?', array($pppasswd, $crmid));
					}
				}
				break;
			default:
				break;
		}
	}
	$rs = $adb->pquery('select user_password from vtiger_portalinfo where id=?', array($crmid));
	if (empty($rs->fields) || empty($rs->fields['user_password'])) {
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
		$smarty->assign('ERROR_MESSAGE', getTranslatedString('ppnotset', 'Contacts'));
	} elseif (!$addError) {
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-success');
		$smarty->assign('ERROR_MESSAGE', getTranslatedString('ppset', 'Contacts'));
	}
	if (empty($_REQUEST['ppaction'])) {
		echo '<div id="ppinfo">';
	}
	$smarty->display('applicationmessage.tpl');
	?>
<div class="slds-notify slds-notify_alert slds-theme_alert-texture slds-theme_error" id="show-err_msg" style="margin-bottom: 20px;display: none">
	<span class="slds-assistive-text">error</span>
	<h2 id="err_msg"></h2>
	<div class="slds-notify__close">
		<button class="slds-button slds-button_icon slds-button_icon-small slds-button_icon-inverse" onclick="document.getElementById('show-err_msg').style.display='none';">
			<svg class="slds-button__icon" aria-hidden="true">
				<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
			</svg>
		</button>
	</div>
</div>
<div class="slds-grid">
<div class="slds-col">
	<button class="slds-truncate slds-button slds-button_neutral" style="border:0;" title="<?php echo getTranslatedString('LBL_RESET_PASSWORD'); ?>" onclick="doppSave()">
		<svg class="slds-icon slds-icon_x-small slds-icon-text-default slds-m-right_x-small" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#save"></use>
		</svg>
	</button>
</div>
<div class="slds-col">
	<button class="slds-truncate slds-button slds-button_text-destructive" style="border:0;" title="<?php echo getTranslatedString('LBL_DELETE'); ?>" onclick="doppDelete()">
		<svg class="slds-icon slds-icon_x-small slds-icon-text-default slds-m-right_x-small" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#delete"></use>
		</svg>
	</button>
</div>
<div class="slds-col">
	<button class="slds-truncate slds-button slds-button_neutral" style="border:0;" title="<?php echo getTranslatedString('Generate password', 'Users'); ?>" onclick="doppGenerate()">
		<svg class="slds-icon slds-icon_x-small slds-icon-text-default slds-m-right_x-small" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#password"></use>
		</svg>
	</button>
</div>
<div class="slds-col">
	<button class="slds-truncate slds-button slds-button_neutral" style="border:0;" title="<?php echo getTranslatedString('Copy', 'EtiquetasOO'); ?>"
	id="ppcopybtn" onclick="this.dataset.clipboardText = document.getElementById('ppasswd').value;">
		<svg class="slds-icon slds-icon_x-small slds-icon-text-default slds-m-right_x-small" aria-hidden="true">
			<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#copy"></use>
		</svg>
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
	window.doppGenerate = function () {
		document.getElementById('ppasswd').value = corebos_Password.getPassword(12, true, true, true, true, false, true, true, true, false);
	}
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
		let err_msg = '';
		let new_password = document.getElementById('ppasswd').value;
		if (new_password == '') {
			err_msg += alert_arr['ERR_ENTER_NEW_PASSWORD'];
		}
		if (err_msg != '') {
			document.getElementById('show-err_msg').style.display = 'block';
			document.getElementById('err_msg').innerHTML = err_msg;
			return;
		}
		new_password = new_password.substring(0, 1024);
		let password = corebos_Password.passwordChecker(new_password);
		if (!password) {
			err_msg = alert_arr['PASSWORD REQUIREMENTS NOT MET'];
			document.getElementById('show-err_msg').style.display = 'block';
			document.getElementById('err_msg').innerHTML = err_msg;
		} else {
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
	}
	var ppclipcopyobject = new ClipboardJS('#ppcopybtn');
	ppclipcopyobject.on('success', function(e) { clipcopyclicked = false; });
	ppclipcopyobject.on('error', function(e) { clipcopyclicked = false; });
	</script>
	<?php
}
