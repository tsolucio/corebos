<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
*************************************************************************************************/
include_once 'include/Webservices/Create.php';

class addSendEmailAction2FAQ extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb, $current_user;
			$usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
			$rec = array(
				'assigned_user_id' => $usrwsid,
				'reference' => 'Send FAQ by Email',
				'msgt_type' => 'Email',
				'msgt_status' => 'Active',
				'msgt_language' => 'en',
				'msgt_module' => 'Faq',
				'msgt_fields' => '',
				'msgt_metavars' => '',
				'subject' => '[FAQ] $question',
				'template' => '<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none; background-color: rgb(122, 122, 254);" width="700">
<tbody>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td width="50">&nbsp;</td>
		<td>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody>
				<tr>
					<td>
					<table border="0" cellpadding="0" cellspacing="0" style="background-color: rgb(27, 77, 140); font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(255, 255, 255); font-weight: normal; line-height: 25px;" width="100%">
						<tbody>
							<tr>
								<td align="center" rowspan="4">$logo$</td>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td align="left" style="background-color: rgb(27, 77, 140); font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: rgb(255, 255, 255); font-weight: bolder; line-height: 35px;">$companyname</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
						</tbody>
					</table>
					</td>
				</tr>
				<tr>
					<td>
					<table border="0" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);" width="100%">
						<tbody>
							<tr>
								<td valign="top">
								<table border="0" cellpadding="5" cellspacing="0" width="100%">
									<tbody>
										<tr>
											<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);">&nbsp;</td>
										</tr>
										<tr>
											<td style="font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;">Dear $contacts-firstname$ $contacts-lastname$,</td>
										</tr>
										<tr>
											<td style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;"><strong>Question: $question</strong><br />
											Answer: $faq_answer<br />
											Please see if this answer solves your problem and get back to us.<br />
											&nbsp;</td>
										</tr>
										<tr>
											<td align="center">&nbsp;</td>
										</tr>
										<tr>
											<td align="right"><strong style="padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;">Sincerely</strong></td>
										</tr>
										<tr>
											<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">Support Team</td>
										</tr>
										<tr>
											<td align="right" style="font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;">$companyname</td>
										</tr>
									</tbody>
								</table>
								</td>
								<td valign="top" width="1%">&nbsp;</td>
							</tr>
						</tbody>
					</table>
					</td>
				</tr>
			</tbody>
		</table>
		</td>
		<td width="50">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</tbody>
</table>',
				'templateonlytext' => 'Dear $contacts-firstname$ $contacts-lastname$,

Question: $question

Answer: $faq_answer

Please see if this answer solves your problem and get back to us.

Sincerly
Support Team
$companyname',
				'tags' => '',
				'msgt_category' => '--None--',
				'description' => 'Send FAQ by email',
			);
			vtws_create('MsgTemplate', $rec, $current_user);
			/////////////////////
			$tabid = getTabid('Faq');
			$type = 'DETAILVIEWBASIC';
			$label = 'LBL_SENDMAIL_BUTTON_LABEL';
			$url = "javascript:sendmailtemplate('Send FAQ by Email', '".'$MODULE$'."', ".'$RECORD$);';
			$iconpath = 'themes/images/sendmail.png';
			$handlerInfo = null;
			$onlyonmymodule = true;
			$rsCbmap = $adb->query("SELECT cbmapid
				FROM vtiger_cbmap
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_cbmap.cbmapid
				WHERE vtiger_crmentity.deleted=0 AND mapname='SendEmail_ConditionExpression'");
			$cbmap = $adb->fetch_array($rsCbmap);
			$brmap = $cbmap['cbmapid'];
			BusinessActions::addLink($tabid, $type, $label, $url, $iconpath, 0, $handlerInfo, $onlyonmymodule, $brmap);
			BusinessActions::addLink($tabid, 'HEADERSCRIPT', 'MailJS', 'include/js/Mail.js', '', 0, $handlerInfo, $onlyonmymodule, 0);
			/////////////////////
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}