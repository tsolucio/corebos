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
*************************************************************************************************/

class importdefaultemailtemplates extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$vtiger_actions = array(
				array('reference' => 'Birthday Sample','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Birthday Sample','template' => '<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
				<title></title>
				<style type="text/css">#templatePreheader{font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #444444;background-color: #ffffff;}#backgroundTable{background-color: #f4faff;}#backgroundTable tbody tr td{/*background-color: #ffffff;*/}#footerContent{font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;background-color: #ffffff}
				</style>
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr height="10px">
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>
							<table border="0" cellpadding="5" cellspacing="0" id="templatePreheader" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your email content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr>
							<td>
							<table border="0" cellpadding="5" cellspacing="0" width="600">
								<tbody>
									<tr>
										<td width="100px">&nbsp;</td>
										<td width="400px"><img alt="Birthday" src="http://localhost/coreboscrm/storage/kcimages/images/happybday.jpg" /></td>
										<td width="100px">&nbsp;</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr>
							<td>
							<center>
							<table>
								<tbody>
									<tr height="10px">
										<td><b color:="" font-size:="" style="font-family: " trebuchet="">$contacts-firstname$ $contacts-lastname$</b></td>
									</tr>
									<tr height="20px">
										<td>&nbsp;</td>
									</tr>
								</tbody>
							</table>
							</center>
							</td>
						</tr>
						<tr height="10px">
							<td colspan="6">
							<table style="font-size: 16px;color: #5d5d5d;">
								<tbody>
									<tr>
										<td width="20px">&nbsp;</td>
										<td style="text-align:center;line-height: 30px;" width="560px">Dear $contacts-firstname$,<br />
										We wish you all the happiness in the world and more, on this, your special day!<br />
										Warmest wishes,<br />
										CompanyName</td>
										<td width="20px">&nbsp;</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="20px">
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="6" id="footerContent">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle"><a href="http://twitter.com/" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com/" target="_blank">follow on Facebook</a> | <a href="#">forward to a friend</a><br />
										<em>Copyright &copy;2013 </em>CompanyName<em>, All rights reserved.</em><br />
										<a href="http://unsubscribe/">unsubscribe from this list</a></td>
										<td><img alt="Company Logo" height="50px" src="companylogo.png" width="200px" /></td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="20px">
						</tr>
					</tbody>
				</table>
				</center>','templateonlytext' => 'Dear $contacts-firstname$,

				We wish you all the happiness in the world and more, on this, your special day!

				Warmest wishes,
				CompanyName'),
							array('reference' => 'Webinar','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Webinar','template' => '<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
				<title></title>
				<style type="text/css">#templatePreheader{font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #444444;}#backgroundTable{background-color: #E3E2E9;}#backgroundTable tbody tr td{/*background-color: #ffffff;*/}#footerContent{font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;}
				</style>
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr height="10px">
						</tr>
						<tr>
							<td>
							<table border="0" cellpadding="5" cellspacing="0" id="templatePreheader" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your email content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr>
							<td>
							<center><img alt="Meeting" src="http://localhost/coreboscrm/storage/kcimages/images/meet.png" style="width: 600px; height: 328px;" /></center>
							</td>
						</tr>
						<tr>
							<td>
							<center>
							<table>
								<tbody>
									<tr height="10px">
										<td>&nbsp;</td>
									</tr>
									<tr height="10px">
										<td><b>Title:</b> Unlocking Your Sales Team Potential With Sales Force Automation</td>
									</tr>
									<tr height="10px">
										<td><b>Date:</b> Wednesday, March 6 , 2015</td>
									</tr>
									<tr height="10px">
										<td><b>Time:</b> 10:00 AM - 11:00 AM EST (US)</td>
									</tr>
									<tr height="10px">
										<td>&nbsp;</td>
									</tr>
								</tbody>
							</table>
							</center>
							</td>
						</tr>
						<tr height="10px">
							<td colspan="6">
							<table>
								<tbody>
									<tr>
										<td width="20px">&nbsp;</td>
										<td>After registering you will receive a confirmation email containing information about joining the Webinar.</td>
										<td width="20px">&nbsp;</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="100px">
							<td colspan="6">
							<table>
								<tbody>
									<tr>
										<td width="10px">&nbsp;</td>
										<td>
										<p style="text-align:justify;font-size: 14px;font-family: Helvetica,sans-sarif;">CRM experts know the true power of sales force automation (SFA) - that win rates can be up to 70% higher, and sales cycle times less than half of those without it. Despite all the fancy new sales tools that have been developed over the years, SFA remains one of the most important elements impacting the success of a CRM implementation because it frees up time spent on administrative tasks for more important duties, like nurturing your leads and prospects. Are you employing the best SFA practices and leveraging your CRM implementation to the fullest? Join us next Wednesday to learn how to supercharge your sales team and boost your win rates using SFA tools and techniques.</p>
										</td>
										<td width="10px">&nbsp;</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr>
							<td colspan="6" id="footerContent">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle">
										<center>
										<div>&nbsp;<a href="http://twitter.com/" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com/" target="_blank">follow on Facebook</a> | <a href="#">forward to a friend</a>&nbsp;</div>
										</center>
										</td>
									</tr>
									<tr>
										<td valign="top" width="350">
										<center>
										<div><em>Copyright &copy;2013 CompanyName, All rights reserved.</em></div>
										</center>
										</td>
									</tr>
									<tr>
										<td colspan="2" id="utility" valign="middle">
										<center>
										<div>&nbsp;<a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</center>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="10px">
						</tr>
					</tbody>
				</table>
				</center>','templateonlytext' => ''),
							array('reference' => 'Marketing','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Marketing','template' => '<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
				<title></title>
				<style type="text/css">#templatePreheader{font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #666666;}#backgroundTable{/*background-color: #99ccff;*/}#backgroundTable tbody tr td{/*background-color: #ffffff;*/}#footerContent{font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;}p{margin: 0 !important;}
				</style>
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr>
							<td colspan="6">
							<table border="0" cellpadding="5" cellspacing="0" id="templatePreheader" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your email content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr>
							<td colspan="6">
							<center><img alt="vtiger On-demand Logo" src="http://localhost/coreboscrm/storage/kcimages/images/chartmulticolor.png" style="width: 600px; height: 215px;" /></center>
							</td>
						</tr>
						<tr height="50px">
							<td width="20px">&nbsp;</td>
							<td style="text-align: center;" width="380px">
							<p style="font-size: 30px;color: #007dc7;font-family: Helvetica,sans-sarif">4 Steps of Marketing</p>
							</td>
							<td width="20px">&nbsp;</td>
						</tr>
						<tr height="20px">
							<td>&nbsp;</td>
						</tr>
						<tr height="100px">
							<td width="10px">&nbsp;</td>
							<td>
							<p style="color:#454545;text-align:justify;font-size: 17px;font-family: Verdana,sans-sarif;">CRM experts know the true power of sales force automation (SFA) - that win rates can be up to 70% higher, and sales cycle times less than half of those without it. Despite all the fancy new sales tools that have been developed over the years, SFA remains one of the most important elements impacting the success of a CRM implementation because it frees up time spent on administrative tasks for more important duties, like nurturing your leads and prospects. Are you employing the best SFA practices and leveraging your CRM implementation to the fullest? Join us next Wednesday to learn how to supercharge your sales team and boost your win rates using SFA tools and techniques.</p>
							</td>
							<td width="10px">&nbsp;</td>
						</tr>
						<tr>
							<td width="10px">&nbsp;</td>
							<td colspan="6">
							<table>
								<tbody>
									<tr height="10px">
										<td>&nbsp;</td>
									</tr>
									<tr height="10px">
										<td><b>System Requirements</b></td>
									</tr>
									<tr height="10px">
										<td>PC-based attendees</td>
									</tr>
									<tr height="10px">
										<td>Required: Windows 7, Vista, XP or 2003 Server</td>
									</tr>
									<tr height="10px">
										<td>&nbsp;</td>
									</tr>
									<tr height="10px">
										<td>Mac-based attendees</td>
									</tr>
									<tr height="10px">
										<td>Required: Mac OS X 10.5 or newer</td>
									</tr>
									<tr height="10px">
										<td>&nbsp;</td>
									</tr>
									<tr height="10px">
										<td>Mobile attendees</td>
									</tr>
									<tr height="10px">
										<td>Required: iPhone, iPad, Android phone or Android tablet</td>
									</tr>
									<tr height="20px">
										<td>&nbsp;</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr>
							<td width="10px">&nbsp;</td>
							<td id="footerContent" width="590px">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="4" valign="middle" width="400px">
										<div>&nbsp;<a href="http://twitter.com/" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com/" target="_blank">follow on Facebook</a> | <a href="#">forward to a friend</a>&nbsp;<br />
										<em>Copyright &copy;2013 CompanyName, All rights reserved.</em></div>
										</td>
										<td colspan="2" valign="middle" width="190px">
										<div><a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="20px">
							<td>&nbsp;</td>
						</tr>
					</tbody>
				</table>
				</center>','templateonlytext' => ''),
							array('reference' => 'Promoting Product','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Promoting Product','template' => '<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
				<title></title>
				<style type="text/css">#templatePreheader{font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #666666;background-color: #fffv;}#backgroundTable{/*background-color: #99ccff;*/}#backgroundTable tbody tr td{/*background-color: #ffffff;*/}#footerContent{font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;}
				</style>
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr>
							<td colspan="6">
							<table border="0" cellpadding="5" cellspacing="0" id="templatePreheader" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your email content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr>
							<td colspan="6">
							<center><img alt="vtiger On-demand Logo" src="http://localhost/coreboscrm/storage/kcimages/images/logo.png" style="width: 600px; height: 100px;" /></center>
							</td>
						</tr>
						<tr height="50px">
							<td colspan="6" style="text-align:center;">
							<table>
								<tbody>
									<tr>
										<td width="5%">&nbsp;</td>
										<td width="90%">
										<p style="font-size: 20px;font-family: Helvetica,sans-sarif;">ProductKart introducing a brand new Product into market</p>
										</td>
										<td width="5%">&nbsp;</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="200px">
							<td width="200px">
							<table>
								<tbody>
									<tr height="200px">
										<td width="400px">
										<p style="font-size: 14px;text-align: justify;color: #007dc7;font-family: Helvetica,sans-sarif">Unlocking Your Sales Team Potential With Sales Force AutomationUnlocking Your Sales Team Potential With Sales Force AutomationUnlocking Your Sales Team Potential With Sales Force AutomationUnlocking Your Sales Team Potential With Sales Force Automation some Unlocking Your Sales Team Potential</p>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
							<td width="200px"><img alt="key illustration image" src="http://localhost/coreboscrm/storage/kcimages/images/pro.png" style="width: 200px; height: 200px;" /></td>
							<td width="200px">
							<table>
								<tbody>
									<tr height="200px">
										<td width="400px">
										<p style="font-size: 14px;text-align: justify;color: #007dc7;font-family: Helvetica,sans-sarif">Unlocking Your Sales Team Potential With Sales Force AutomationUnlocking Your Sales Team Potential With Sales Force AutomationUnlocking Your Sales Team Potential With Sales Force AutomationUnlocking Your Sales Team Potential With Sales Force AutomationUnlocking Your Sales Team Potential</p>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="100px">
							<td colspan="6">
							<table>
								<tbody>
									<tr>
										<td width="10px">&nbsp;</td>
										<td>
										<p style="text-align:justify;font-size: 14px;font-family: Helvetica,sans-sarif;">Snelsten dichtbij hectaren beletsel lamamijn is op. Planters geschikt machtige die resident elk negritos passeert men are. Afneemt mei gedaald men metalen wij opzicht proeven. Volhouden moerassen in mineralen in. Had hoofdstad hun vroegeren sultanaat singapore. Goudmijnen werktuigen en op herkenbaar nu losgemaakt de voorschijn. Negri steel komen al en grond. Met kost ton soms rook uit des. Dus dit wezen een geval sinds wilde nam steel. Op slechts streken af en afwegen ze tijdens. Is al twaalf te in stadje hoewel goeden eerste. Productief afwachting dit insnijding men dan tin. Van verscholen verkochten dik tinwinning uitgevoerd monopolies dat van. Hoewel arbeid aan dat den sterft. Regentijd ad al vereenigd olifanten verbouwen na. Hollanders buitendien locomobiel ongunstige bak mag ingewanden.</p>
										</td>
										<td width="10px">&nbsp;</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr>
							<td colspan="6" id="footerContent">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle">
										<center>
										<div>&nbsp;<a href="http://twitter.com" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com" target="_blank">follow on Facebook</a> | <a href="#">forward to a friend</a>&nbsp;</div>
										</center>
										</td>
									</tr>
									<tr>
										<td valign="top" width="350">
										<center>
										<div><em>Copyright &copy;2013 CompanyName, All rights reserved.</em></div>
										</center>
										</td>
									</tr>
									<tr>
										<td colspan="2" id="utility" valign="middle">
										<center>
										<div>&nbsp;<a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</center>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</center>','templateonlytext' => ''),
							array('reference' => '2-Column_2-Images','actions_type' => 'Digital','actions_status' => 'Active','actions_language' => '','subject' => '2-Column_2-Images','template' => '<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
				<title></title>
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr>
							<td colspan="6">
							<table border="0" cellpadding="5" cellspacing="0" style="font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #666666;background-color: #e8f5fe;" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your email content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="100px" style="background: #eee;">
							<td colspan="6">
							<center>Insert a logo or Heading here</center>
							</td>
						</tr>
						<tr height="500px">
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<center><img src="http://localhost/coreboscrm/storage/kcimages/images/default_Image.png" style="width: 200px; height: 200px;" /></center>

							<h3>Column1 Heading here</h3>

							<p>Column1 body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<center><img src="http://localhost/coreboscrm/storage/kcimages/images/default_Image.png" style="height: 200px; width: 200px;" /></center>

							<h3>Column2 Heading here</h3>

							<p>Column2 body</p>
							</td>
						</tr>
						<tr>
							<td colspan="6" style="font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;border: 1px solid #ddd;padding: 10px;">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle">
										<center>
										<div>&nbsp;<a href="http://twitter.com" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com" target="_blank">follow on Facebook</a></div>
										</center>
										</td>
									</tr>
									<tr>
										<td valign="top" width="350">
										<center>
										<div><em>Copyright &copy;Company name, All rights reserved.</em></div>
										</center>
										</td>
									</tr>
									<tr>
										<td colspan="2" id="utility" valign="middle">
										<center>
										<div><a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</center>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</center>','templateonlytext' => ''),
							array('reference' => '2-Column_1-Logo','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => '2-Column_1-Logo','template' => '<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
				<title></title>
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr>
							<td colspan="6">
							<table border="0" cellpadding="5" cellspacing="0" style="font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #666666;background-color: #e8f5fe;" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your email content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="100px" style="background: #eee;">
							<td colspan="6">
							<center>Insert a logo or Heading here</center>
							</td>
						</tr>
						<tr height="500px">
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<p>Column1 body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<p>Column2 body</p>
							</td>
						</tr>
						<tr>
							<td colspan="6" style="font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;border: 1px solid #ddd;padding: 10px;">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle">
										<center>
										<div>&nbsp;<a href="http://twitter.com" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com" target="_blank">follow on Facebook</a></div>
										</center>
										</td>
									</tr>
									<tr>
										<td valign="top" width="350">
										<center>
										<div><em>Copyright &copy;Company name, All rights reserved.</em></div>
										</center>
										</td>
									</tr>
									<tr>
										<td colspan="2" id="utility" valign="middle">
										<center>
										<div><a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</center>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</center>','templateonlytext' => ''),
							array('reference' => '1-Heading_9-Grid','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => '1-Heading_9-Grid','template' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html>
				<head>
					<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
					<title>Basic Template</title>
				</head>
				<body class="scayt-enabled" leftmargin="0" marginheight="0" marginwidth="0" offset="0" style="font-family: Helvetica,Verdana,sans-serif;font-size:12px;" topmargin="0">
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr>
							<td colspan="6">
							<table border="0" cellpadding="5" cellspacing="0" style="font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #666666;background-color: #e8f5fe;" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your email content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="100px" style="background: #eee;">
							<td colspan="6">
							<center>Insert a logo here</center>
							</td>
						</tr>
						<tr height="100px">
							<td colspan="6" style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h1>Main Heading here</h1>

							<p>Some Description......</p>
							</td>
						</tr>
						<tr height="200px">
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
						</tr>
						<tr height="200px">
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
						</tr>
						<tr height="200px">
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
						</tr>
						<tr>
							<td colspan="6" style="font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;border: 1px solid #ddd;padding: 10px;">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle">
										<center>
										<div>&nbsp;<a href="http://twitter.com" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com" target="_blank">follow on Facebook</a></div>
										</center>
										</td>
									</tr>
									<tr>
										<td valign="top" width="350">
										<center>
										<div><em>Copyright &copy;Company name, All rights reserved.</em></div>
										</center>
										</td>
									</tr>
									<tr>
										<td colspan="" id="utility" valign="middle">
										<center>
										<div><a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</center>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</center>
				</body>
				</html>','templateonlytext' => ''),
							array('reference' => '1-Heading_4-Grid','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => '1-Heading_4-Grid','template' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html>
				<head>
					<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
					<title>Basic Template</title>
				</head>
				<body class="scayt-enabled" leftmargin="0" marginheight="0" marginwidth="0" offset="0" style="font-family: Helvetica,Verdana,sans-serif;font-size:12px;" topmargin="0">
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr>
							<td colspan="6">
							<table border="0" cellpadding="5" cellspacing="0" style="font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #666666;background-color: #e8f5fe;" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your emails content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="100px" style="background: #eee;">
							<td colspan="6">
							<center>Insert a logo here</center>
							</td>
						</tr>
						<tr height="100px">
							<td colspan="6" style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h1>Main Heading here</h1>

							<p>Some Description......</p>
							</td>
						</tr>
						<tr height="300px">
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
						</tr>
						<tr height="300px">
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h3>Grid heading</h3>

							<p>Grid body</p>
							</td>
						</tr>
						<tr>
							<td colspan="6" style="font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;border: 1px solid #ddd;padding: 10px;">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle">
										<center>
										<div>&nbsp;<a href="http://twitter.com" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com" target="_blank">follow on Facebook</a></div>
										</center>
										</td>
									</tr>
									<tr>
										<td valign="top" width="350">
										<center>
										<div><em>Copyright &copy;Company name, All rights reserved.</em></div>
										</center>
										</td>
									</tr>
									<tr>
										<td colspan="" id="utility" valign="middle">
										<center>
										<div><a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</center>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</center>
				</body>
				</html>','templateonlytext' => ''),
							array('reference' => '3-Images_3-Columns_1-Logo','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => '3-Images_3-Columns_1-Logo','template' => '<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
				<title></title>
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr>
							<td colspan="6">
							<table border="0" cellpadding="5" cellspacing="0" style="font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #666666;background-color: #e8f5fe;" width="100%">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your emails content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="100px" style="background: #eee;">
							<td colspan="6">
							<center>Insert a logo here</center>
							</td>
						</tr>
						<tr height="100px">
							<td colspan="6" style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h1>Your Heading here</h1>

							<p>Some Description......</p>
							</td>
						</tr>
						<tr height="500px">
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<center><img src="http://localhost/coreboscrm/storage/kcimages/images/default_Image.png" style="width: 200px; height: 200px;" /></center>

							<h1>Column1 Heading here</h1>

							<p>Column1 body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<center><img src="http://localhost/coreboscrm/storage/kcimages/images/default_Image.png" style="width: 200px; height: 200px;" /></center>

							<h1>Column2 Heading here</h1>

							<p>Column2 body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<center><img src="http://localhost/coreboscrm/storage/kcimages/images/default_Image.png" style="width: 200px; height: 200px;" /></center>

							<h1>Column3 Heading here</h1>

							<p>Column3 body</p>
							</td>
						</tr>
						<tr>
							<td colspan="6" style="font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;border: 1px solid #ddd;padding: 10px;">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle">
										<center>
										<div>&nbsp;<a href="http://twitter.com" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com" target="_blank">follow on Facebook</a></div>
										</center>
										</td>
									</tr>
									<tr>
										<td valign="top" width="350">
										<center>
										<div><em>Copyright &copy;Company name, All rights reserved.</em></div>
										</center>
										</td>
									</tr>
									<tr>
										<td colspan="" id="utility" valign="middle">
										<center>
										<div><a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</center>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</center>','templateonlytext' => ''),
							array('reference' => '1-Logo_1-Heading_3-Columns','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => '1-Logo_1-Heading_3-Columns','template' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html>
				<head>
					<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
					<title>Basic Template</title>
				</head>
				<body class="scayt-enabled" leftmargin="0" marginheight="0" marginwidth="0" offset="0" style="font-family: Helvetica,Verdana,sans-serif;font-size:12px;" topmargin="0">
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr>
							<td colspan="6">
							<table border="0" cellpadding="5" cellspacing="0" style="font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #666666;background-color: #e8f5fe;" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your emails content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="100px" style="background: #eee;">
							<td colspan="6">
							<center>Insert a logo here</center>
							</td>
						</tr>
						<tr height="100px">
							<td colspan="6" style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h1>Your Heading here</h1>

							<p>Some Description......</p>
							</td>
						</tr>
						<tr height="500px">
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h1>Column1 Heading here</h1>

							<p>Column1 body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h1>Column2 Heading here</h1>

							<p>Column2 body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h1>Column3 Heading here</h1>

							<p>Column3 body</p>
							</td>
						</tr>
						<tr>
							<td colspan="6" style="font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;border: 1px solid #ddd;padding: 10px;">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle">
										<center>
										<div>&nbsp;<a href="http://twitter.com" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com" target="_blank">follow on Facebook</a></div>
										</center>
										</td>
									</tr>
									<tr>
										<td valign="top" width="350">
										<center>
										<div><em>Copyright &copy;Company name, All rights reserved.</em></div>
										</center>
										</td>
									</tr>
									<tr>
										<td colspan="" id="utility" valign="middle">
										<center>
										<div><a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</center>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</center>
				</body>
				</html>','templateonlytext' => ''),
							array('reference' => '1-Logo_1-Heading_3-AlternativeRows','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => '1-Logo_1-Heading_3-AlternativeRows','template' => '<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
				<title></title>
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr>
							<td colspan="6">
							<table border="0" cellpadding="5" cellspacing="0" style="font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #666666;background-color: #e8f5fe;" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your emails content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="100px" style="background: #eee;">
							<td colspan="6">
							<center>Insert a logo here</center>
							</td>
						</tr>
						<tr height="100px">
							<td colspan="6" style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h1>Main Heading here</h1>

							<p>Some Description......</p>
							</td>
						</tr>
						<tr height="20px">
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>
							<table>
								<tbody>
									<tr height="200px">
										<td><img src="http://localhost/coreboscrm/storage/kcimages/images/default_Image.png" style="width: 200px; height: 200px;" /></td>
										<td width="20px">&nbsp;</td>
										<td>
										<div>
										<h3>SubHeading 1</h3>

										<p>Betrubte launigen heimelig mu zu du. Lauernd da ei so wo spielte barbara flecken. Ruh arbeiter dem vor gelaufig nah sparlich. Ja unsicherer stockwerke feierabend uberwunden dachkammer er da. Besonderes ungerechte dazwischen in ku dazwischen da gearbeitet getunchten. Kleines namlich nur fenster ihn. Schuchtern leuchtturm neidgefuhl la se dienstmagd im flusterton erkundigte. Fragen beeten werdet ordnen wochen oha loffel tod gar zur. Knabenhaft arbeitsame</p>
										</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="20px">
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>
							<table>
								<tbody>
									<tr height="200px">
										<td>
										<div>
										<h3>SubHeading 2</h3>

										<p>Betrubte launigen heimelig mu zu du. Lauernd da ei so wo spielte barbara flecken. Ruh arbeiter dem vor gelaufig nah sparlich. Ja unsicherer stockwerke feierabend uberwunden dachkammer er da. Besonderes ungerechte dazwischen in ku dazwischen da gearbeitet getunchten. Kleines namlich nur fenster ihn. Schuchtern leuchtturm neidgefuhl la se dienstmagd im flusterton erkundigte. Fragen beeten werdet ordnen wochen oha loffel tod gar zur. Knabenhaft arbeitsame</p>
										</div>
										</td>
										<td width="20px">&nbsp;</td>
										<td><img src="http://localhost/coreboscrm/storage/kcimages/images/default_Image.png" style="width: 200px; height: 200px;" /></td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="20px">
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>
							<table>
								<tbody>
									<tr height="200px">
										<td><img src="http://localhost/coreboscrm/storage/kcimages/images/default_Image.png" style="width: 200px; height: 200px;" /></td>
										<td width="20px">&nbsp;</td>
										<td>
										<div>
										<h3>SubHeading 3</h3>

										<p>Betrubte launigen heimelig mu zu du. Lauernd da ei so wo spielte barbara flecken. Ruh arbeiter dem vor gelaufig nah sparlich. Ja unsicherer stockwerke feierabend uberwunden dachkammer er da. Besonderes ungerechte dazwischen in ku dazwischen da gearbeitet getunchten. Kleines namlich nur fenster ihn. Schuchtern leuchtturm neidgefuhl la se dienstmagd im flusterton erkundigte. Fragen beeten werdet ordnen wochen oha loffel tod gar zur. Knabenhaft arbeitsame</p>
										</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr>
							<td colspan="6" style="font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;border: 1px solid #ddd;padding: 10px;">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle">
										<center>
										<div>&nbsp;<a href="http://twitter.com" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com" target="_blank">follow on Facebook</a></div>
										</center>
										</td>
									</tr>
									<tr>
										<td valign="top" width="350">
										<center>
										<div><em>Copyright &copy;Company name, All rights reserved.</em></div>
										</center>
										</td>
									</tr>
									<tr>
										<td colspan="" id="utility" valign="middle">
										<center>
										<div><a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</center>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</center>','templateonlytext' => ''),
							array('reference' => '1-Heading_2-Column_1-Logo','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => '1-Heading_2-Column_1-Logo','template' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html>
				<head>
					<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
					<title>Basic Template</title>
				</head>
				<body class="scayt-enabled" leftmargin="0" marginheight="0" marginwidth="0" offset="0" style="font-family: Helvetica,Verdana,sans-serif;font-size:12px;" topmargin="0">
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr>
							<td colspan="6">
							<table border="0" cellpadding="5" cellspacing="0" style="font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #666666;background-color: #e8f5fe;" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your email content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="100px" style="background: #eee;">
							<td colspan="6">
							<center>Insert a logo here</center>
							</td>
						</tr>
						<tr height="100px">
							<td colspan="2" style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<h1>Your Heading here</h1>

							<p>Some Description......</p>
							</td>
						</tr>
						<tr height="500px">
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<p>Column1 body</p>
							</td>
							<td style="border: 1px solid #ddd;padding: 10px;" valign="top">
							<p>Column2 body</p>
							</td>
						</tr>
						<tr>
							<td colspan="6" style="font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;border: 1px solid #ddd;padding: 10px;">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle">
										<center>
										<div>&nbsp;<a href="http://twitter.com" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com" target="_blank">follow on Facebook</a></div>
										</center>
										</td>
									</tr>
									<tr>
										<td valign="top" width="350">
										<center>
										<div><em>Copyright &copy;Company name, All rights reserved.</em></div>
										</center>
										</td>
									</tr>
									<tr>
										<td colspan="2" id="utility" valign="middle">
										<center>
										<div><a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</center>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</center>
				</body>
				</html>','templateonlytext' => ''),
							array('reference' => '1-Column_1-Image_1-Logo','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => '1-Column_1-Image_1-Logo','template' => '<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
				<title></title>
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr>
							<td colspan="6">
							<table border="0" cellpadding="5" cellspacing="0" style="font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #666666;background-color: #e8f5fe;" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your emails content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="100px" style="background: #eee;">
							<td colspan="6">
							<center>Insert a logo or Heading here</center>
							</td>
						</tr>
						<tr height="150px">
							<td style="border: 1px solid #efefef;padding: 10px;" valign="top">
							<table border="0" cellpadding="0" cellspacing="0">
								<tbody>
									<tr height="100px">
										<td style="" valign="top" width="420px">
										<h1>Your Heading here</h1>

										<p>Body of the Email template</p>
										</td>
										<td style="" valign="top" width="180px"><img src="http://localhost/coreboscrm/storage/kcimages/images/default_Image.png" style="width: 150px; height: 150px;" /></td>
									</tr>
									<tr>
										<td style="" valign="top">Continue from the above....</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr>
							<td colspan="6" style="font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;border: 1px solid #efefef;padding: 10px;">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle">
										<center>
										<div>&nbsp;<a href="http://twitter.com" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com" target="_blank">follow on Facebook</a></div>
										</center>
										</td>
									</tr>
									<tr>
										<td valign="top" width="350">
										<center>
										<div><em>Copyright &copy;Company name, All rights reserved.</em></div>
										</center>
										</td>
									</tr>
									<tr>
										<td colspan="2" id="utility" valign="middle">
										<center>
										<div><a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</center>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</center>','templateonlytext' => ''),
							array('reference' => '1-Logo_1-Column','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => '1-Logo_1-Column','template' => '<meta content="text/html;charset=UTF-8" http-equiv="Content-Type" />
				<title></title>
				<center>
				<table border="0" cellpadding="0" cellspacing="0" id="backgroundTable" width="600px">
					<tbody>
						<tr>
							<td colspan="6">
							<table border="0" cellpadding="5" cellspacing="0" style="font-family: Helvetica,Verdana,sans-serif;font-size: 10px;color: #666666;background-color: #e8f5fe;" width="600">
								<tbody>
									<tr>
										<td>
										<div>Use this area to offer a short teaser of your emails content. Text here will show in the preview area of some email clients.</div>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
						<tr height="100px" style="background: #eee;">
							<td colspan="6">
							<center>Insert a logo or Heading here</center>
							</td>
						</tr>
						<tr height="150px">
							<td style="border: 1px solid #efefef;padding: 10px;" valign="top">
							<h1>Your Heading here</h1>

							<p>Body of the Email template</p>
							</td>
						</tr>
						<tr>
							<td colspan="6" style="font-family: Helvetica,Verdana,sans-serif;font-size: 11px;color: #666666;border: 1px solid #efefef;padding: 10px;">
							<table border="0" cellpadding="4" cellspacing="0" width="100%">
								<tbody>
									<tr>
										<td colspan="2" id="social" valign="middle">
										<center>
										<div>&nbsp;<a href="http://twitter.com" target="_blank">follow on Twitter</a> | <a href="https://www.facebook.com" target="_blank">follow on Facebook</a></div>
										</center>
										</td>
									</tr>
									<tr>
										<td valign="top" width="350">
										<center>
										<div><em>Copyright &copy;Company name, All rights reserved.</em></div>
										</center>
										</td>
									</tr>
									<tr>
										<td colspan="2" id="utility" valign="middle">
										<center>
										<div><a href="http://unsubscribe/">unsubscribe from this list</a></div>
										</center>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</center>','templateonlytext' => ''),
							array('reference' => 'Green grass','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Green grass','template' => '<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
				<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
					<tbody>
						<tr>
							<td align="center">
							<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFBCE;" width="600">
								<tbody>
									<tr>
										<td>
										<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
											<tbody>
												<tr>
													<td style="vertical-align:top;" width="100%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="background:#FFFBCE;padding-top:5px;padding-right:5px;padding-bottom:5px;padding-left:5px;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFBCE;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<p style="color:#000;text-align:center;font-size:12px;margin:0;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#0F7D27;">View it</span></a> in your browser</p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>

				<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
					<tbody>
						<tr>
							<td align="center">
							<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
								<tbody>
									<tr>
										<td>
										<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
											<tbody>
												<tr>
													<td style="vertical-align:top;padding-top:0px;padding-right:0px;padding-bottom:10px;padding-left:0px;" width="100%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td align="center" style="vertical-align: top;">
																			<div><img src="http://localhost/coreboscrm/storage/kcimages/images/grass.jpg" style="border: medium none; width: 590px; height: 235px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>

													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td align="center" style="vertical-align: top;">
																			<div><img src="http://localhost/coreboscrm/storage/kcimages/images/hlinegreen.gif" style="border: medium none; width: 590px; resize: none; position: relative; display: block; top: 0px; left: 0px; height: 5px;" /></div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>

													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="background:#FFFFFF;padding-top:10px;padding-right:30px;padding-bottom:10px;padding-left:30px;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<h2 style="width:100%;font-size:30px;line-height:170%;margin:0;text-align:left;"><span style="color:#515151;font-weight:bold;font-family:Arial;">Heading 1</span></h2>

																			<p style="color:#515151;font-family:Arial;font-size:16px;line-height:150%;margin:0;text-align:left;">Beet greens kohlrabi, orache; tepary bean winter melon fluted pumpkin epazote sorrel chinese artichoke, soko.</p>
																			&nbsp;

																			<p style="color:#515151;font-family:Arial;font-size:16px;line-height:150%;margin:0;text-align:left;">Tinda! Swiss chard, pigeon pea urad bean turnip moth bean; sweet corn aka corn; aka maize shallot black-eyed pea sierra leone bologi sea kale watercress squash. Tarwi, broadleaf arrowhead - bok choy garbanzo.</p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>

													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="background:#FFFFFF;padding-top:10px;padding-right:30px;padding-bottom:0px;padding-left:30px;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td style="width:100%;border-top-color:#cecece;border-top-style:solid;border-top-width:1px;" width="100%">&nbsp;</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
												</tr>
											</tbody>
										</table>

										<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
											<tbody>
												<tr>
													<td style="vertical-align:top;padding-top:10px;padding-right:30px;padding-bottom:10px;padding-left:30px;" width="255px">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<h3 style="width:100%;font-size:19px;line-height:150%;margin:0;text-align:left;"><span style="color:#515151;font-weight:bold;font-family:Arial;">Heading 2</span></h3>

																			<p style="color:#515151;font-family:Arial;font-size:14px;line-height:150%;margin:0;text-align:left;">Sorrel velvet bean fat hen - pumpkin onion tomato scallion. Urad bean - lentil, garden rocket potato swede; lagos bologi bamboo shoot. Bologi; ginger plectranthus?</p>

																			<p style="color:#515151;font-family:Arial;font-size:14px;line-height:150%;margin:0;text-align:left;">&nbsp;</p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>

													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<table border="0" cellpadding="13" cellspacing="0" style="-moz-border-radius:4px;-webkit-border-radius:4px;border-radius:4px;background-color:#2fb850;">
																				<tbody>
																					<tr>
																						<td align="center" style="text-align:center;text-decoration:none;padding-left:25px;padding-right:25px;" valign="middle">
																						<div class="text">
																						<div style="color:#FFFFFF;font-family:Arial;font-size:20px;line-height:130%;margin:0;text-align:center;font-weight:bold;"><a href="https://corebos.com" style="text-decoration:none;color:#FFFFFF;" target="_blank">Learn more</a></div>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
													<td style="vertical-align:top;padding-top:10px;padding-right:30px;padding-bottom:10px;padding-left:0px;" width="255px">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<h3 style="width:100%;font-size:19px;line-height:150%;margin:0;text-align:left;"><span style="color:#515151;font-weight:bold;font-family:Arial;">Heading 2</span></h3>

																			<p style="color:#515151;font-family:Arial;font-size:14px;line-height:150%;margin:0;text-align:left;">Lagos bologi sierra leone bologi soko lettuce horse gram american groundnut; wild leek. Peanut, carrot american groundnut; rutabaga, indian pea. Miner Lettuce lima.</p>

																			<p style="color:#515151;font-family:Arial;font-size:14px;line-height:150%;margin:0;text-align:left;">&nbsp;</p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>

													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<table border="0" cellpadding="13" cellspacing="0" style="-moz-border-radius:4px;-webkit-border-radius:4px;border-radius:4px;background-color:#2fb850;">
																				<tbody>
																					<tr>
																						<td align="center" style="text-align:center;text-decoration:none;padding-left:25px;padding-right:25px;" valign="middle">
																						<div class="text">
																						<div style="color:#FFFFFF;font-family:Arial;font-size:20px;line-height:130%;margin:0;text-align:center;font-weight:bold;"><a href="https://corebos.com" style="text-decoration:none;color:#FFFFFF;" target="_blank">Learn more</a></div>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
												</tr>
											</tbody>
										</table>

										<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
											<tbody>
												<tr>
													<td style="vertical-align:top;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;" width="100%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="background:#FFFFFF;padding-top:20px;padding-right:30px;padding-bottom:0px;padding-left:30px;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td style="width:100%;border-top-color:#cecece;border-top-style:solid;border-top-width:1px;" width="100%">&nbsp;</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
												</tr>
											</tbody>
										</table>

										<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
											<tbody>
												<tr>
													<td style="vertical-align:top;padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px;" width="255px">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<p style="color:#515151;font-family:Arial;font-size:12px;line-height:170%;margin:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#0F7D27">here</span></a></p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
													<td style="vertical-align:top;padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:0px;" width="255px">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<p style="color:#515151;font-family:Arial;font-size:12px;line-height:170%;margin:0;text-align:left;">Company Details</p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>

				<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
					<tbody>
						<tr>
							<td align="center">
							<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;" width="600">
								<tbody>
									<tr>
										<td>
										<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
											<tbody>
												<tr>
													<td style="vertical-align:top;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:0px;" width="">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFBCE;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFBCE;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">&nbsp;</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
													<td style="vertical-align:top;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:15px;" width="100">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFBCE;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFBCE;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<table border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																				<tbody>
																					<tr>
																						<td align="left" style="vertical-align:top;text-align:left;padding:0;" width="26px"><img src="http://localhost/coreboscrm/storage/kcimages/images/rss.png" style="border: medium none; width: 26px; height: 26px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></td>
																						<td style="vertical-align:middle;">
																						<div class="text" style="word-wrap:break-word;">&nbsp;&nbsp;<a href="https://corebos.com" style="text-decoration:underline;color:#0F7D27;font-family:Arial;font-size:12px;line-height:170%;margin:0;text-align:left;" target="_blank">Subscribe</a></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
													<td style="vertical-align:top;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:15px;" width="153">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFBCE;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFBCE;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<table border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																				<tbody>
																					<tr>
																						<td align="left" style="vertical-align:top;text-align:left;padding:0;" width="26px"><img src="http://localhost/coreboscrm/storage/kcimages/images/twit.png" style="border: medium none; width: 26px; height: 26px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></td>
																						<td style="vertical-align:middle;">
																						<div class="text" style="word-wrap:break-word;">&nbsp;&nbsp;<a href="http:\\twitter.com" style="text-decoration:underline;color:#0F7D27;font-family:Arial;font-size:12px;line-height:170%;margin:0;text-align:left;" target="_blank">Follow Us on Twitter</a></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
													<td style="vertical-align:top;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:15px;" width="153">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFBCE;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFBCE;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<table border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																				<tbody>
																					<tr>
																						<td align="left" style="vertical-align:top;text-align:left;padding:0;" width="26px"><img src="http://localhost/coreboscrm/storage/kcimages/images/fb.png" style="border: medium none; width: 26px; height: 26px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></td>
																						<td style="vertical-align:middle;">
																						<div class="text" style="word-wrap:break-word;">&nbsp;&nbsp;<a href="http:\\facebook.com" style="text-decoration:underline;color:#0F7D27;font-family:Arial;font-size:12px;line-height:170%;margin:0;text-align:left;" target="_blank">Like Us on Facebook</a></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</div>','templateonlytext' => ''),
							array('reference' => 'Coffee Break','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Coffee Break','template' => '<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
				<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
					<tbody>
						<tr>
							<td align="center">
							<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFF8CE;" width="600">
								<tbody>
									<tr>
										<td>
										<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
											<tbody>
												<tr>
													<td style="vertical-align:top;" width="100%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="background:#FFF8CE;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFF8CE;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<p style="color:#554335;text-align:center;font-size:12px;margin:0;font-family:Arial;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#934902;">View it</span></a> in your browser</p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>

				<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
					<tbody>
						<tr>
							<td align="center">
							<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#3A1E12;" width="600">
								<tbody>
									<tr>
										<td>
										<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
											<tbody>
												<tr>
													<td style="vertical-align:top;" width="100%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#3A1E12;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#3A1E12;" width="100%">
																	<tbody>
																		<tr>
																			<td align="center" style="vertical-align: top;">
																			<div><img src="http://localhost/coreboscrm/storage/kcimages/images/Coffee_breaktop.jpg" style="border: medium none; width: 590px; height: 197px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>

													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="background:#3A1E12;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#3A1E12;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<div style="color:#fff9cf;font-size:48px;line-height:100%;font-family:Arial;font-weight:bold;">
																			<h3 style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;"><span style="color:#fff9cf;font-size:48px;line-height:100%;font-family:Arial;font-weight:bold;">COFFEE BREAK</span></h3>
																			</div>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>

													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#3A1E12;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#3A1E12;" width="100%">
																	<tbody>
																		<tr>
																			<td align="center" style="vertical-align: top;">
																			<div><img src="http://localhost/coreboscrm/storage/kcimages/images/Coffee_breakbot.jpg" style="border: medium none; width: 590px; height: 93px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>

				<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
					<tbody>
						<tr>
							<td align="center">
							<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFF8CE;" width="600">
								<tbody>
									<tr>
										<td>
										<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
											<tbody>
												<tr>
													<td style="vertical-align:top;" width="60%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="background:#FFF8CE;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:20px;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFF8CE;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<p style="color:#554339;font-size:14px;line-height:180%;margin:0;text-align:left;font-family:Arial;">Brussels sprout lagos bologi green bean kuka peanut; soybeam elephant garlic. Bitter melon avocado, dabdelion chinese mallow indian prea mustard, spinach. Salsify; carbon, swede gobo, winter purslane!</p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
													<td style="vertical-align:top;" width="40%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="background:#FFF8CE;padding-top:16px;padding-right:0px;padding-bottom:16px;padding-left:0px;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFF8CE;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<div style="color:#554335;font-size:40px;font-weight:bold;font-family:Arial;font-style:italic;">
																			<h1 style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;"><em><span style="color:#554335;font-size:40px;font-weight:bold;font-family:Arial;">Get 50% off!</span></em></h1>
																			</div>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>

													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFF8CE;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFF8CE;" width="100%">
																	<tbody>
																		<tr>
																			<td align="center" style="vertical-align:top;">
																			<table border="0" cellpadding="15" cellspacing="0" style="-webkit-border-radius:30px;-moz-border-radius:30px;border-radius:30px; background-color:#3a2c23; height:20px;">
																				<tbody>
																					<tr>
																						<td align="center" style="text-align:center; text-decoration:none;" valign="middle">
																						<div class="text" style="padding-left:20px;padding-right:20px;">
																						<div style="color:#FFFFFF;font-family:Arial;font-size:20px;line-height:130%;margin:0;text-align:center;font-weight:bold;"><a href="https://corebos.com" style="text-decoration:none;color:#FFFFFF;" target="_blank">Learn more</a></div>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>

				<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
					<tbody>
						<tr>
							<td align="center">
							<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
								<tbody>
									<tr>
										<td>
										<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
											<tbody>
												<tr>
													<td style="vertical-align:top;padding-top:20px;padding-right:2px;padding-bottom:15px;padding-left:20px;" width="33%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="background:#FFFFFF;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td align="center" style="vertical-align: top;">
																			<div><img src="http://localhost/coreboscrm/storage/kcimages/images/default_Image.png" style="border: medium none; width: 180px; height: 129px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>

													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<p style="color:#554339;font-size:14px;line-height:180%;margin:0;text-align:left;font-family:Arial;">Brussels sprout lagos bologi green bean kuka peanut; soybeam elephant garlic. Bitter melon avocado, dabdelion chinese mallow indian prea mustard, spinach.</p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
													<td style="vertical-align:top;padding-top:20px;padding-right:2px;padding-bottom:15px;padding-left:2px;" width="33%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td align="center" style="vertical-align: top;">
																			<div><img src="http://localhost/coreboscrm/storage/kcimages/images/default_Image.png" style="border: medium none; width: 180px; height: 129px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>

													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="background:#FFFFFF;padding-top:0px;padding-right:5px;padding-bottom:0px;padding-left:5px;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<p style="color:#554339;font-size:14px;line-height:180%;margin:0;text-align:left;font-family:Arial;">Prairie turnip potato tatsoi cauliflower, elephant garlic ginger kai-lan earthnut pea lentil american groundnut chicory kohlrabi!</p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
													<td style="vertical-align:top;padding-top:20px;padding-right:20px;padding-bottom:15px;padding-left:2px;" width="33%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td align="center" style="vertical-align: top;">
																			<div><img src="http://localhost/coreboscrm/storage/kcimages/images/default_Image.png" style="border: medium none; width: 180px; height: 129px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>

													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<p style="color:#554339;font-size:14px;line-height:180%;margin:0;text-align:left;font-family:Arial;">Bitterleaf chard camas celery yardlong bean - sierra leone bologi collard greens - runner bean turnip greens kohlrabi dandelion!</p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>

				<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
					<tbody>
						<tr>
							<td align="center">
							<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFF8CE;" width="600">
								<tbody>
									<tr>
										<td>
										<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
											<tbody>
												<tr>
													<td style="vertical-align:top;padding-top:8px;padding-right:35px;padding-bottom:8px;padding-left:35px;" width="100%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="background:#FFF8CE;padding-top:12px;padding-right:0px;padding-bottom:12px;padding-left:0px;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFF8CE;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<p style="margin-top:0;margin-right:0;margin-bottom:0;margin-lef:0;text-align:center;font-family:Arial;font-size:13px;line-height:100%;color:#934902;"><a href="https://corebos.com" style="color:#934902;font-family:Arial;font-size:13px;line-height:100%;margin:0;text-align:center;">SUBSCRIBE TO RSS</a><span style="font-family:Arial;font-size:13px;line-height:100%;margin:0;text-align:center;color:#808080;">&nbsp;|&nbsp;</span><a href="http://facebook.com" style="color:#934902;font-family:Arial;font-size:13px;line-height:100%;margin:0;text-align:center;">LIKE US ON FACEBOOK</a><span style="font-family:Arial;font-size:13px;line-height:100%;margin:0;text-align:center;color:#808080;">&nbsp;|&nbsp;</span><a href="http://twitter.com" style="color:#934902;font-family:Arial;font-size:13px;line-height:100%;margin:0;text-align:center;">FOLLOW US ON TWITTER</a></p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>

				<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
					<tbody>
						<tr>
							<td align="center">
							<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;" width="600">
								<tbody>
									<tr>
										<td>
										<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
											<tbody>
												<tr>
													<td style="vertical-align:top;padding-top:10px;padding-right:30px;padding-bottom:10px;padding-left:0px;" width="50%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#3a2c23;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#3a2c23;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<p style="color:#bcbb9f;font-family:Arial;font-size:12px;line-height:165%;margin:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#cd9a6b">here</span></a></p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
													<td style="vertical-align:top;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:50px;" width="50%">
													<div>
													<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#3a2c23;">
																<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#3a2c23;" width="100%">
																	<tbody>
																		<tr>
																			<td style="vertical-align:top;">
																			<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																			<p style="color:#bcbb9f;font-family:Arial;font-size:12px;line-height:165%;margin:0;text-align:left;">Company Details</p>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>
													</div>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</td>
						</tr>
					</tbody>
				</table>
				</div>','templateonlytext' => ''),
							array('reference' => 'Fresh Blue','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Fresh Blue','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#06BDE5;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#06BDE5;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#06BDE5;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#FFFFFF;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#1a7385;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#06BDE5;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#06BDE5;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="26" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/bdfdc8edd2972b6f9e598f76a1667783png" style="border:medium none;width:502px;height:26px;resize:none;position:relative;display:block;top:0px;left:0px;" width="502" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="69">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#06BDE5;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="93" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/d0c094a9014210ff6e8e1a9a0987bcc2png" style="border:medium none;width:69px;height:93px;resize:none;position:relative;display:block;top:0px;left:0px;" width="69" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;background:#FFFFFF;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="right" style="vertical-align:top;">
																						<div class="text"><span style="display:block;width:100%;font-size:48px;line-height:125%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;font-style:italic;font-family:Georgia,Times New Roman,Times,serif;color:#06BDE5;font-weight:bold;">Fresh Veggies Co.</span> <span style="display:block;width:100%;font-family:Georgia,Times New Roman,Times,serif;font-size:24px;line-height:125%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:right;font-style:italic;color:#06BDE5;font-weight:bold;">Organic Veggies For You</span></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;" width="71">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#06BDE5;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="93" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/443d4a7ec662c495d5526cfa3c8da017png" style="border:medium none;width:71px;height:93px;resize:none;position:relative;display:block;top:0px;left:0px;" width="71" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:0px;padding-right:0px;padding-bottom:30px;padding-left:0px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#06BDE5;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="32" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/e2a64ccc601c49dfeeaa0c3ed70f9b1apng" style="border:medium none;width:600px;height:32px;resize:none;position:relative;display:block;top:0px;left:0px;" width="600" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="254">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#06BDE5;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="186" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/1ffda781601c90f8d1bb72b2d6d74ff0png" style="border:medium none;width:254px;height:186px;resize:none;position:relative;display:block;top:0px;left:0px;" width="254" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:10px;padding-right:15px;padding-bottom:10px;padding-left:15px;" width="">&nbsp;</td>
																<td style="vertical-align:top;" width="316">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#06BDE5;padding-top:0px;padding-right:0px;padding-bottom:10px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h2 style="width:100%;font-size:30px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-style:italic;font-family:Georgia,Times New Roman,Times,serif;"><span style="color:#FFFFFF;font-weight:bold;">Heading 1</span></h2>

																						<p style="color:#FFFFFF;font-family:Georgia,Times New Roman,Times,serif;font-size:16px;line-height:125%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Asparagus ginger napa groundnut, radishes garbanzo lettuce endive new zealand spinach radicchio chinese cabbage. Mooli sea kale cardoon manioc. Celeriac, lagos bologi gobo.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#06BDE5;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td align="right" style="vertical-align:top;">
																						<table border="0" cellpadding="10" cellspacing="0" style="-moz-border-radius:5px;-webkit-border-radius:5px;background-color:#FFFFFF;border-radius:5px;">
																							<tbody>
																								<tr>
																									<td align="center" style="text-align:center;text-decoration:none;" valign="middle">
																									<div class="text">
																									<p style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-weight:bold;color:#06BDE5;font-family:Georgia,Times New Roman,Times,serif;font-size:16px;line-height:125%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-style:italic;text-align:left;"><a href="https://corebos.com" style="font-weight:bold;text-decoration:none;color:#06BDE5;font-family:Georgia,Times New Roman,Times,serif;font-size:16px;line-height:125%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-style:italic;text-align:left;">Learn More &raquo;</a></p>
																									</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:15px;padding-right:0px;padding-bottom:0px;padding-left:0px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#06BDE5;padding-top:15px;padding-right:0px;padding-bottom:15px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:24px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-style:italic;font-family:Georgia,Times New Roman,Times,serif;"><span style="color:#FFFFFF;font-weight:bold;">Heading 2</span></h3>

																						<p style="color:#FFFFFF;font-family:Georgia,Times New Roman,Times,serif;font-size:16px;line-height:125%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Summer purslane polk spinach new zealand spinach guar tepary bean napa cabbage gumbo, broadleaf arrowhead. Kale tatsoi prairie turnip ti; soko prairie turnip beet greens - tomatillo daikon celeriac.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#06BDE5;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="198" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/9ac79ff043e449e75710d2f6eca70d0apng" style="border:medium none;width:600px;height:198px;resize:none;position:relative;display:block;top:0px;left:0px;" width="600" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#06BDE5;padding-top:15px;padding-right:0px;padding-bottom:15px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#FFFFFF;font-family:Georgia,Times New Roman,Times,serif;font-size:16px;line-height:125%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Bell pepper water spinach sierra leone bologi lentil fava bean parsnip celery common bean bitter melon. Prussian asparagus catsear; sweet potato or kumara tigernut okra garbanzo pak choy water spinach eggplant polk ensete sorrel. Brinjal potato bitter gourd. Arugula cress, common bean. Chinese artichoke bitter melon broccoli rabe sierra leone bologi, eggplant - summer purslane summer purslane lizard tail scallion.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#06BDE5;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td align="right" style="vertical-align:top;">
																						<table border="0" cellpadding="10" cellspacing="0" style="-moz-border-radius:5px;-webkit-border-radius:5px;background-color:#FFFFFF;border-radius:5px;">
																							<tbody>
																								<tr>
																									<td align="center" style="text-align:center;text-decoration:none;" valign="middle">
																									<div class="text">
																									<p style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-weight:bold;color:#06BDE5;font-family:Georgia,Times New Roman,Times,serif;font-size:16px;line-height:125%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-style:italic;text-align:left;"><a href="https://corebos.com" style="font-weight:bold;text-decoration:none;color:#06BDE5;font-family:Georgia,Times New Roman,Times,serif;font-size:16px;line-height:125%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-style:italic;text-align:left;">Learn More &raquo;</a></p>
																									</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:30px;padding-right:0px;padding-bottom:35px;padding-left:0px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#06BDE5;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#FFFFFF;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#1a7385">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:35px;" width="0">&nbsp;</td>
																<td style="vertical-align:top;padding-top:30px;padding-right:0px;padding-bottom:35px;padding-left:0px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#06BDE5;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#06BDE5;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#FFFFFF;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Company Details</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'Conference Black','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Conference Black','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#000000;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#000000;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#000000;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#000000;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="font-family:helvetica;color:#D5D5D5;text-align:center;font-size:12px;margin:0;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#EDBF00;font-family:helvetica;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#000000;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#000000;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#000000;" width="100%">
																				<tbody>
																					<tr>
																						<td style="width:100%;border-top-color:#8A8A8A;border-top-style:solid;border-top-width:1px;" width="100%">&nbsp;</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#000000;padding-top:20px;padding-right:0px;padding-bottom:20px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#000000;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="font-family:helvetica;font-size:40px;line-height:112%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;"><span style="color:#FFFFFF;font-weight:bold;">Typography Conference 2012</span></h1>

																						<p style="font-family:helvetica;color:#FFFFFF;font-size:18px;text-align:right;margin-bottom:5px;margin-left:0;margin-top: 10px;">The best type conference this year&nbsp;&nbsp;&nbsp;&nbsp;</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#000000;padding-top:5px;padding-right:0px;padding-bottom:3px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#000000;" width="100%">
																				<tbody>
																					<tr>
																						<td style="width:100%;border-top-color:#8A8A8A;border-top-style:solid;border-top-width:1px;" width="100%">&nbsp;</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#000000;padding-top:1px;padding-right:0px;padding-bottom:1px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#000000;" width="100%">
																				<tbody>
																					<tr>
																						<td style="width:100%;border-top-color:#8A8A8A;border-top-style:solid;border-top-width:1px;" width="100%">&nbsp;</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#000000;padding-top:25px;padding-right:0px;padding-bottom:25px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#000000;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h2 style="width:100%;font-size:25px;font-family:helvetica;margin-top:10px;margin-right:0;margin-bottom:15px;margin-left:0;text-align:left;"><span style="color:#FFFFFF;font-weight:bold;">Heading 1</span></h2>

																						<p style="color:#BDBDBD;font-family:helvetica;font-size:16px;line-height:150%;margin:0;text-align:left;">Beet greens kohlrabi, orache;tepary bean winter melon fluted pumpkin epazote sorrel chinese artichoke, soko. Ulluco;winter purslane mung bean, parsnip sierra leone bologi. Spinach sweet pepper, cauliflower.</p>
																						&nbsp;

																						<p style="color:#BDBDBD;font-family:helvetica;font-size:16px;line-height:150%;margin:0;text-align:left;">Tinda! Swiss chard, pigeon pea urad bean turnip moth bean;sweet corn aka corn;aka maize shallot black-eyed pea sierra leone bologi sea kale watercress squash. Tarwi, broadleaf arrowhead - bok choy garbanzo.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#000000;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#000000;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="197" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/f70e4db660da5933462cd3d2fc15d586jpg" style="border:medium none;width:600px;height:197px;resize:none;position:relative;display:block;top:0px;left:0px;" width="600" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#000000;padding-top:30px;padding-right:0px;padding-bottom:20px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#000000;" width="100%">
																				<tbody>
																					<tr>
																						<td style="width:100%;border-top-color:#8A8A8A;border-top-style:solid;border-top-width:1px;" width="100%">&nbsp;</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#000000;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#000000;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#BDBDBD;font-family:helvetica;font-size:16px;line-height:150%;margin:0;text-align:left;">Beet greens kohlrabi, orache;tepary bean winter melon fluted pumpkin epazote sorrel chinese artichoke, soko. Ulluco;winter purslane mung bean, parsnip sierra leone bologi. Spinach sweet pepper, cauliflower.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#000000;padding-top:20px;padding-right:20px;padding-bottom:20px;padding-left:20px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#000000;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align:top;">
																						<table border="0" cellpadding="10" cellspacing="0" style="-moz-border-radius:2px;-webkit-border-radius:2px;background-color:#ffcc00;border-radius:2px;">
																							<tbody>
																								<tr>
																									<td align="center" style="text-align:center;text-decoration:none;padding-left:20px;padding-right:20px;" valign="middle">
																									<div class="text" style="text-align:center;"><a href="https://corebos.com" style="font-weight:bold;font-family:helvetica;text-decoration:none;font-size:26px;margin:0;text-align:center;line-height:150%;"><span style="color:#010101;">Sign up now</span></a></div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:40px;padding-right:0px;padding-bottom:20px;padding-left:0px;" width="60%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#000000;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#000000;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#BDBDBD;font-family:helvetica;font-size:12px;line-height:170%;margin:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span href="[unsubscribe]" style="text-decoration:underline;color:#EDBF00;font-family:helvetica;"> here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:40px;padding-right:0px;padding-bottom:20px;padding-left:0px;" width="40%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#000000;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#000000;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#BDBDBD;font-family:helvetica;font-size:12px;line-height:170%;margin:0;text-align:left;">Company Details</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'Event Green','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Event Green','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#3E5916;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#3E5916;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#3E5916;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#3E5916;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#FFFFFF;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#ffd700;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#3E5916;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#3E5916;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#3E5916;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img src="http://localhost/coreboscrm/storage/kcimages/images/hlineorange.gif" style="border: medium none; width: 600px; height: 33px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:25px;padding-right:25px;padding-bottom:25px;padding-left:25px;background:#94A606;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#94A606;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#94A606;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="margin-top:0;margin-right:0;margin-bottom:10px;margin-left:0;line-height:120%;font-size:40px;"><span style="color:#FFFFFF;font-family:Arial;font-weight:bold;">The Happy Campers Club</span></h1>

																						<p style="color:#FFFFFF;font-size:16px;line-height:130%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial;">Winter melon fat hen catsear okra; greater plantain tarwi mooli yam collard greens. Mung bean winter purslane tomatillo skirret chickpea. Squash swede moth bean jerusalem artichoke bitterleaf, lettuce chickpea komatsuna. Black-eyed pea ceylon spinach, summer purslane.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:11px;padding-right:0px;padding-bottom:11px;padding-left:0px;" width="100%">&nbsp;</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:20px;padding-right:15px;padding-bottom:20px;padding-left:20px;background:#FFFFFF;" width="57%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="208" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/514933f271157e97cbd6da86332af94e.png" style="border:medium none;width:311px;height:208px;resize:none;position:relative;display:block;top:0px;left:0px;" width="311" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:20px;padding-right:20px;padding-bottom:20px;padding-left:0px;background:#FFFFFF;" width="43%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="margin-top:0;margin-right:0;margin-bottom:10px;margin-left:0;line-height:130%;font-size:22px;"><span style="color:#3E5916;font-family:Arial;font-weight:bold;">About the Event</span></h3>

																						<p style="font-family:Arial;color:#3E5916;font-size:12px;line-height:165%;margin-top:0;margin-right:0;margin-bottom:15px;margin-left:0;text-align:left;">Yam okra catsear water chestnut scalion pak choy yardlong bean komatsuna plectranthus. Sierra Leone bologi tigemut cauliflower fiddlehead yarrow bitter gourd, Fluted pumpkin sea kale elephant. Lotus root pak choy tepary bean, tepary bean komatsuna tinda lentil camas.</p>

																						<div style="text-align:right"><a href="https://corebos.com" style="color:#3E5916;font-size:13px;line-height:180%;text-align:right;font-family:Arial;">Learn more &raquo;</a></div>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:11px;padding-right:0px;padding-bottom:11px;padding-left:0px;" width="100%">&nbsp;</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:20px;padding-right:15px;padding-bottom:20px;padding-left:20px;background:#FFFFFF;" width="43%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="margin-top:0;margin-right:0;margin-bottom:10px;margin-left:0;line-height:130%;font-size:22px;"><span style="color:#3E5916;font-family:Arial;font-weight:bold;">Date &amp; Location</span></h3>

																						<p style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial;color:#3E5916;font-size:17px;"><span style="font-family:Arial;color:#3E5916;font-size:17px;font-weight:bold;">May 20, 2012</span><br />
																						<span style="font-family:Arial;color:#3E5916;font-size:17px;">Fort Lauderdale, FL</span><br />
																						<br />
																						<span style="font-family:Arial;color:#3E5916;font-size:17px;font-weight:bold;">June 08, 2012</span><br />
																						<span style="font-family:Arial;color:#3E5916;font-size:17px;">Las Vegas, NV</span><br />
																						<br />
																						<span style="font-family:Arial;color:#3E5916;font-size:17px;font-weight:bold;">July 15, 2012</span><br />
																						<span style="font-family:Arial;color:#3E5916;font-size:17px;">Raleigh, NC</span></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:20px;padding-right:20px;padding-bottom:20px;padding-left:0px;background:#FFFFFF;" width="57%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="208" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/7a1ac05a524782d66ec8781153f595f9.png" style="border:medium none;width:311px;height:208px;resize:none;position:relative;display:block;top:0px;left:0px;" width="311" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:11px;padding-right:0px;padding-bottom:11px;padding-left:0px;" width="100%">&nbsp;</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:20px;padding-right:15px;padding-bottom:20px;padding-left:20px;background:#FFFFFF;" width="57%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="208" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/a6780dc194cdd03ea6a6633948c8215b.png" style="border:medium none;width:311px;height:208px;resize:none;position:relative;display:block;top:0px;left:0px;" width="311" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:20px;padding-right:20px;padding-bottom:20px;padding-left:0px;background:#FFFFFF;" width="43%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="margin-top:0;margin-right:0;margin-bottom:10px;margin-left:0;line-height:130%;font-size:22px;"><span style="color:#3E5916;font-family:Arial;font-weight:bold;">For More Information</span></h3>

																						<p style="font-family:Arial;color:#3E5916;font-size:12px;line-height:165%;margin-top:0;margin-right:0;margin-bottom:15px;margin-left:0;text-align:left;">Plectranthus welsh onion leaves earthnut pea. Fluted pumpkin - lagos bologi - lambs lettuce cucumber collard greens. Horseradish greater plantain camas endive; tatsoi moth bean lentil cucumber, florence fennel onion fat hen.</p>

																						<div style="text-align:right"><a href="https://corebos.com" style="color:#3E5916;font-size:13px;line-height:180%;text-align:right;font-family:Arial;">Learn more &raquo;</a></div>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:11px;padding-right:0px;padding-bottom:11px;padding-left:0px;" width="100%">&nbsp;</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:15px;padding-right:0px;padding-bottom:15px;padding-left:15px;background:#FFFFFF;" width="57%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#3E5916;font-family:Arial,sans-serif;font-size:12px;line-height:160%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#3E5916">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:0px;background:#FFFFFF;" width="43%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#3E5916;font-family:Arial,sans-serif;font-size:12px;line-height:160%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Company Details</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'Presentation','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Presentation','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#FEE9BE;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FEE9BE;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FEE9BE;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#BD5F5F;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#5ca8c7;">View it</span></a> in your browser.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FEE9BE;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:30px;padding-right:0px;padding-bottom:30px;padding-left:0px;" width="263">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FEE9BE;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="181" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/bcea077f99164f3cf4631899ef4f235c.png" style="border:medium none;width:263px;height:181px;resize:none;position:relative;display:block;top:0px;left:0px;" width="263" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:30px;padding-right:0px;padding-bottom:30px;padding-left:5px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FEE9BE;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="width:100%;font-size:120px;line-height:100%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;text-align:left;font-family:Arial;"><span style="color:#BD5F5F;font-weight:bold;">Hello.</span></h1>

																						<p style="color:#BD5F5F;font-size:28px;line-height:110%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-weight:bold;font-family:Arial;">Florence fennel tarwi, beet greens.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:40px;padding-right:0px;padding-bottom:40px;padding-left:0px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FEE9BE;padding-top:7px;padding-right:0px;padding-bottom:7px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																							<tbody>
																								<tr>
																									<td align="left" style="vertical-align:top;text-align:left;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;" width="41px">&nbsp;</td>
																									<td style="background-color:#BF5F60;vertical-align:middle;" valign="middle">
																									<div class="text" style="word-wrap:break-word;vertical-align:middle;"><span style="font-size:25px;color:#FFFFFF;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;padding-top:0;padding-right:10px;padding-bottom:0;padding-left:10px;text-align:left;line-height:130%;vertical-align:middle;font-family:Arial;font-weight:bold;">Who We Are</span></div>
																									</td>
																									<td align="left" style="vertical-align:top;text-align:left;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;" width="71px">&nbsp;</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FEE9BE;padding-top:7px;padding-right:0px;padding-bottom:10px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#BD5F5F;font-size:16px;line-height:140%;margin-top:0;margin-right:0;margin-bottom:30px;margin-left:0;text-align:left;font-family:Arial;">Veggies sunt bona vobis, proinde vos postulo esse magis summer purslane spinach salad leek wattle seed turnip greens corn.</p>

																						<p style="color:#BD5F5F;font-size:16px;line-height:140%;margin-top:0;margin-right:0;margin-bottom:15px;margin-left:0;text-align:left;font-family:Arial;">Sorrel beet greens okra bamboo shoot prairie turnip sweet pepper turnip greens water chestnut summer purslane celtuce broccoli. Sorrel pea sprouts catsear avocado spinach welsh onion radicchio pea kombu courgette mustard tatsoi tigernut.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FEE9BE;padding-top:7px;padding-right:0px;padding-bottom:7px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																							<tbody>
																								<tr>
																									<td align="left" style="vertical-align:top;text-align:left;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;" width="41px">&nbsp;</td>
																									<td style="background-color:#47A0CC;vertical-align:middle;" valign="middle">
																									<div class="text" style="word-wrap:break-word;vertical-align:middle;"><span style="font-size:25px;color:#FFFFFF;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;padding-top:0;padding-right:10px;padding-bottom:0;padding-left:10px;text-align:left;line-height:130%;vertical-align:middle;font-weight:bold;font-family:Arial;">What We Do</span></div>
																									</td>
																									<td align="left" style="vertical-align:top;text-align:left;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;" width="71px">&nbsp;</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FEE9BE;padding-top:15px;padding-right:0px;padding-bottom:0px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<ul style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:30px;font-family:Arial;">
																							<li style="color:#BD5F5F;font-size:16px;line-height:140%;margin-top:0;margin-right:0;margin-bottom:20px;margin-left:0;text-align:left;">Veggies sunt bona vobis, proinde vos postulo esse magis summer purslane spinach</li>
																							<li style="color:#BD5F5F;font-size:16px;line-height:140%;text-align:left;margin-top:0;margin-right:0;margin-bottom:20px;margin-left:0;">Salad leek wattle seed turnip greens corn.</li>
																							<li style="color:#BD5F5F;font-size:16px;line-height:140%;margin-top:0;margin-right:0;margin-bottom:20px;margin-left:0;text-align:left;">Sorrel beet greens okra bamboo shoot prairie turnip sweet pepper turnip greens water chestnut summer purslane</li>
																						</ul>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FEE9BE;padding-top:7px;padding-right:0px;padding-bottom:7px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																							<tbody>
																								<tr>
																									<td align="left" style="vertical-align:top;text-align:left;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;" width="41px">&nbsp;</td>
																									<td style="background-color:#BF5F60;vertical-align:middle;" valign="middle">
																									<div class="text" style="word-wrap:break-word;vertical-align:middle;"><span style="font-size:25px;color:#FFFFFF;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;padding-top:0;padding-right:10px;padding-bottom:0;padding-left:10px;text-align:left;line-height:130%;vertical-align:middle;font-weight:bold;font-family:Arial;">Why Hire Us</span></div>
																									</td>
																									<td align="left" style="vertical-align:top;text-align:left;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;" width="71px">&nbsp;</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FEE9BE;padding-top:7px;padding-right:0px;padding-bottom:7px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#BD5F5F;font-size:16px;line-height:140%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial;">Veggies sunt bona vobis, proinde vos postulo esse magis summer purslane spinach salad leek wattle seed turnip greens corn.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:0px;padding-right:35px;padding-bottom:0px;padding-left:0px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FEE9BE;padding-top:7px;padding-right:0px;padding-bottom:7px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td align="right" style="vertical-align:top;">
																						<table border="0" cellpadding="0" cellspacing="0" style="text-align:right;border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																							<tbody>
																								<tr>
																									<td align="left" style="vertical-align:top;text-align:left;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;" width="24px"><img src="http://localhost/coreboscrm/storage/kcimages/images/twit.png" style="border: medium none; width: 24px; height: 24px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></td>
																									<td style="vertical-align:bottom;padding-left:10px;">
																									<div class="text" style="word-wrap:break-word;">
																									<div style="color:#5CA8C7;font-size:15px;text-align:left;font-family:Arial;"><a href="http://twitter.com" style="color:#5CA8C7;font-size:15px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial;">Follow Us on Twitter</a></div>
																									</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FEE9BE;padding-top:7px;padding-right:0px;padding-bottom:7px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<table border="0" cellpadding="0" cellspacing="0" style="text-align:left;border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																							<tbody>
																								<tr>
																									<td align="left" style="vertical-align:top;text-align:left;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;" width="24px"><img src="http://localhost/coreboscrm/storage/kcimages/images/fb.png" style="border: medium none; width: 24px; height: 24px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></td>
																									<td style="vertical-align:bottom;padding-left:10px;">
																									<div class="text" style="word-wrap:break-word;text-align:left;">
																									<div style="color:#BF5F60;font-size:15px;text-align:left;font-family:Arial;"><a href="http://facebook.com" style="color:#BF5F60;font-size:15px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial;">Like Us on Facebook</a></div>
																									</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:70px;padding-right:50px;padding-bottom:35px;padding-left:0px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FEE9BE;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#BD5F5F;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#5CA8C7">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:70px;padding-right:0px;padding-bottom:35px;padding-left:0px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FEE9BE;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FEE9BE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#BD5F5F;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Company Dertails</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'We make awesome things','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'We make awesome things','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#402359;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#402359;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#402359;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#BF6161;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial,sans-serif;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#499FD0;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#402359;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:20px;padding-right:15px;padding-bottom:40px;padding-left:0px;" width="76">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#402359;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="149" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/db245ec0d009911b401ed0b453d949e3png" style="border:medium none;width:76px;height:149px;resize:none;position:relative;display:block;top:0px;left:0px;" width="76" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:20px;padding-right:0px;padding-bottom:40px;padding-left:0px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#402359;padding-top:45px;padding-right:0px;padding-bottom:0px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="width:100%;font-size:93px;line-height:100%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="color:#E4CDF7;font-weight:bold">Design Lab</span></h1>

																						<h2 style="width:100%;font-size:30px;line-height:120%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="color:#FFFFFF;font-weight:bold">We make awesome things</span></h2>

																						<p style="color:#FFFFFF;font-family:Arial,sans-serif;font-size:17px;line-height:140%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Spinach tigernut parsnip tatsoi, winter melon american skirret. Fat corn salad cress, turnip water spinach tomato samphire dandelion.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="185">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#402359;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<table border="0" cellpadding="10" cellspacing="0" style="width:100%;border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																							<tbody>
																								<tr>
																									<td style="vertical-align:middle;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;background:#9096D2;word-wrap:break-word;-webkit-border-top-right-radius: 21px;-webkit-border-bottom-right-radius: 21px;-moz-border-radius-topright: 21px;-moz-border-radius-bottomright: 21px;border-top-right-radius: 21px;border-bottom-right-radius: 21px;-webkit-box-shadow:1px 2px 1px #231433;moz-box-shadow:1px 2px 1px #231433;box-shadow:1px 2px 1px #231433;" valign="middle">
																									<div class="text" style="vertical-align:middle;"><span style="font-size:17px;line-height:100%;color:#402359;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;font-weight:bold;">IN THIS ISSUE</span></div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#402359;padding-top:10px;padding-right:0px;padding-bottom:20px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="font-size:15px;line-height:140%;color:#FFFFFF;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">Leek - carrot new zealand spinach greater plantain swiss chard celtuce lettuce prairie turnip. Kurrat kurrat; orache. Chinese artichoke eggplant, bitter melon.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#402359;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<table border="0" cellpadding="10" cellspacing="0" style="width:100%;border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																							<tbody>
																								<tr>
																									<td style="vertical-align:middle;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;background:#9096D2;word-wrap:break-word;-webkit-border-top-right-radius: 21px;-webkit-border-bottom-right-radius: 21px;-moz-border-radius-topright: 21px;-moz-border-radius-bottomright: 21px;border-top-right-radius: 21px;border-bottom-right-radius: 21px;-webkit-box-shadow:1px 2px 1px #231433;moz-box-shadow:1px 2px 1px #231433;box-shadow:1px 2px 1px #231433;" valign="middle">
																									<div class="text" style="vertical-align:middle;"><span style="font-size:17px;line-height:100%;color:#402359;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;font-weight:bold;">IN THE NEWS</span></div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#402359;padding-top:10px;padding-right:0px;padding-bottom:5px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h4 style="font-size:15px;line-height:140%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#FFFFFF;">01/01/2012</span></h4>

																						<p style="font-size:15px;line-height:140%;color:#FFFFFF;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">Turnip greens velvet bean aubergine; jama potato elephant garlic winter.</p>

																						<p style="font-size:15px;line-height:140%;color:#FFFFFF;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">&nbsp;</p>

																						<h4 style="font-size:15px;line-height:140%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#FFFFFF;">01/01/2012</span></h4>

																						<p style="font-size:15px;line-height:140%;color:#FFFFFF;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">Chinese artichoke yardlong bean sweet potato or kumara potato chicory.</p>

																						<p style="font-size:15px;line-height:140%;color:#FFFFFF;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">&nbsp;</p>

																						<h4 style="font-size:15px;line-height:140%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#FFFFFF;">01/01/2012</span></h4>

																						<p style="font-size:15px;line-height:140%;color:#FFFFFF;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">Sweet corn aka corn; aka maize paracress black-eyed pea zucchini.</p>

																						<p style="font-size:15px;line-height:140%;color:#FFFFFF;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">&nbsp;</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#402359;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<table border="0" cellpadding="10" cellspacing="0" style="width:100%;border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																							<tbody>
																								<tr>
																									<td style="vertical-align:middle;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;background:#9096D2;word-wrap:break-word;-webkit-border-top-right-radius: 21px;-webkit-border-bottom-right-radius: 21px;-moz-border-radius-topright: 21px;-moz-border-radius-bottomright: 21px;border-top-right-radius: 21px;border-bottom-right-radius: 21px;-webkit-box-shadow:1px 2px 1px #231433;moz-box-shadow:1px 2px 1px #231433;box-shadow:1px 2px 1px #231433;" valign="middle">
																									<div class="text" style="vertical-align:middle;"><span style="font-size:17px;line-height:100%;color:#402359;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;font-weight:bold;">GET IN TOUCH</span></div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#402359;padding-top:10px;padding-right:0px;padding-bottom:13px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="font-size:15px;line-height:140%;color:#FFFFFF;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">Pumpkin tarwi ricebean chaya. Beet greens pea sprouts, tatsoi - arracacha.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#402359;padding-top:7px;padding-right:0px;padding-bottom:7px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<table border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																							<tbody>
																								<tr>
																									<td align="left" style="vertical-align:top;text-align:left;padding:0;" width="24px"><img src="http://localhost/coreboscrm/storage/kcimages/images/twitblue.png" style="border: medium none; width: 24px; height: 24px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></td>
																									<td style="vertical-align:top;padding-left:10px;">
																									<div class="text" style="word-wrap:break-word;">
																									<div style="font-size:15px;line-height:140%;color:#9096D2;text-align:left;font-family:Arial,sans-serif;"><a href="http://twitter.com" style="font-size:15px;line-height:140%;color:#9096D2;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">Follow Us on Twitter</a></div>
																									</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#402359;padding-top:7px;padding-right:0px;padding-bottom:7px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<table border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;border-spacing:0px;table-layout:fixed;">
																							<tbody>
																								<tr>
																									<td align="left" style="vertical-align:top;text-align:left;padding:0;" width="24px"><img src="http://localhost/coreboscrm/storage/kcimages/images/fbblue.png" style="border: medium none; width: 24px; height: 24px; resize: none; position: relative; display: block; top: 0px; left: 0px;" /></td>
																									<td style="vertical-align:top;padding-left:10px;">
																									<div class="text" style="word-wrap:break-word;">
																									<div style="font-size:15px;line-height:140%;color:#9096D2;text-align:left;font-family:Arial,sans-serif;"><a href="http://facebook.com" style="font-size:15px;line-height:140%;color:#9096D2;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">Like Us on Facebook</a></div>
																									</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;" width="25">&nbsp;</td>
																<td style="vertical-align:top;background:#FFFFFF;-webkit-border-radius:5px;-moz-border-radius:5px;border-radius:5px;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;" width="370">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:10px;padding-right:3px;padding-bottom:15px;padding-left:3px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h2 style="width:100%;font-size:30px;line-height:160%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#402359;">The Lab</span></h2>

																						<p style="font-size:15px;line-height:140%;color:#402359;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">Tarwi arracacha arracacha jerusalem artichoke, sea beet celery, yam corn salad, chinese cabbage endive corn salad. Komatsuna earthnut pea nopal; burdock rutabaga. Kurrat, swede ulluco plectranthus lettuce garbanzo. Summer purslane courgette scallion rutabaga.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#989DD4;padding-top:7px;padding-right:7px;padding-bottom:7px;padding-left:7px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div><img height="238" src="http://staticnl.sendgrid.com/uploads/5ee921f0f6db354cf4424671f4d084a6/f19317c08ab57edf2ca0f78cef722b90png" style="border:medium none;width:356px;height:238px;resize:none;position:relative;display:block;top:0px;left:0px;" width="356" /></div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:3px;padding-bottom:10px;padding-left:3px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="font-size:15px;line-height:140%;color:#402359;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">Zucchini miner lettuce, bok choy chard. Ginger orache celtuce, florence fennel. Lettuce jama, sierra leone bologi celeriac daikon arugula, catsear. Okra gobo yarrow moth bean, epazote.</p>

																						<p style="font-size:15px;line-height:140%;color:#402359;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">&nbsp;</p>

																						<h2 style="width:100%;font-size:30px;line-height:160%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#402359;">Vote For Us</span></h2>

																						<p style="font-size:15px;line-height:140%;color:#402359;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">Beetroot, chinese mallow potato mooli land cress. Tomato tomatillo, sierra leone bologi parsnip ceylon spinach. Tinda yacn, chard chicory prussian asparagus.</p>

																						<p style="font-size:15px;line-height:140%;color:#402359;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">&nbsp;</p>

																						<p style="font-size:15px;line-height:140%;color:#402359;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;">Chicory pea silver beet welsh onion radicchio azuki bean kale chard gourd bitterleaf celery artichoke bell pepper dandelion cabbage scallion napa cabbage. Asparagus celery sea lettuce bunya nuts corn.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:60px;padding-right:20px;padding-bottom:0px;padding-left:0px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#402359;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#BF6161;text-align:left;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial,sans-serif;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#499FD0">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:60px;padding-right:0px;padding-bottom:0px;padding-left:20px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#402359;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#402359;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#BF6161;text-align:left;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial,sans-serif;">Company Details</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'Basic Template 1','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Basic Template 1','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#ddd;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial,sans-serif;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="width:100%;font-size:42px;line-height:120%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Email Title</span></h1>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:35px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h2 style="width:100%;font-size:30px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h2>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:16px;line-height:160%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Asparagus ginger american groundnut, radish garbanzo lettuce endive new zealand spinach radicchio chinese cabbage ricebean west indian gherkin. Mooli sea kale cardoon manioc. Celeriac, lagos bologi gobo dolichos bean indian pea cress, beet greens icama bitter gourd lotus root, runner bean fat hen chaya cassava.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:35px;" width="33%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:0px;padding-right:35px;padding-bottom:0px;padding-left:35px;" width="33%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:0px;padding-right:35px;padding-bottom:0px;padding-left:0px;" width="33%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:20px;padding-left:35px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:13px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;"><a href="http://twitter.com" style="color:#002AFF;">Follow Us on Twitter</a> | <a href="http://facebook.com" style="color:#002AFF;">Like Us on Facebook</a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:10px;padding-bottom:35px;padding-left:35px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:10px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Company Details</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'Basic Template 2','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Basic Template 2','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#ddd;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial,sans-serif;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="width:100%;font-size:42px;line-height:120%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Email Title</span></h1>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:35px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h2 style="width:100%;font-size:30px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h2>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:16px;line-height:160%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Asparagus ginger american groundnut, radish garbanzo lettuce endive new zealand spinach radicchio chinese cabbage ricebean west indian gherkin. Mooli sea kale cardoon manioc. Celeriac, lagos bologi gobo dolichos bean indian pea cress, beet greens icama bitter gourd lotus root, runner bean fat hen chaya cassava.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:10px;padding-right:15px;padding-bottom:10px;padding-left:35px;" width="150">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:0px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:10px;padding-right:15px;padding-bottom:10px;padding-left:35px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:0px;" width="150">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:10px;padding-right:15px;padding-bottom:10px;padding-left:35px;" width="150">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:0px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:20px;padding-left:35px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:13px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;"><a href="http://twitter.com" style="color:#002AFF;">Follow Us on Twitter</a> | <a href="http://facebook.com" style="color:#002AFF;">Like Us on Facebook</a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:10px;padding-bottom:35px;padding-left:35px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:10px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Company Details</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'Basic Template 3','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Basic Template 3','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#ddd;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial,sans-serif;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:0px;padding-right:0px;padding-bottom:10px;padding-left:0px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="width:100%;font-size:42px;line-height:120%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Email Title</span></h1>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:10px;padding-right:15px;padding-bottom:10px;padding-left:35px;" width="230">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:0px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h2 style="width:100%;font-size:30px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h2>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:16px;line-height:160%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Asparagus ginger american tarwi napa groundnut, radish garbanzo lettuce endive new zealand spinach radicchio chinese cabbage ricebean west indian gherkin. Mooli sea kale cardoon manioc. Celeriac, lagos bologi gobo.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:35px;" width="150">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:15px;padding-right:35px;padding-bottom:15px;padding-left:0px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:35px;" width="150">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:15px;padding-right:35px;padding-bottom:15px;padding-left:0px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:20px;padding-left:35px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:13px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;"><a href="http://twitter.com" style="color:#002AFF;">Follow Us on Twitter</a> | <a href="http://facebook.com" style="color:#002AFF;">Like Us on Facebook</a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:10px;padding-bottom:35px;padding-left:35px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:10px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Company Details</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'Basic Template 4','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Basic Template 4','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#ddd;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial,sans-serif;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:0px;padding-right:0px;padding-bottom:10px;padding-left:0px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="width:100%;font-size:42px;line-height:120%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Email Title</span></h1>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:10px;padding-right:15px;padding-bottom:10px;padding-left:35px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h2 style="width:100%;font-size:30px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h2>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:16px;line-height:160%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Asparagus ginger american tarwi napa groundnut, radish garbanzo lettuce endive new zealand spinach radicchio chinese cabbage ricebean west indian gherkin. Mooli sea kale cardoon manioc. Celeriac, lagos bologi gobo.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:0px;" width="230">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:35px;" width="150">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:15px;padding-right:35px;padding-bottom:15px;padding-left:0px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:35px;" width="150">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:15px;padding-right:35px;padding-bottom:15px;padding-left:0px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:20px;padding-left:35px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:13px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;"><a href="http://twitter.com" style="color:#002AFF;">Follow Us on Twitter</a> | <a href="http://facebook.com" style="color:#002AFF;">Like Us on Facebook</a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:10px;padding-bottom:35px;padding-left:35px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:10px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">Company Details</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'Basic Template 5','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Basic Template 5','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#ddd;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial,sans-serif;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="width:100%;font-size:42px;line-height:120%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Email Title</span></h1>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:35px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h2 style="width:100%;font-size:30px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h2>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:16px;line-height:160%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Asparagus ginger american groundnut, radish garbanzo lettuce endive new zealand spinach radicchio chinese cabbage ricebean west indian gherkin. Mooli sea kale cardoon manioc. Celeriac, lagos bologi gobo dolichos bean indian pea cress, beet greens icama bitter gourd lotus root, runner bean fat hen chaya cassava.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:10px;padding-right:15px;padding-bottom:10px;padding-left:35px;" width="150">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:0px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:10px;padding-right:15px;padding-bottom:10px;padding-left:35px;" width="150">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:0px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:10px;padding-right:15px;padding-bottom:10px;padding-left:35px;" width="150">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:0px;" width="">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:20px;padding-left:35px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:13px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;"><a href="http://twitter.com" style="color:#002AFF;">Follow Us on Twitter</a> | <a href="http://facebook.com" style="color:#002AFF;">Like Us on Facebook</a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:10px;padding-bottom:35px;padding-left:35px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:10px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">Company Details</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'Basic Template 7','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Basic Template 7','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#ddd;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial,sans-serif;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:0px;padding-right:0px;padding-bottom:10px;padding-left:0px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="width:100%;font-size:42px;line-height:120%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Email Title</span></h1>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:35px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h2 style="width:100%;font-size:30px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h2>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:16px;line-height:160%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Asparagus ginger american groundnut, radish garbanzo lettuce endive new zealand spinach radicchio chinese cabbage ricebean west indian gherkin. Mooli sea kale cardoon manioc. Celeriac, lagos bologi gobo dolichos bean indian pea cress, beet greens icama bitter gourd lotus root, runner bean fat hen chaya cassava.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:35px;" width="225px">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:35px;" width="">&nbsp;</td>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:0px;" width="225px">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:20px;padding-left:35px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:13px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;"><a href="http://twitter.com" style="color:#002AFF;">Follow Us on Twitter</a> | <a href="http://facebook.com" style="color:#002AFF;">Like Us on Facebook</a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:10px;padding-bottom:35px;padding-left:35px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:10px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">Company Details</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'Basic Template 6','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Basic Template 6','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#ddd;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial,sans-serif;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:0px;padding-right:0px;padding-bottom:10px;padding-left:0px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="width:100%;font-size:42px;line-height:120%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Email Title</span></h1>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:35px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:10px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:35px;" width="530px">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:10px;padding-right:35px;padding-bottom:10px;padding-left:35px;" width="530px">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:20px;padding-left:35px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:13px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;"><a href="http://twitter.com" style="color:#002AFF;">Follow Us on Twitter</a> | <a href="http://facebook.com" style="color:#002AFF;">Like Us on Facebook</a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:10px;padding-bottom:35px;padding-left:35px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:10px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">Company Details</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'Basic Template 8','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Basic Template 8','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#ddd;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial,sans-serif;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:0px;padding-right:0px;padding-bottom:10px;padding-left:0px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="width:100%;font-size:42px;line-height:120%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Email Title</span></h1>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;background:#EEEEEE;" width="220">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#EEEEEE;padding-top:10px;padding-right:25px;padding-bottom:10px;padding-left:25px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#EEEEEE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<ul style="color:#808080;font-family:Arial,sans-serif;font-size:13px;margin-top:0;margin-right:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;text-align:left;">
																							<li style="line-height:170%;">Welsh onion elephant foot yam</li>
																							<li style="line-height:170%;">Kohlrabi earthnut pea broadleaf</li>
																							<li style="line-height:170%;">Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal.</li>
																							<li style="line-height:170%;">Horse gram land cress sea kale</li>
																							<li style="line-height:170%;">Arracacha celtuce fluted pumpkin</li>
																						</ul>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#EEEEEE;padding-top:10px;padding-right:25px;padding-bottom:10px;padding-left:25px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#EEEEEE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:13px;line-height:180%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:0px;padding-right:30px;padding-bottom:10px;padding-left:30px;" width="320">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h2 style="width:100%;font-size:30px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h2>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:16px;line-height:160%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Asparagus ginger american groundnut, radish garbanzo lettuce endive new zealand spinach radicchio chinese cabbage ricebean west indian gherkin. Mooli sea kale cardoon manioc. Celeriac, lagos bologi gobo dolichos bean indian pea cress, beet greens icama bitter gourd lotus root, runner bean fat hen chaya cassava.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<table cellpadding="0" cellspacing="0" class="designer-subrow" style="width:100%;border-collapse:collapse;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td class="sub-width-cell" style="vertical-align:top;border-color:#fff;border-style:solid;border-width:0 10px 0 0;" width="150px">
																			<div>
																			<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																				<tbody>
																					<tr>
																						<td style="background:#FFFFFF;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:0px;">
																						<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																							<tbody>
																								<tr>
																									<td align="center" style="vertical-align: top;">
																									<div>image here</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</div>

																			<div>
																			<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																				<tbody>
																					<tr>
																						<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																						<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																							<tbody>
																								<tr>
																									<td style="vertical-align:top;">
																									<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																									<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																									<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress.</p>
																									</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</div>
																			</td>
																			<td class="sub-width-cell" style="vertical-align:top;" width="">&nbsp;</td>
																			<td class="sub-width-cell" style="vertical-align:top;border-color:#fff;border-style:solid;border-width:0 0 0 10px;" width="150px">
																			<div>
																			<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																				<tbody>
																					<tr>
																						<td style="background:#FFFFFF;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:0px;">
																						<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																							<tbody>
																								<tr>
																									<td align="center" style="vertical-align: top;">
																									<div>image here</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</div>

																			<div>
																			<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																				<tbody>
																					<tr>
																						<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																						<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																							<tbody>
																								<tr>
																									<td style="vertical-align:top;">
																									<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																									<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																									<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress.</p>
																									</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:10px;padding-bottom:35px;padding-left:35px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:10px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">Company Details</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
							array('reference' => 'Basic Template 9','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Basic Template 9','template' => '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#ddd;min-width:620px;table-layout:fixed;" width="100%">
					<tbody>
						<tr>
							<td align="center" style="padding-right:10px;padding-top:10px;padding-bottom:10px;padding-left:10px;">
							<div style="width:auto;margin-right:auto;margin-left:auto;margin-top:0;margin-bottom:0;color:#000;font-family:Arial;font-size:12px;line-height:150%;">
							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;text-align:center;font-size:12px;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;font-family:Arial,sans-serif;">Email not displaying correctly? <a href="[weblink]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">View it</span></a> in your browser</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>

							<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
								<tbody>
									<tr>
										<td align="center">
										<table cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;width:600px;background:#FFFFFF;" width="600">
											<tbody>
												<tr>
													<td>
													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:0px;padding-right:0px;padding-bottom:10px;padding-left:0px;" width="100%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:15px;padding-right:15px;padding-bottom:15px;padding-left:15px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h1 style="width:100%;font-size:42px;line-height:120%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:center;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Email Title</span></h1>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:0px;padding-right:30px;padding-bottom:10px;padding-left:30px;" width="320">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td align="center" style="vertical-align: top;">
																						<div>image here</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#FFFFFF;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:0px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h2 style="width:100%;font-size:30px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h2>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:16px;line-height:160%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Asparagus ginger american groundnut, radish garbanzo lettuce endive new zealand spinach radicchio chinese cabbage ricebean west indian gherkin. Mooli sea kale cardoon manioc. Celeriac, lagos bologi gobo dolichos bean indian pea cress, beet greens icama bitter gourd lotus root, runner bean fat hen chaya cassava.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<table cellpadding="0" cellspacing="0" class="designer-subrow" style="width:100%;border-collapse:collapse;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td class="sub-width-cell" style="vertical-align:top;border-color:#fff;border-style:solid;border-width:0 10px 0 0;" width="150px">
																			<div>
																			<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																				<tbody>
																					<tr>
																						<td style="background:#FFFFFF;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:0px;">
																						<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																							<tbody>
																								<tr>
																									<td align="center" style="vertical-align: top;">
																									<div>image here</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</div>

																			<div>
																			<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																				<tbody>
																					<tr>
																						<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																						<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																							<tbody>
																								<tr>
																									<td style="vertical-align:top;">
																									<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																									<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																									<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress.</p>
																									</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</div>
																			</td>
																			<td class="sub-width-cell" style="vertical-align:top;" width="">&nbsp;</td>
																			<td class="sub-width-cell" style="vertical-align:top;border-color:#fff;border-style:solid;border-width:0 0 0 10px;" width="150px">
																			<div>
																			<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																				<tbody>
																					<tr>
																						<td style="background:#FFFFFF;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:0px;">
																						<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																							<tbody>
																								<tr>
																									<td align="center" style="vertical-align: top;">
																									<div>image here</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</div>

																			<div>
																			<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																				<tbody>
																					<tr>
																						<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																						<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																							<tbody>
																								<tr>
																									<td style="vertical-align:top;">
																									<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																									<h3 style="width:100%;font-size:18px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																									<p style="color:#808080;font-family:Arial,sans-serif;font-size:14px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress.</p>
																									</div>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</div>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</td>
																<td style="vertical-align:top;background:#EEEEEE;" width="220">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#EEEEEE;padding-top:10px;padding-right:25px;padding-bottom:10px;padding-left:25px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#EEEEEE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<ul style="color:#808080;font-family:Arial,sans-serif;font-size:13px;margin-top:0;margin-right:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;text-align:left;">
																							<li style="line-height:170%;">Welsh onion elephant foot yam</li>
																							<li style="line-height:170%;">Kohlrabi earthnut pea broadleaf</li>
																							<li style="line-height:170%;">Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal.</li>
																							<li style="line-height:170%;">Horse gram land cress sea kale</li>
																							<li style="line-height:170%;">Arracacha celtuce fluted pumpkin</li>
																						</ul>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>

																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="background:#EEEEEE;padding-top:10px;padding-right:25px;padding-bottom:10px;padding-left:25px;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#EEEEEE;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<h3 style="width:100%;font-size:18px;line-height:150%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;font-family:Arial,sans-serif;"><span style="font-weight:bold;color:#808080;">Heading</span></h3>

																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:13px;line-height:180%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">Welsh onion elephant foot yam, kohlrabi earthnut pea. Eggplant swiss chard mizuna greens carrot tinda ricebean brinjal. Horse gram land cress. Arracacha celtuce fluted pumpkin.</p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>

													<table cellpadding="0" cellspacing="0" style="width:600px;border-collapse:collapse;table-layout:fixed;">
														<tbody>
															<tr>
																<td style="vertical-align:top;padding-top:35px;padding-right:10px;padding-bottom:35px;padding-left:35px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">
																						<p style="color:#808080;font-family:Arial,sans-serif;font-size:12px;line-height:170%;margin-top:0;margin-right:0;margin-bottom:0;margin-left:0;text-align:left;">To unsubscribe please click <a href="[unsubscribe]" style="text-decoration:none;"><span style="text-decoration:underline;color:#002AFF;">here</span></a></p>
																						</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
																<td style="vertical-align:top;padding-top:35px;padding-right:35px;padding-bottom:35px;padding-left:10px;" width="50%">
																<div>
																<table border="0" cellpadding="" cellspacing="0" style="width:100%;border-collapse:separate;table-layout:fixed;">
																	<tbody>
																		<tr>
																			<td style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;background:#FFFFFF;">
																			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0px;table-layout:fixed;background:#FFFFFF;" width="100%">
																				<tbody>
																					<tr>
																						<td style="vertical-align:top;">
																						<div style="word-wrap:break-word;line-height:140%;text-align:left;">Company Details</div>
																						</td>
																					</tr>
																				</tbody>
																			</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
																</div>
																</td>
															</tr>
														</tbody>
													</table>
													</td>
												</tr>
											</tbody>
										</table>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</td>
						</tr>
					</tbody>
				</table>','templateonlytext' => ''),
				array('reference' => 'Mercado','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Mercado','template' => '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<style type="text/css">
				.ReadMsgBody, .ExternalClass {width: 100%;}.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}a {color: inherit;text-decoration: none;}
				@media only screen and (max-width: 600px), only screen and (max-device-width: 600px) {*[class="preheader"], *[class="stack-left"], *[class="stack-right"], *[class="column"] {display: block !important;text-align: left !important;width: 100% !important;}*[class="preheader-text"], *[class="view-browser"] {display: block !important;text-align: left !important;width: 280px !important;}*[class="view-browser"] {padding-top: 3px !important;}*[class="logo-container"] {width: 100% !important;}*[class="logo"] {padding-left: 0 !important;}*[class="facebook"] {padding-left: 0 !important;}*[class="footer"], *[class="sender-name"], *[class="unsub"] {text-align: left !important;display: block !important;}*[class="outer"] {padding: 10px !important;}*[class="promo"] {padding: 20px !important;}*[class="header-promo"] {font-size: 55px !important;line-height: 58px !important;}.sub-header-promo {font-size: 28px !important;line-height: 32px !important;}}
				@media screen and (min-width: 601px) {*[class="container"] {width: 600px!important;}}
				@media only screen and (max-width: 415px), only screen and (max-device-width: 415px) {*[class="preheader"] {width: 100% !important;}}
				</style>
				<center class="wrapper" style="-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:100%;-moz-text-size-adjust:100%;-ms-text-size-adjust:100%;width:100%;background-color:#f7f7f7;table-layout:fixed;" >
					<div class="webkit" style="max-width:600px;" > 
						<table class="outer" align="center" cellpadding="0" cellspacing="0" border="0" style="width:100%;min-width:300px;max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;" >
							<tr>
								<td>
									<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" border="0" style="-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:100%;-moz-text-size-adjust:100%;-ms-text-size-adjust:100%;width:100%;background-color:#f7f7f7;table-layout:fixed;" >
										<tr>
											<td align="center">
												<table width="100%" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td>
															<table class="container" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;" >
																<tr>
																	<td style="text-align:left;vertical-align:top;font-size:0;" >
																		<div style="width:300px;display:inline-block;vertical-align:top;" >
																			<table width="100%">
																				<tr>
																					<td class="preheader-text" align="left" valign="top" style="font-family:Verdana, Helvetica, Arial, sans-serif;font-size:10px;mso-line-height-rule:exactly;line-height:13px;color:#595459;padding-top:20px;padding-bottom:0;padding-right:5px;padding-left:0;" ><!--PREHEADER TXT--> 
																						This is the preheader text This is the preheader text 
																					</td>
																				</tr>
																			</table>
																		</div>
																		<div style="width:300px;display:inline-block;vertical-align:top;" >
																			<table width="100%">
																				<tr>
																					<td class="view-browser" align="right" valign="top" style="font-family:Verdana, Helvetica, Arial, sans-serif;font-size:10px;mso-line-height-rule:exactly;line-height:13px;color:#595459;padding-top:20px;padding-bottom:0;padding-right:5px;padding-left:0;" >Email not displaying correctly? <a href="##" target="_blank" style="text-decoration:underline;color:#0070CD;" >View it</a> in your browser.</td>
																				</tr>
																			</table>
																		</div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td>
															<table class="container" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;" >
																<tr>
																	<td style="text-align:left;vertical-align:top;font-size:0;" >
																		<div class="logo-container" style="width:428px;display:inline-block;vertical-align:top;" >
																			<table width="100%">
																				<tr>
																					<td align="left" class="logo" style="padding-top:40px;padding-bottom:0;padding-right:0;padding-left:10px;" ><!--Add Logo--> 
																						<a href="##" target="_blank" style="text-decoration:none;font-family:`Trebuchet MS`;font-size:18px;color:#77C324;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/Logo.png" width="180" height="40" alt="Serene" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a> 
																					</td>
																				</tr>
																			</table>
																		</div>
																		<div style="width:152px;display:inline-block;vertical-align:top;" >
																			<table width="100%" cellpadding="0" cellspacing="0" border="0">
																				<tr>
																					<td align="right">
																						<table cellpadding="0" cellspacing="0" border="0">
																							<tr>
																								<td class="facebook" align="right" valign="top" style="padding-top:40px;padding-left:10px;" ><a href="##" target="_blank" style="font-family:Arial, Helvetica, sans-serif;text-decoration:none;font-size:11px;color:#4E4E4E;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/facebook.png" width="33" height="33" alt="facebook" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a></td>
																								<td class="twitter" align="right" valign="top" style="padding-top:40px;padding-left:10px;" ><a href="##" target="_blank" style="font-family:Arial, Helvetica, sans-serif;text-decoration:none;font-size:11px;color:#4E4E4E;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/twitter.png" width="33" height="33" alt="Twitter" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a></td>
																								<td class="google-plus" align="right" valign="top" style="padding-top:40px;padding-left:10px;" ><a href="##" target="_blank" style="font-family:Arial, Helvetica, sans-serif;text-decoration:none;font-size:11px;color:#4E4E4E;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/googleplus.png" width="33" height="33" alt="Google+" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a></td>
																								<td class="Pinterest" align="right" valign="top" style="padding-top:40px;padding-left:10px;" ><a href="##" target="_blank" style="color:inherit;text-decoration:none;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/pinterest.png" width="33" height="33" alt="Pinterest" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a></td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																			</table>
																		</div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
													</tr>
													<tr>
														<td class="body-wrapper" style="background-color:#ffffff;" >
															<table width="100%" cellpadding="0" cellspacing="0" border="0" >
																<tr> 
																	<td align="center" class="hero-image" style="font-family:`Trebuchet MS`;font-size:18px;background-color:#ffffff;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/header.jpg" alt="" width="600" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;width:100%;max-width:600px;min-width:300px;height:auto;" /></td>
																</tr>
																<tr>
																	<td align="center" class="promo" style="background-color:#7BAB26;padding-top:40px;padding-bottom:40px;padding-right:40px;padding-left:40px;" >
																		<table cellpadding="0" cellspacing="0" border="0" width="100%">
																			<tr>
																				<td align="center" class="header-promo" style="font-family:`Trebuchet MS`;font-size:75px;line-height:87px;mso-line-height-rule:exactly;color:#ffffff;" >50% OFF</td>
																			</tr>
																			<tr>
																				<td align="center" class="sub-header-promo" style="font-family:`Trebuchet MS`;font-size:35px;line-height:41px;mso-line-height-rule:exactly;color:#ffffff;" >Lorem ipsum dolor</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td height="40" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																</tr>
																<tr>
																	<td align="center">
																		<table width="100%" cellpadding="0" cellspacing="0"  class="container" style="max-width:600px;" >
																			<tr>
																				<td style="text-align:center;vertical-align:top;font-size:0;" >
																					<div style="width:300px;display:inline-block;vertical-align:top;" >
																						<table width="100%" cellpadding="0" cellspacing="0" border="0">
																							<tr>
																								<td align="center" style="font-size:12px;" > 
																									<img src="http://localhost/coreboscrm/storage/kcimages/images/leftimage.jpg" width="250" height="164" alt="" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /> 
																								</td>
																							</tr>
																							<tr>
																								<td class="sub-header" style="font-family:`Trebuchet MS`;font-size:22px;mso-line-height-rule:exactly;line-height:29px;text-align:left;color:#4E4E4E;padding-top:25px;padding-bottom:0;padding-right:25px;padding-left:25px;" >
																									Lorem Ipsum Dolor
																								</td>
																							</tr>
																							<tr>
																								<td class="sub-copy" style="font-family:`Trebuchet MS`;font-size:14px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;color:#9B9B9B;padding-top:15px;padding-bottom:40px;padding-right:25px;padding-left:25px;" >
																									At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis. 
																								</td>
																							</tr>
																							<tr>
																								<td align="left">
																									<table cellpadding="0" cellspacing="0" border="0" width="270" style="width:270px;" >
																										<tr>
																											<td align="left" class="price" style="font-family:`Trebuchet MS`;font-size:32px;color:#606060;padding-top:0;padding-bottom:0;padding-right:0;padding-left:25px;" >$49,99</td>
																											<td align="right">
																												<table cellpadding="0" cellspacing="0" border="0">
																													<tr>
																														<td align="center" bgcolor="#77C324" class="button" style="-webkit-border-radius:0px;-moz-border-radius:0px;border-radius:0px;" >
																															<a href="###" target="_blank" style="font-family:`Trebuchet MS`;font-size:15px;color:#ffffff;font-weight:bold;text-decoration:none;-webkit-border-radius:0px;-moz-border-radius:0px;border-radius:0px;border-width:1px;border-style:solid;border-color:#77C324;display:inline-block;padding-top:12px;padding-bottom:12px;padding-right:30px;padding-left:30px;" > 
																																BUY NOW
																															</a>
																														</td>
																													</tr>
																												</table>
																											</td>
																										</tr>
																									</table>
																								</td>
																							</tr>
																							<tr>
																								<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																							</tr>
																						</table>
																					</div>
																					<div style="width:300px;display:inline-block;vertical-align:top;" >
																						<table width="100%" cellpadding="0" cellspacing="0" border="0">
																							<tr>
																								<td align="center" style="font-size:12px;" > 
																									<img src="http://localhost/coreboscrm/storage/kcimages/images/rightimage.jpg" width="250" height="164" alt="" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /> 
																								</td>
																							</tr>
																							<tr>
																								<td class="sub-header" style="font-family:`Trebuchet MS`;font-size:22px;mso-line-height-rule:exactly;line-height:29px;text-align:left;color:#4E4E4E;padding-top:25px;padding-bottom:0;padding-right:25px;padding-left:25px;" ><!--Sub Header Left--> 
																									Lorem Ipsum Dolor 
																								</td>
																							</tr>
																							<tr>
																								<td class="sub-copy" style="font-family:`Trebuchet MS`;font-size:14px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;color:#9B9B9B;padding-top:15px;padding-bottom:40px;padding-right:25px;padding-left:25px;" ><!--Sub Copy Left--> 
																									At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis. 
																								</td>
																							</tr>
																							<tr>
																								<td align="left">
																									<table cellpadding="0" cellspacing="0" border="0" width="270" style="width:270px;" >
																										<tr>
																											<td align="left" class="price" style="font-family:`Trebuchet MS`;font-size:32px;color:#606060;padding-top:0;padding-bottom:0;padding-right:0;padding-left:25px;" >$49,99</td>
																											<td align="right">
																												<table cellpadding="0" cellspacing="0" border="0">
																													<tr>
																														<td align="center" bgcolor="#77C324" class="button" style="-webkit-border-radius:0px;-moz-border-radius:0px;border-radius:0px;" >
																															<a href="###" target="_blank" style="font-family:`Trebuchet MS`;font-size:15px;color:#ffffff;font-weight:bold;text-decoration:none;-webkit-border-radius:0px;-moz-border-radius:0px;border-radius:0px;border-width:1px;border-style:solid;border-color:#77C324;display:inline-block;padding-top:12px;padding-bottom:12px;padding-right:30px;padding-left:30px;" > 
																																BUY NOW
																															</a>
																														</td>
																													</tr>
																												</table>
																											</td>
																										</tr>
																									</table>
																								</td>
																							</tr>
																							<tr>
																								<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																							</tr>
																						</table>
																					</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td height="40" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																</tr>
																<tr>
																	<td align="center">
																		<table width="100%" cellpadding="0" cellspacing="0"  class="container" style="max-width:600px;" >
																			<tr>
																				<td style="text-align:center;vertical-align:top;font-size:0;" >
																					<div style="width:300px;display:inline-block;vertical-align:top;" >
																						<table width="100%" cellpadding="0" cellspacing="0" border="0">
																							<tr>
																								<td align="center" style="font-size:12px;" > 
																									<img src="http://localhost/coreboscrm/storage/kcimages/images/leftimage.jpg" width="250" height="164" alt="" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /> 
																								</td>
																							</tr>
																							<tr>
																								<td class="sub-header" style="font-family:`Trebuchet MS`;font-size:22px;mso-line-height-rule:exactly;line-height:29px;text-align:left;color:#4E4E4E;padding-top:25px;padding-bottom:0;padding-right:25px;padding-left:25px;" ><!--Sub Header Left--> 
																									Lorem Ipsum Dolor 
																								</td>
																							</tr>
																							<tr>
																								<td class="sub-copy" style="font-family:`Trebuchet MS`;font-size:14px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;color:#9B9B9B;padding-top:15px;padding-bottom:40px;padding-right:25px;padding-left:25px;" ><!--Sub Copy Left--> 
																									At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis. 
																								</td>
																							</tr>
																							<tr>
																								<td align="left">
																									<table cellpadding="0" cellspacing="0" border="0" width="270" style="width:270px;" >
																										<tr>
																											<td align="left" class="price" style="font-family:`Trebuchet MS`;font-size:32px;color:#606060;padding-top:0;padding-bottom:0;padding-right:0;padding-left:25px;" ><!--Price-->$49,99 <!--Price--></td>
																											<td align="right">
																												<table cellpadding="0" cellspacing="0" border="0">
																													<tr>
																														<td align="center" bgcolor="#77C324" class="button" style="-webkit-border-radius:0px;-moz-border-radius:0px;border-radius:0px;" ><a href="###" target="_blank" style="font-family:`Trebuchet MS`;font-size:15px;color:#ffffff;font-weight:bold;text-decoration:none;-webkit-border-radius:0px;-moz-border-radius:0px;border-radius:0px;border-width:1px;border-style:solid;border-color:#77C324;display:inline-block;padding-top:12px;padding-bottom:12px;padding-right:30px;padding-left:30px;" > 
																														BUY NOW
																														</a></td>
																													</tr>
																												</table>
																											</td>
																										</tr>
																									</table>
																								</td>
																							</tr>
																							<tr>
																								<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																							</tr>
																						</table>
																					</div>
																					<div style="width:300px;display:inline-block;vertical-align:top;" >
																						<table width="100%" cellpadding="0" cellspacing="0" border="0">
																							<tr>
																								<td align="center" style="font-size:12px;" >
																									<img src="http://localhost/coreboscrm/storage/kcimages/images/rightimage.jpg" width="250" height="164" alt="" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /> 
																								</td>
																							</tr>
																							<tr>
																								<td class="sub-header" style="font-family:`Trebuchet MS`;font-size:22px;mso-line-height-rule:exactly;line-height:29px;text-align:left;color:#4E4E4E;padding-top:25px;padding-bottom:0;padding-right:25px;padding-left:25px;" ><!--Sub Header Left--> 
																									Lorem Ipsum Dolor 
																								</td>
																							</tr>
																							<tr>
																								<td class="sub-copy" style="font-family:`Trebuchet MS`;font-size:14px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;color:#9B9B9B;padding-top:15px;padding-bottom:40px;padding-right:25px;padding-left:25px;" ><!--Sub Copy Left--> 
																									At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis. 
																								</td>
																							</tr>
																							<tr>
																								<td align="left">
																									<table cellpadding="0" cellspacing="0" border="0" width="270" style="width:270px;" >
																										<tr>
																											<td align="left" class="price" style="font-family:`Trebuchet MS`;font-size:32px;color:#606060;padding-top:0;padding-bottom:0;padding-right:0;padding-left:25px;" ><!--Price-->$49,99 <!--Price--></td>
																											<td align="right">
																												<table cellpadding="0" cellspacing="0" border="0">
																													<tr>
																														<td align="center" bgcolor="#77C324" class="button" style="-webkit-border-radius:0px;-moz-border-radius:0px;border-radius:0px;" >
																															<a href="###" target="_blank" style="font-family:`Trebuchet MS`;font-size:15px;color:#ffffff;font-weight:bold;text-decoration:none;-webkit-border-radius:0px;-moz-border-radius:0px;border-radius:0px;border-width:1px;border-style:solid;border-color:#77C324;display:inline-block;padding-top:12px;padding-bottom:12px;padding-right:30px;padding-left:30px;" >
																																BUY NOW 
																															</a>
																														</td>
																													</tr>
																												</table>
																											</td>
																										</tr>
																									</table>
																								</td>
																							</tr>
																							<tr>
																								<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																							</tr>
																						</table>
																					</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td height="40" class="spacer border" style="border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#86B13B;font-size:0;line-height:0;" >&nbsp;</td>
																</tr>
																<tr>
																	<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																</tr>
																<tr>
																	<td>
																		<table class="container" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;" >
																			<tr>
																				<td style="text-align:center;vertical-align:top;font-size:0;" >
																					<div class="column" style="width:33%;display:inline-block;vertical-align:top;" >
																						<table width="100%">
																							<tr>
																								<td class="column-Header" align="left" style="font-family:`Trebuchet MS`;font-size:22px;mso-line-height-rule:exactly;font-weight:bold;line-height:26px;color:#77C324;padding-top:10px;padding-bottom:10px;padding-right:0;padding-left:25px;" ><!--Column Header--> 
																									Category 
																								</td>
																							</tr>
																							<tr>
																								<td class="column-copy" align="left" style="font-family:`Trebuchet MS`;font-size:15px;mso-line-height-rule:exactly;line-height:23px;color:#4E4E4E;padding-top:0;padding-bottom:40px;padding-right:0;padding-left:25px;" ><!--Column Copy--> 
																									Running Shoes<br />
																									Sneakers<br />
																									Boots<br />
																									Loafers<br />
																									Flats 
																								</td>
																							</tr>
																						</table>
																					</div>
																					<div class="column" style="width:33%;display:inline-block;vertical-align:top;" >
																						<table width="100%">
																							<tr>
																								<td class="column-Header" align="left" style="font-family:`Trebuchet MS`;font-size:22px;mso-line-height-rule:exactly;font-weight:bold;line-height:26px;color:#77C324;padding-top:10px;padding-bottom:10px;padding-right:0;padding-left:25px;" ><!--Column Header--> 
																									Brands 
																								</td>
																							</tr>
																							<tr>
																								<td class="column-copy" align="left" style="font-family:`Trebuchet MS`;font-size:15px;mso-line-height-rule:exactly;line-height:23px;color:#4E4E4E;padding-top:0;padding-bottom:40px;padding-right:0;padding-left:25px;" ><!--Column Copy--> 
																									Adidas<br />
																									Puma<br />
																									Fila<br />
																									Reebook<br />
																									Woodland 
																								</td>
																							</tr>
																						</table>
																					</div>
																					<div class="column" style="width:33%;display:inline-block;vertical-align:top;" >
																						<table width="100%">
																							<tr>
																								<td class="column-Header" align="left" style="font-family:`Trebuchet MS`;font-size:22px;mso-line-height-rule:exactly;font-weight:bold;line-height:26px;color:#77C324;padding-top:10px;padding-bottom:10px;padding-right:0;padding-left:25px;" ><!--Column Header--> 
																									Styles 
																								</td>
																							</tr>
																							<tr>
																								<td class="column-copy" align="left" style="font-family:`Trebuchet MS`;font-size:15px;mso-line-height-rule:exactly;line-height:23px;color:#4E4E4E;padding-top:0;padding-bottom:40px;padding-right:0;padding-left:25px;" ><!--Column Copy--> 
																									Formals<br />
																									Casual Shoes<br />
																									Sport Sandals<br />
																									Slippers &amp; Flip Flops 
																								</td>
																							</tr>
																						</table>
																					</div>
																				</td>
																			</tr>
																			<tr>
																				<td bgcolor="#ffffff" height="20" class="spacer border" style="border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#86B13B;font-size:0;line-height:0;" >&nbsp;</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="footer-wrapper">
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr>
											<td>
												<table cellpadding="0" cellspacing="0" border="0" width="50%" align="left" class="stack-left">
													<tr>
														<td class="logo-bottom" align="left" valign="top" style="padding-top:40px;padding-bottom:20px;padding-right:5px;padding-left:0;" > 
															<a href="##" target="_blank" style="text-decoration:none;font-family:`Trebuchet MS`;font-size:18px;color:#77C324;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/Logobottom.png" width="180" height="40" alt="Serene" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a> 
														</td>
													</tr>
													<tr>
														<td class="disclaimer" align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif;font-size:10px;mso-line-height-rule:exactly;line-height:16px;padding-top:0;padding-bottom:0;padding-right:50px;padding-left:0;color:#6C6C6C;" >
															Lorem ipsum dolor sit amet, consectetur <br />
															adipisicing elit, sed do eiusmod tempor.
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0" border="0" width="50%" align="right" class="stack-right">
													<tr>
														<td align="right" valign="top" class="sender-name" style="font-family:Arial, Helvetica, sans-serif;font-size:10px;line-height:16px;color:#7F7F7F;padding-top:40px !important;padding-bottom:0;padding-right:0;padding-left:0;" >[Sender_Name]</td>
													</tr>
													<tr>
														<td align="right" valign="top" class="footer" style="font-family:Arial, Helvetica, sans-serif;font-size:10px;line-height:16px;color:#7F7F7F;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >[Sender_Address]</td>
													</tr>
													<tr>
														<td align="right" valign="top" class="footer" style="font-family:Arial, Helvetica, sans-serif;font-size:10px;line-height:16px;color:#7F7F7F;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >[Sender_City], [Sender_State] [Sender_Zip]</td>
													</tr>
													<tr>
														<td class="unsub" valign="top" style="font-family:Arial, Helvetica, sans-serif;font-size:10px;line-height:16px;color:#0055B8;text-align:right;padding-top:15px;" ><a href="###" target="_blank" style="color:inherit;text-decoration:none;" >Unsubscribe</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="###" target="_blank" style="color:inherit;text-decoration:none;" >Update Preferences</a></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
							</tr>
						</table>
					</div>
				</center>','templateonlytext' => ''),
				array('reference' => 'Nurture','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Nurture','template' => '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<style type="text/css">
				.ReadMsgBody, .ExternalClass {width: 100%;}.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}a {color: inherit;text-decoration: none;}
				@media only screen and (max-width: 600px), only screen and (max-device-width: 600px) {*[class="preheader"] {display: block !important;text-align: left !important;width: 100% !important;}*[class="preheader-text"], *[class="view-browser"] {display: block !important;text-align: left !important;padding-left: 20px !important;width: 100% !important;}*[class="view-browser"] {padding-top: 3px !important;}*[class="button-inner"] a {min-width: 300px !important;max-width: 600px !important;width: 100% !important;padding: 15px 10px !important;}}
				@media screen and (min-width: 601px) {*[class="container"] {width: 600px!important;}}
				</style>
				<center class="wrapper" style="-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:100%;-moz-text-size-adjust:100%;-ms-text-size-adjust:100%;width:100%;background-color:#ffffff;table-layout:fixed;" >
					<div class="webkit" style="max-width:600px;" >
						<table class="outer" align="center" cellpadding="0" cellspacing="0" border="0" style="width:100%;min-width:320px;max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;" >
							<tr>
								<td>
									<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" border="0" style="-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:100%;-moz-text-size-adjust:100%;-ms-text-size-adjust:100%;width:100%;background-color:#ffffff;table-layout:fixed;" >
										<tr>
											<td align="center">
												<table width="100%" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td>
															<table class="container" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;" >
																<tr>
																	<td style="text-align:left;vertical-align:top;font-size:0;" >
																		<div style="width:330px;display:inline-block;vertical-align:top;" >
																			<table width="100%">
																				<tr>
																					<td class="preheader-text" align="left" valign="top" style="font-family:Verdana Helvetica, Arial, sans-serif;font-size:10px;mso-line-height-rule:exactly;line-height:13px;color:#595459;padding-top:20px;padding-bottom:0;padding-right:5px;padding-left:5px;" >
																						This is the preheader text This is the preheader text
																					</td>
																				</tr>
																			</table>
																		</div>
																		<div style="width:270px;display:inline-block;vertical-align:top;" >
																			<table width="100%">
																				<tr>
																					<td class="view-browser" valign="top" style="font-family:Verdana Helvetica, Arial, sans-serif;font-size:10px;mso-line-height-rule:exactly;line-height:13px; text-align:left;color:#595459;padding-top:20px;padding-bottom:0;padding-right:0;padding-left:5px;" >Email not displaying correctly? <a href="##" target="_blank" style="text-decoration:underline;color:#0070CD;" >View it</a> in your browser.</td>
																				</tr>
																			</table>
																		</div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td align="center" class="logo" style="padding-top:40px;padding-bottom:40px;padding-right:10px;padding-left:10px;" >
															<a href="##" target="_blank" style="text-decoration:none;font-family:Arial, Helvetica, sans-serif;font-size:18px;color:#F5A623;" >
																<img src="http://localhost/coreboscrm/storage/kcimages/images/Logo.png" width="160" height="44" alt="NURTURE" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" />
															</a> 
														</td>
													</tr>
													<tr> 
														<td align="center" class="hero-image" style="font-family:Arial, Helvetica, sans-serif;font-size:18px;color:#626262;background-color:#ffffff;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/Image.jpg" alt="" width="600" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;width:100%;max-width:600px;min-width:320px;height:auto;" /></td>
													</tr>
													<tr>
														<td align="center" class="body-wrapper" style="background-color:#ffffff;" >
															<table width="85%" cellpadding="0" cellspacing="0" border="0" >
																<tr>
																	<td align="center" class="header-copy" style="font-family:Helvetica, Arial, sans-serif;font-size:23px;mso-line-height-rule:exactly;line-height:40px;color:#F5A623;padding-top:30px;" >
																		Lorem Ipsum Dolor sit Amet
																	</td>
																</tr>
																<tr>
																	<td align="center" class="body-copy" style="padding-top:15px;padding-bottom:40px;padding-right:0;padding-left:0;font-family:Helvetica, Arial, sans-serif;font-size:15px;mso-line-height-rule:exactly;font-weight:100;line-height:26px;color:#9B9B9B;" >
																		At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi. 
																	</td>
																</tr>
																<tr>
																	<td align="center">
																		<table cellpadding="0" cellspacing="0" border="0">
																			<tr>
																				<td align="center" bgcolor="#F5A623" class="button" style="-webkit-border-radius:8px;-moz-border-radius:8px;border-radius:8px;" >
																				<a href="###" target="_blank" style="font-family:Helvetica, Arial, sans-serif;font-size:15px;color:#ffffff;font-weight:bold;text-decoration:none;-webkit-border-radius:8px;-moz-border-radius:8px;border-radius:8px;border-width:1px;border-style:solid;border-color:#F5A623;display:inline-block;padding-top:12px;padding-bottom:12px;padding-right:40px;padding-left:40px;" >
																					CALL TO ACTION
																					</a>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="40" class="spacer border" style="border-bottom-width:2px;border-bottom-style:solid;border-bottom-color:#F5A623;font-size:0;line-height:0;" >&nbsp;</td>
													</tr>
													<tr>
														<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
													</tr>
													<tr>
														<td align="center">
															<table width="100%" cellpadding="0" cellspacing="0"  class="container" style="max-width:600px;" >
																<tr>
																	<td style="text-align:center;vertical-align:top;font-size:0;" >
																		<div style="width:300px;display:inline-block;vertical-align:top;" >
																			<table width="100%" cellpadding="0" cellspacing="0" border="0">
																				<tr>
																					<td align="center" style="font-size:12px;" >
																						<img src="http://localhost/coreboscrm/storage/kcimages/images/Imageleft.jpg" width="280" alt="" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" />
																					</td>
																				</tr>
																				<tr>
																					<td class="sub-header" style="font-family:Helvetica, Arial, sans-serif;font-size:18px;mso-line-height-rule:exactly;line-height:22px;color:#F5A623;padding-top:25px;padding-bottom:0;padding-right:5px;padding-left:5px;" >
																						Lorem Ipsum Dolor sit Amet
																					</td>
																				</tr>
																				<tr>
																					<td class="sub-copy" style="font-family:Helvetica, Arial, sans-serif;font-size:15px;mso-line-height-rule:exactly;font-weight:100;line-height:26px;color:#9B9B9B;padding-top:15px;padding-bottom:25px;padding-right:5px;padding-left:5px;" >
																						At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias.
																					</td>
																				</tr>
																				<tr>
																					<td align="center">
																						<table cellpadding="0" cellspacing="0" border="0">
																							<tr>
																								<td align="center" bgcolor="#F5A623" class="button-sub" style="-webkit-border-radius:8px;-moz-border-radius:8px;border-radius:8px;" >
																									<a href="###" target="_blank" style="font-family:Helvetica, Arial, sans-serif;font-size:15px;color:#ffffff;font-weight:bold;text-decoration:none;-webkit-border-radius:8px;-moz-border-radius:8px;border-radius:8px;border-width:1px;border-style:solid;border-color:#F5A623;display:inline-block;padding-top:12px;padding-bottom:12px;padding-right:18px;padding-left:18px;" >
																										CALL TO ACTION
																									</a>
																								</td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																				</tr>
																			</table>
																		</div>
																		<div style="width:300px;display:inline-block;vertical-align:top;" >
																			<table width="100%" cellpadding="0" cellspacing="0" border="0">
																				<tr>
																					<td align="center" style="font-size:12px;" >
																						<img src="http://localhost/coreboscrm/storage/kcimages/images/Imageright.jpg" width="280" alt="" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" />
																					</td>
																				</tr>
																				<tr>
																					<td class="sub-header" style="font-family:Helvetica, Arial, sans-serif;font-size:18px;mso-line-height-rule:exactly;line-height:22px;color:#F5A623;padding-top:25px;padding-bottom:0;padding-right:5px;padding-left:5px;" >Lorem Ipsum Dolor sit Amet</td>
																				</tr>
																				<tr>
																					<td class="sub-copy" style="font-family:Helvetica, Arial, sans-serif;font-size:15px;mso-line-height-rule:exactly;font-weight:100;line-height:26px;color:#9B9B9B;padding-top:15px;padding-bottom:25px;padding-right:5px;padding-left:5px;" >
																						At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias.
																					</td>
																				</tr>
																				<tr>
																					<td align="center">
																						<table cellpadding="0" cellspacing="0" border="0">
																							<tr>
																								<td align="center" bgcolor="#F5A623" class="button-sub" style="-webkit-border-radius:8px;-moz-border-radius:8px;border-radius:8px;" >
																									<a href="###" target="_blank" style="font-family:Helvetica, Arial, sans-serif;font-size:15px;color:#ffffff;font-weight:bold;text-decoration:none;-webkit-border-radius:8px;-moz-border-radius:8px;border-radius:8px;border-width:1px;border-style:solid;border-color:#F5A623;display:inline-block;padding-top:12px;padding-bottom:12px;padding-right:18px;padding-left:18px;" >
																										CALL TO ACTION
																									</a>
																								</td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																				</tr>
																			</table>
																		</div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td height="20" class="spacer border" style="border-bottom-width:2px;border-bottom-style:solid;border-bottom-color:#F5A623;font-size:0;line-height:0;" >&nbsp;</td>
										</tr>
										<tr>
											<td align="center"  class="logo-bottom" style="padding-top:30px;padding-bottom:15px;padding-right:5px;padding-left:5px;" > 
												<img src="http://localhost/coreboscrm/storage/kcimages/images/Logo_bottom.png" width="160" height="44" alt="NURTURE" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" />
											</td>
										</tr>
										<tr>
											<td align="center">
												<table cellpadding="0" cellspacing="0" border="0" width="85%" class="body-width">
													<tr>
														<td align="center" class="footer" style="font-family:Helvetica, Arial, sans-serif;font-size:10px;line-height:16px;color:#7F7F7F;padding-top:5px;" >[Sender_Name]</td>
													</tr>
													<tr>
														<td align="center" class="footer" style="font-family:Helvetica, Arial, sans-serif;font-size:10px;line-height:16px;color:#7F7F7F;padding-top:5px;" >[Sender_Address]</td>
													</tr>
													<tr>
														<td align="center" class="footer" style="font-family:Helvetica, Arial, sans-serif;font-size:10px;line-height:16px;color:#7F7F7F;padding-top:5px;" >[Sender_City], [Sender_State] [Sender_Zip]</td>
													</tr>
													<tr>
														<td align="center" class="unsub" style="font-family:Arial, Helvetica, sans-serif;font-size:10px;line-height:16px;color:#7F7F7F;padding-top:20px;padding-bottom:40px;padding-right:0;padding-left:0;" ><a href="###" target="_blank" style="text-decoration:none;color:#F5A623;" >Unsubscribe</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="###" target="_blank" style="text-decoration:none;color:#F5A623;" >Update Preferences</a></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>','templateonlytext' => ''),
				array('reference' => 'Serene','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Serene','template' => '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<style type="text/css">
				.ReadMsgBody, .ExternalClass {width: 100%;}.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}a {color: inherit;text-decoration: none;}
				@media only screen and (max-width: 600px), only screen and (max-device-width: 600px) {*[class="preheader"], *[class="stack-left"], *[class="stack-right"] {display: block !important;text-align: left !important;width: 100% !important;}*[class="preheader-text"], *[class="view-browser"] {display: block !important;text-align: left !important;padding-left: 20px !important;width: 100% !important;}*[class="view-browser"] {padding-top: 3px !important;}*[class="logo-container"] {width: 100% !important;}*[class="logo"] {padding-left: 40px !important;}*[class="facebook"] {padding-left: 40px !important;}*[class="footer"], *[class="sender-name"] {text-align: left !important;display: block;}}
				@media screen and (min-width: 601px) {*[class="container"] {width: 600px!important;}}
				</style>
				<center class="wrapper" style="-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:100%;-moz-text-size-adjust:100%;-ms-text-size-adjust:100%;width:100%;background-color:#f4f4f4;table-layout:fixed;" >
					<div class="webkit" style="max-width:600px;" >
						<table class="outer" align="center" cellpadding="0" cellspacing="0" border="0" style="width:100%;min-width:320px;max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;" >
							<tr>
								<td>
									<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" border="0" style="-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:100%;-moz-text-size-adjust:100%;-ms-text-size-adjust:100%;width:100%;background-color:#f4f4f4;table-layout:fixed;" >
										<tr>
											<td align="center">
												<table width="100%" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td>
															<table class="container" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;" >
																<tr>
																	<td style="text-align:left;vertical-align:top;font-size:0;" >
																		<div style="width:330px;display:inline-block;vertical-align:top;" >
																			<table width="100%">
																				<tr>
																					<td class="preheader-text" align="left" valign="top" style="font-family:Verdana Helvetica, Arial, sans-serif;font-size:10px;mso-line-height-rule:exactly;line-height:13px;color:#595459;padding-top:20px;padding-bottom:0;padding-right:5px;padding-left:5px;" ><!--PREHEADER TXT--> 
																						This is the preheader text This is the preheader text
																					</td>
																				</tr>
																			</table>
																		</div>
																		<div style="width:270px;display:inline-block;vertical-align:top;" >
																			<table width="100%">
																				<tr>
																					<td class="view-browser" valign="top" style="font-family:Verdana Helvetica, Arial, sans-serif;font-size:10px;mso-line-height-rule:exactly;line-height:13px; text-align:left;color:#595459;padding-top:20px;padding-bottom:0;padding-right:0;padding-left:5px;" >Email not displaying correctly? <a href="##" target="_blank" style="text-decoration:underline;color:#0070CD;" >View it</a> in your browser.</td>
																				</tr>
																			</table>
																		</div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td>
															<table class="container" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;" >
																<tr>
																	<td style="text-align:left;vertical-align:top;font-size:0;" >
																		<div class="logo-container" style="width:428px;display:inline-block;vertical-align:top;" >
																			<table width="100%">
																				<tr>
																					<td align="left" class="logo" style="padding-top:40px;padding-bottom:0;padding-right:0;padding-left:10px;" ><!--Add Logo--> 
																						<a href="##" target="_blank" style="text-decoration:none;font-family:Arial, Helvetica, sans-serif;font-size:18px;color:#7ED321;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/Logo.png" width="177" height="32" alt="Serene" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a> 
																					</td>
																				</tr>
																			</table>
																		</div>
																		<div style="width:152px;display:inline-block;vertical-align:top;" >
																			<table width="100%" cellpadding="0" cellspacing="0" border="0">
																				<tr>
																					<td align="right">
																						<table cellpadding="0" cellspacing="0" border="0">
																							<tr>
																								<td class="facebook" align="right" valign="top" style="padding-top:40px;padding-left:10px;" ><a href="##" target="_blank" style="font-family:Arial, Helvetica, sans-serif;text-decoration:none;font-size:11px;color:#2D2D2D;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/facebook.png" width="33" height="33" alt="facebook" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a></td>
																								<td class="twitter" align="right" valign="top" style="padding-top:40px;padding-left:10px;" ><a href="##" target="_blank" style="font-family:Arial, Helvetica, sans-serif;text-decoration:none;font-size:11px;color:#2D2D2D;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/twitter.png" width="33" height="33" alt="Twitter" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a></td>
																								<td class="google-plus" align="right" valign="top" style="padding-top:40px;padding-left:10px;" ><a href="##" target="_blank" style="font-family:Arial, Helvetica, sans-serif;text-decoration:none;font-size:11px;color:#2D2D2D;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/googleplus.png" width="33" height="33" alt="Google+" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a></td>
																								<td class="Pinterest" align="right" valign="top" style="padding-top:40px;padding-left:10px;" ><a href="##" target="_blank" style="color:inherit;text-decoration:none;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/pinterest.png" width="33" height="33" alt="Pinterest" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a></td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																			</table>
																		</div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
													</tr>
													<tr>
														<td class="body-wrapper" style="background-color:#ffffff;" >
															<table width="100%" cellpadding="0" cellspacing="0" border="0" >
																<tr>
																	<td align="center" class="body-container" style="padding-top:0;padding-bottom:0;padding-right:40px;padding-left:40px;" >
																		<table width="100%" cellpadding="0" cellspacing="0" border="0" >
																			<tr>
																				<td align="left" class="header-copy" style="font-family:`Palatino Linotype`, Palatino, `Century Schoolbook L`, `Times New Roman`, serif;font-size:22px;mso-line-height-rule:exactly;line-height:29px;color:#2D2D2D;padding-top:60px;" ><!--HEADER TXT--> 
																					Hello [%first_name%], 
																				</td>
																			</tr>
																			<tr>
																				<td align="left" class="body-copy" style="padding-top:20px;padding-bottom:40px;padding-right:0;padding-left:0;font-family:`Palatino Linotype`, Palatino, `Century Schoolbook L`, `Times New Roman`, serif;font-size:15px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;color:#2D2D2D;" ><!--BODY-COPY TXT--> 
																					Lorem ipsum dolor sit amet, vix facer virtute conceptam ne. Te qui consul graeco imperdiet, omnes pertinax torquatos eu quo, nam et summo admodum sensibus. Et veri ludus petentium eam. Mandamus erroribus ei has, cu his illud veniam.<br />
																					<br />
																						Lorem ipsum dolor sit amet, vix facer virtute conceptam ne. Te qui consul graeco imperdiet, omnes pertinax torquatos eu quo, nam et summo admodum sensibus. Et veri ludus petentium eam. Mandamus erroribus ei has, cu his illud veniam.<br />
																					<br />
																						Enjoy, <br />
																					<br />
																					[sender_name]
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td align="center" class="hero-image" style="font-family:Arial, Helvetica, sans-serif;font-size:18px;color:#2D2D2D;background-color:#ffffff;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/featuredimage.jpg" alt="" width="600" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;width:100%;max-width:600px;min-width:320px;height:auto;" /></td>
																</tr>
																<tr>
																	<td align="center" class="body-container" style="padding-top:0;padding-bottom:0;padding-right:40px;padding-left:40px;" >
																		<table width="100%" cellpadding="0" cellspacing="0" border="0" >
																			<tr>
																				<td align="left" class="header-copy" style="font-family:`Palatino Linotype`, Palatino, `Century Schoolbook L`, `Times New Roman`, serif;font-size:22px;mso-line-height-rule:exactly;line-height:29px;color:#2D2D2D;padding-top:60px;" >
																					Lorem ipsum dolor 
																				</td>
																			</tr>
																			<tr>
																				<td align="left" class="body-copy" style="padding-top:20px;padding-bottom:40px;padding-right:0;padding-left:0;font-family:`Palatino Linotype`, Palatino, `Century Schoolbook L`, `Times New Roman`, serif;font-size:15px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;color:#2D2D2D;" ><!--SUB-BODY-COPY TXT--> 
																				Lorem ipsum dolor sit amet, vix facer virtute conceptam ne. Te qui consul graeco imperdiet, omnes pertinax torquatos eu quo, nam et summo admodum sensibus. Et veri ludus petentium eam. Mandamus erroribus ei has, cu his illud veniam.
																				</td>
																			</tr>
																			<tr>
																				<td align="center">
																					<table cellpadding="0" cellspacing="0" border="0">
																						<tr>
																							<td align="center" bgcolor="#7ED321" class="button" style="-webkit-border-radius:7px;-moz-border-radius:7px;border-radius:7px;" >
																								<a href="###" target="_blank" style="font-family:`Palatino Linotype`, Palatino, `Century Schoolbook L`, `Times New Roman`, serif;font-size:15px;color:#ffffff;font-weight:bold;text-decoration:none;-webkit-border-radius:7px;-moz-border-radius:7px;border-radius:7px;border-width:1px;border-style:solid;border-color:#7ED321;display:inline-block;padding-top:12px;padding-bottom:12px;padding-right:40px;padding-left:40px;" >
																									CALL TO ACTION
																								</a>
																							</td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																			<tr>
																				<td height="50" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td align="center">
																		<table width="100%" cellpadding="0" cellspacing="0"  class="container" style="max-width:600px;" >
																			<tr>
																				<td style="text-align:center;vertical-align:top;font-size:0;" >
																					<div style="width:300px;display:inline-block;vertical-align:top;" >
																						<table width="100%" cellpadding="0" cellspacing="0" border="0">
																							<tr>
																								<td align="center" style="font-size:12px;" >
																									<img src="http://localhost/coreboscrm/storage/kcimages/images/imageleft.jpg" width="300" alt="" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /> 
																								</td>
																							</tr>
																							<tr>
																								<td class="sub-header" style="font-family:`Palatino Linotype`, Palatino, `Century Schoolbook L`, `Times New Roman`, serif;font-size:22px;mso-line-height-rule:exactly;line-height:29px;text-align:left;color:#2D2D2D;padding-top:25px;padding-bottom:0;padding-right:40px;padding-left:40px;" >
																									Lorem Ipsum Dolor
																								</td>
																							</tr>
																							<tr>
																								<td class="sub-copy" style="font-family:`Palatino Linotype`, Palatino, `Century Schoolbook L`, `Times New Roman`, serif;font-size:15px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;color:#2D2D2D;padding-top:15px;padding-bottom:10px;padding-right:40px;padding-left:40px;" >
																									At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias.
																								</td>
																							</tr>
																							<tr>
																								<td align="left" class="button-sub" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:40px;" >
																									<a href="###" target="_blank" style="font-family:`Palatino Linotype`, Palatino, `Century Schoolbook L`, `Times New Roman`, serif;font-size:15px;color:#7ED321;text-decoration:none;" >
																										Read More 
																									</a>
																								</td>
																							</tr>
																							<tr>
																								<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																							</tr>
																						</table>
																					</div>
																					<div style="width:300px;display:inline-block;vertical-align:top;" >
																						<table width="100%" cellpadding="0" cellspacing="0" border="0">
																							<tr>
																								<td align="center" style="font-size:12px;" >
																									<img src="http://localhost/coreboscrm/storage/kcimages/images/imageright.jpg" width="300" alt="" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" />
																								</td>
																							</tr>
																							<tr>
																								<td class="sub-header" style="font-family:`Palatino Linotype`, Palatino, `Century Schoolbook L`, `Times New Roman`, serif;font-size:22px;mso-line-height-rule:exactly;line-height:29px;text-align:left;color:#2D2D2D;padding-top:25px;padding-bottom:0;padding-right:40px;padding-left:40px;" >Lorem Ipsum Dolor</td> 
																							</tr>
																							<tr>
																								<td class="sub-copy" style="font-family:`Palatino Linotype`, Palatino, `Century Schoolbook L`, `Times New Roman`, serif;font-size:15px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;color:#2D2D2D;padding-top:15px;padding-bottom:10px;padding-right:40px;padding-left:40px;" >
																									At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias.
																								</td>
																							</tr>
																							<tr>
																								<td align="left" class="button-sub" style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:40px;" >
																									<a href="###" target="_blank" style="font-family:`Palatino Linotype`, Palatino, `Century Schoolbook L`, `Times New Roman`, serif;font-size:15px;color:#7ED321;text-decoration:none;" >
																										Read More 
																									</a>
																								</td>
																							</tr>
																							<tr>
																								<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																							</tr>
																						</table>
																					</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td height="40" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td class="footer-wrapper" style="background-color:#7ED321;" >
												<table cellpadding="0" cellspacing="0" border="0" width="100%">
													<tr>
														<td>
															<table cellpadding="0" cellspacing="0" border="0" width="50%" align="left" class="stack-left">
																<tr>
																	<td class="logo-bottom" align="left" valign="top" style="color:#000;padding-top:40px;padding-bottom:0px;padding-right:5px;padding-left:40px;" ><!--Bottom Logo--> 
																		<img src="http://localhost/coreboscrm/storage/kcimages/images/Logowhite.png" width="155" height="28" alt="NURTURE" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /> 
																	</td>
																</tr>
																<tr>
																	<td class="unsub" align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif;font-size:10px;line-height:16px;color:#ffffff;padding-top:15px;padding-bottom:0;padding-right:0;padding-left:40px;" ><a href="###" target="_blank" style="text-decoration:none;color:#ffffff;" >Unsubscribe</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="###" target="_blank" style="text-decoration:none;color:#ffffff;" >Update Preferences</a></td>
																</tr>
															</table>
															<table cellpadding="0" cellspacing="0" border="0" width="50%" align="right" class="stack-right">
																<tr>
																	<td align="right" valign="top" class="sender-name" style="font-family:Helvetica, Arial, sans-serif;font-size:10px;line-height:16px;color:#ffffff;padding-top:50px !important;padding-bottom:0;padding-right:40px;padding-left:40px;" >[Sender_Name]</td>
																</tr>
																<tr>
																	<td align="right" valign="top" class="footer" style="font-family:Helvetica, Arial, sans-serif;font-size:10px;line-height:16px;color:#ffffff;padding-top:0;padding-bottom:0;padding-right:40px;padding-left:40px;" >[Sender_Address]</td>
																</tr>
																<tr>
																	<td align="right" valign="top" class="footer" style="font-family:Helvetica, Arial, sans-serif;font-size:10px;line-height:16px;color:#ffffff;padding-top:0;padding-bottom:0;padding-right:40px;padding-left:40px;" >[Sender_City], [Sender_State] [Sender_Zip]</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="40" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>','templateonlytext' => ''),
				array('reference' => 'Underwood','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Underwood','template' => '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<style type="text/css">
				.ReadMsgBody, .ExternalClass {width: 100%;}.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}a {color: inherit;text-decoration: none;}
				@media only screen and (max-width: 600px), only screen and (max-device-width: 600px) {*[class="preheader"], *[class="stack-left"], *[class="stack-right"] {display: block !important;text-align: left !important;width: 100% !important;}*[class="preheader-text"], *[class="view-browser"] {display: block !important;text-align: left !important;padding-left: 20px !important;width: 100% !important;}*[class="view-browser"] {padding-top: 3px !important;}*[class="logo-container"] {width: 100% !important;}*[class="logo"] {padding-left: 0 !important;}*[class="facebook"] {padding-left: 0 !important;}*[class="footer"], *[class="sender-name"] {text-align: left !important;display: block;}}
				@media screen and (min-width: 601px) {*[class="container-top"] {width: 600px!important;}*[class="container"] {width: 530px!important;}}
				@media only screen and (max-width: 415px), only screen and (max-device-width: 415px) {*[class="webkit"] {padding: 5px !important;}*[class="wrapper"] {border-top: 1px solid #444444 !important;border-bottom: 1px solid #444444 !important;border-left: none !important;border-right: none !important;}*[class="preheader"] {width: 100% !important;}}
				</style>
				<center class="wrapper" style="-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:100%;-moz-text-size-adjust:100%;-ms-text-size-adjust:100%;width:100%;background-color:#ffffff;" >
					<div class="webkit" style="max-width:650px;padding-top:0;padding-bottom:0;padding-right:15px;padding-left:15px;" > 
						<table class="outer" align="center" cellpadding="0" cellspacing="0" border="0" style="width:100%;min-width:180px;max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;" >
							<tr>
								<td>
									<table width="100%" cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td>
												<table class="container-top" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;" >
													<tr>
														<td style="text-align:left;vertical-align:top;font-size:0;" >
															<div class="preheader" style="width:340px;display:inline-block;vertical-align:top;" >
																<table width="100%">
																	<tr>
																		<td class="preheader-text" align="left" valign="top" style="font-family:Verdana Helvetica, Arial, sans-serif;font-size:10px;mso-line-height-rule:exactly;line-height:13px;color:#595459;padding-top:20px;padding-bottom:0;padding-right:5px;padding-left:5px;" >
																			This is the preheader text This is the preheader text
																		</td>
																	</tr>
																</table>
															</div>
															<div style="width:260px;display:inline-block;vertical-align:top;" >
																<table width="100%">
																	<tr>
																		<td class="view-browser" align="right" valign="top" style="font-family:Verdana Helvetica, Arial, sans-serif;font-size:10px;mso-line-height-rule:exactly;line-height:13px;color:#595459;padding-top:20px;padding-bottom:0;padding-right:0;padding-left:0;" >Email not displaying correctly? <a href="##" target="_blank" style="text-decoration:underline;color:#0070CD;" >View it</a> in your browser.</td>
																	</tr>
																</table>
															</div>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td height="20" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
										</tr>
										<tr>
											<td align="center" class="wrapper-border" style="table-layout:fixed;border-width:1px;border-style:solid;border-color:#444444;" >
												<table width="100%" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td align="center" class="wrapper-container" style="padding-top:35px;padding-bottom:35px;padding-right:35px;padding-left:35px;" >
															<table cellpadding="0" cellspacing="0" border="0" width="100%">
																<tr>
																	<td>
																		<table class="container" width="100%" cellpadding="0" cellspacing="0" style="border-bottom-width:2px;border-bottom-style:solid;border-bottom-color:#4A90E2;" >
																			<tr>
																				<td style="text-align:left;vertical-align:top;font-size:0;" >
																					<div class="logo-container" style="width:360px;display:inline-block;vertical-align:top;" >
																						<table width="100%">
																							<tr>
																								<td align="left" class="logo" style="padding-top:0;padding-bottom:20px;padding-right:0;padding-left:0;" >
																									<a href="##" target="_blank" style="text-decoration:none;font-family:Arial, Helvetica, sans-serif;font-size:18px;color:#7ED321;" >
																										<img src="http://localhost/coreboscrm/storage/kcimages/images/Logo.png" width="179" height="42" alt="" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" />
																									</a>
																								</td>
																							</tr>
																						</table>
																					</div>
																					<div style="width:162px;display:inline-block;vertical-align:top;" >
																						<table width="100%">
																							<tr>
																								<td class="social" align="right" style="padding-top:0;padding-bottom:30px;padding-right:0;padding-left:0;" >
																									<table cellpadding="0" cellspacing="0" border="0">
																										<tr>
																											<td class="facebook" align="right" valign="top" style="padding-top:10px;" ><a href="##" target="_blank" style="font-family:Arial, Helvetica, sans-serif;text-decoration:none;font-size:11px;color:#444444;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/Facebook.png" width="33" height="33" alt="facebook" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a></td>
																											<td class="twitter" align="right" valign="top" style="padding-top:10px;padding-left:10px;" ><a href="##" target="_blank" style="font-family:Arial, Helvetica, sans-serif;text-decoration:none;font-size:11px;color:#444444;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/Twitter.png" width="33" height="33" alt="Twitter" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a></td>
																											<td class="google-plus" align="right" valign="top" style="padding-top:10px;padding-left:10px;" ><a href="##" target="_blank" style="font-family:Arial, Helvetica, sans-serif;text-decoration:none;font-size:11px;color:#444444;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/GooglePlus.png" width="33" height="33" alt="Google+" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a></td>
																											<td class="Pinterest" align="right" valign="top" style="padding-top:10px;padding-left:10px;" ><a href="##" target="_blank" style="color:inherit;text-decoration:none;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/Pinterest.png" width="33" height="33" alt="Pinterest" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a></td>
																										</tr>
																									</table>
																								</td>
																							</tr>
																						</table>
																					</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td align="left" class="header-copy" style="font-family:Courier, `Courier New`;font-size:33px;mso-line-height-rule:exactly;line-height:40px;color:#4A90E2;padding-top:35px;padding-bottom:35px;padding-right:0;padding-left:0;" >
																		Lorem Ipsum dolor sit amet.
																	</td>
																</tr>
																<tr>
																	<td align="center" class="hero-image" style="font-family:Arial, Helvetica, sans-serif;font-size:18px;color:#444444 background-color: #ffffff;" >
																		<img src="http://localhost/coreboscrm/storage/kcimages/images/headerimage.jpg" alt="" width="530" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;width:100%;max-width:540px;min-width:200px;height:auto;" />
																	</td>
																</tr>
																<tr>
																	<td align="left" class="body-copy" style="padding-top:30px;padding-bottom:30px;padding-right:0;padding-left:0;font-family:Courier, `Courier New`;font-size:15px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;color:#000001;" >
																		Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
																	</td>
																</tr>
																<tr>
																	<td class="sub-copy" style="font-family:Courier, `Courier New`;font-size:15px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;font-style:italic;padding-top:25px;padding-bottom:25px;padding-right:40px;padding-left:40px;border-top-width:2px;border-top-style:solid;border-top-color:#4A90E2;border-bottom-width:2px;border-bottom-style:solid;border-bottom-color:#4A90E2;color:#4A90E2;" ><!--Sub Copy Left--> 
																		&ldquo;At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti.&rdquo; 
																	</td>
																</tr>
																<tr>
																	<td align="left" class="body-copy" style="padding-top:30px;padding-bottom:30px;padding-right:0;padding-left:0;font-family:Courier, `Courier New`;font-size:15px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;color:#000001;" >
																		Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. <br />
																		<br />
																		<a href="http//:Replace" target="_blank" style="font-family:Courier, `Courier New`;font-size:15px;font-style:italic;color:#4A90E2;text-decoration:none;" >
																			Read more&gt;&gt;
																		</a> 
																	</td>
																</tr>
																<tr>
																	<td class="sub-header" style="font-family:Courier, `Courier New`;font-size:22px;mso-line-height-rule:exactly;line-height:29px;text-align:left;color:#4A90E2;padding-top:15px;padding-bottom:0;padding-right:40px;padding-left:0;" >
																		Lorem Ipsum dolor sit amet.
																	</td>
																</tr>
																<tr>
																	<td align="center" class="sub-image" style="font-family:Arial, Helvetica, sans-serif;font-size:18px;color:#444444 background-color: #ffffff;padding-top:20px;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/subheaderimage.jpg" alt="" width="530" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;width:100%;max-width:540px;min-width:200px;height:auto;" /></td>
																</tr>
																<tr>
																	<td align="left" class="body-copy" style="padding-top:30px;padding-bottom:30px;padding-right:0;padding-left:0;font-family:Courier, `Courier New`;font-size:15px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;color:#000001;" >
																		Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. <br />
																		<br />
																		<a href="http//:Replace" target="_blank" style="font-family:Courier, `Courier New`;font-size:15px;font-style:italic;color:#4A90E2;text-decoration:none;" >Read more&gt;&gt;</a> 
																	</td>
																</tr>
																<tr>
																	<td class="sub-header" style="font-family:Courier, `Courier New`;font-size:22px;mso-line-height-rule:exactly;line-height:29px;text-align:left;color:#4A90E2;padding-top:15px;padding-bottom:0;padding-right:40px;padding-left:0;" >
																		Lorem Ipsum dolor sit amet.
																	</td>
																</tr>
																<tr>
																	<td align="left" class="body-copy" style="padding-top:30px;padding-bottom:30px;padding-right:0;padding-left:0;font-family:Courier, `Courier New`;font-size:15px;mso-line-height-rule:exactly;text-align:left;font-weight:100;line-height:24px;color:#000001;" > 
																		Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. <br />
																		<br />
																		<a class="button" href="http//:Replace" target="_blank" style="font-family:Courier, `Courier New`;font-size:15px;font-style:italic;color:#4A90E2;text-decoration:none;" >Read more&gt;&gt;</a>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td class="footer-wrapper">
												<table cellpadding="0" cellspacing="0" border="0" width="100%">
													<tr>
														<td>
															<table cellpadding="0" cellspacing="0" border="0" width="50%" align="left" class="stack-left">
																<tr>
																	<td class="logo-bottom" align="left" valign="top" style="padding-top:25px;padding-bottom:10px;padding-right:0;padding-left:0;" >
																		<a href="##" target="_blank" style="text-decoration:none;font-family:Arial, Helvetica, sans-serif;font-size:18px;color:#7ED321;" >
																			<img src="http://localhost/coreboscrm/storage/kcimages/images/Logo.png" width="179" height="42" alt="Serene" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" />
																		</a>
																	</td>
																</tr>
																<tr>
																	<td class="disclaimer" align="left" valign="top" style="font-family:Courier, `Courier New`;font-size:10px;mso-line-height-rule:exactly;line-height:16px;padding-top:0;padding-bottom:0;padding-right:50px;padding-left:0;color:#6C6C6C;" >
																		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor.
																	</td>
																</tr>
															</table>
															<table cellpadding="0" cellspacing="0" border="0" width="50%" align="right" class="stack-right">
																<tr>
																	<td align="right" valign="top" class="sender-name" style="font-family:Helvetica, Arial, sans-serif;font-size:10px;line-height:16px;color:#7F7F7F;padding-top:30px !important;padding-bottom:0;padding-right:0;padding-left:0;" >[Sender_Name]</td>
																</tr>
																<tr>
																	<td align="right" valign="top" class="footer" style="font-family:Helvetica, Arial, sans-serif;font-size:10px;line-height:16px;color:#7F7F7F;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >[Sender_Address]</td>
																</tr>
																<tr>
																	<td align="right" valign="top" class="footer" style="font-family:Helvetica, Arial, sans-serif;font-size:10px;line-height:16px;color:#7F7F7F;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;" >[Sender_City], [Sender_State] [Sender_Zip]</td>
																</tr>
																<tr>
																	<td class="footer unsub" align="right" valign="top" style="font-family:Courier, `Courier New`;font-size:10px;line-height:16px;color:#0055B8;padding-top:15px;padding-bottom:0;padding-right:0;padding-left:0;" ><a href="###" target="_blank" style="text-decoration:none;color:#0055B8;" >Unsubscribe</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="###" target="_blank" style="text-decoration:none;color:#0055B8;" >Update Preferences</a></td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td class="spacer" height="30" style="font-size:0;line-height:0;" >&nbsp;</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>','templateonlytext' => ''),
				array('reference' => 'Bloco','actions_type' => 'Email','actions_status' => 'Active','actions_language' => '','subject' => 'Bloco','template' => '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<style type="text/css">
				.ReadMsgBody, .ExternalClass {width: 100%;}.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}a {color: inherit;text-decoration: none;}
				@media only screen and (max-width: 600px), only screen and (max-device-width: 600px) {*[class="preheader"], *[class="stack-left"], *[class="stack-right"] {display: block !important;text-align: left !important;width: 100% !important;}*[class="preheader-text"], *[class="view-browser"] {display: block !important;text-align: left !important;padding-left: 20px !important;width:300px!important;}
				*[class="view-browser"] {padding-top: 3px !important;}*[class="logo-container"] {width: 100% !important;}*[class="logo"] {padding-left: 40px !important;}*[class="facebook"] {padding-left: 40px !important;}*[class="footer"], *[class="sender-name"] {text-align: left !important;display: block;}}
				@media screen and (min-width: 601px) {*[class="container"] {width: 600px!important;}}
				</style>
				<center class="wrapper" style="-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:100%;-moz-text-size-adjust:100%;-ms-text-size-adjust:100%;width:100%;background-color:#f4f4f4;table-layout:fixed;" >
					<div class="webkit" style="max-width:600px;" >
						<table class="outer" align="center" cellpadding="0" cellspacing="0" border="0" style="width:100%;min-width:320px;max-width:600px;margin-top:0;margin-bottom:0;margin-right:auto;margin-left:auto;" >
							<tr>
								<td>
									<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" border="0" style="-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:100%;-moz-text-size-adjust:100%;-ms-text-size-adjust:100%;width:100%;background-color:#f4f4f4;table-layout:fixed;" >
										<tr>
											<td align="center">
												<table width="100%" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td>
															<table class="container" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;" >
																<tr>
																	<td style="text-align:left;vertical-align:top;font-size:0;" >
																		<div style="width:330px;display:inline-block;vertical-align:top;" >
																			<table width="100%">
																				<tr>
																					<td class="preheader-text" align="left" valign="top" style="font-family:Verdana Helvetica, Arial, sans-serif;font-size:10px;mso-line-height-rule:exactly;line-height:13px;color:#595459;padding-top:20px;padding-bottom:0;padding-right:5px;padding-left:5px;" >
																						This is the preheader text This is the preheader text
																					</td>
																				</tr>
																			</table>
																		</div>
																		<div style="width:270px;display:inline-block;vertical-align:top;" >
																			<table width="100%">
																				<tr>
																					<td class="view-browser" valign="top" style="font-family:Verdana Helvetica, Arial, sans-serif;font-size:10px;mso-line-height-rule:exactly;line-height:13px;color:#595459;padding-top:20px;padding-bottom:0;padding-right:5px;padding-left:5px;" >Email not displaying correctly? <a href="##" target="_blank" style="text-decoration:underline;color:#0070CD;" >View it</a> in your browser.</td>
																				</tr>
																			</table>
																		</div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td align="center" class="logo" style="padding-top:40px;padding-bottom:40px;padding-right:0;padding-left:0;" >
															<a href="##" target="_blank" style="text-decoration:none;font-family:Arial, Helvetica, sans-serif;font-size:18px;color:#7ED321;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/Logo.png" width="136" height="32" alt="NURTURE" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" /></a>
														</td>
													</tr>
													<tr>
														<td align="center" class="hero-image" style="font-family:Arial, Helvetica, sans-serif;font-size:18px;color:#626262;background-color:#ffffff;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/featuredimage.jpg" alt="" width="600" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;width:100%;max-width:600px;min-width:320px;height:auto;" /></td>
													</tr>
													<tr>
														<td align="center" class="body-wrapper" style="background-color:#ffffff;border-bottom-width:2px;border-bottom-style:solid;border-bottom-color:#32A9D6;" >
															<table width="85%" cellpadding="0" cellspacing="0" border="0" >
																<tr>
																	<td align="center" class="header-copy" style="font-family:Helvetica, Arial, sans-serif;font-size:23px;font-weight:100;mso-line-height-rule:exactly;line-height:40px;color:#2D2D2D;padding-top:30px;" >
																		Lorem ipsum dolor sit ame
																	</td>
																</tr>
																<tr>
																	<td align="center" class="body-copy" style="padding-top:15px;padding-bottom:40px;padding-right:0;padding-left:0;font-family:Helvetica, Arial, sans-serif;font-size:15px;mso-line-height-rule:exactly;font-weight:100;line-height:26px;color:#626262;" >
																		Lorem ipsum dolor sit amet, vix facer virtute conceptam ne. Te qui consul graeco imperdiet, omnes pertinax torquatos eu quo, nam et summo admodum sensibus. Et veri ludus petentium eam. Mandamus erroribus ei has, cu his illud veniam.
																	</td>
																</tr>
																<tr>
																	<td align="center">
																		<table cellpadding="0" cellspacing="0" border="0">
																			<tr>
																				<td align="center" bgcolor="#32A9D6" class="button" style="-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;" >
																					<a href="###" target="_blank" style="font-family:Helvetica, Arial, sans-serif;font-size:15px;color:#ffffff;font-weight:bold;text-decoration:none;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;border-width:1px;border-style:solid;border-color:#32A9D6;display:inline-block;padding-top:12px;padding-bottom:12px;padding-right:40px;padding-left:40px;" >
																						Call to Action
																					</a>
																				</td>
																			</tr>
																			<tr>
																				<td height="50" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="40" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
													</tr>
													<tr>
														<td align="center" class="hero-image" style="font-family:Arial, Helvetica, sans-serif;font-size:18px;color:#626262;background-color:#ffffff;" ><img src="http://localhost/coreboscrm/storage/kcimages/images/featureimage.jpg" alt="" width="600" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;width:100%;max-width:600px;min-width:320px;height:auto;" /></td>
													</tr>
													<tr>
														<td align="center" class="body-wrapper" style="background-color:#ffffff;border-bottom-width:2px;border-bottom-style:solid;border-bottom-color:#32A9D6;" >
															<table width="85%" cellpadding="0" cellspacing="0" border="0" >
																<tr>
																	<td align="center" class="header-copy" style="font-family:Helvetica, Arial, sans-serif;font-size:23px;font-weight:100;mso-line-height-rule:exactly;line-height:40px;color:#2D2D2D;padding-top:30px;" >
																		Lorem ipsum dolor sit ame
																	</td>
																</tr>
																<tr>
																	<td align="center" class="body-copy" style="padding-top:15px;padding-bottom:40px;padding-right:0;padding-left:0;font-family:Helvetica, Arial, sans-serif;font-size:15px;mso-line-height-rule:exactly;font-weight:100;line-height:26px;color:#626262;" >
																		Lorem ipsum dolor sit amet, vix facer virtute conceptam ne. Te qui consul graeco imperdiet, omnes pertinax torquatos eu quo, nam et summo admodum sensibus. Et veri ludus petentium eam. Mandamus erroribus ei has, cu his illud veniam.
																	</td>
																</tr>
																<tr>
																	<td align="center">
																		<table cellpadding="0" cellspacing="0" border="0">
																			<tr>
																				<td align="center" bgcolor="#32A9D6" class="button" style="-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;" ><a href="###" target="_blank" style="font-family:Helvetica, Arial, sans-serif;font-size:15px;color:#ffffff;font-weight:bold;text-decoration:none;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;border-width:1px;border-style:solid;border-color:#32A9D6;display:inline-block;padding-top:12px;padding-bottom:12px;padding-right:40px;padding-left:40px;" >
																				Call to Action
																				</a></td>
																			</tr>
																			<tr>
																				<td height="50" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="40" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
													</tr>
													<tr>
														<td align="center" class="body-wrapper" style="background-color:#ffffff;border-bottom-width:2px;border-bottom-style:solid;border-bottom-color:#32A9D6;" >
															<table width="85%" cellpadding="0" cellspacing="0" border="0" >
																<tr>
																	<td align="center" class="header-copy" style="font-family:Helvetica, Arial, sans-serif;font-size:23px;font-weight:100;mso-line-height-rule:exactly;line-height:40px;color:#2D2D2D;padding-top:30px;" >
																		Lorem ipsum dolor sit ame
																	</td>
																</tr>
																<tr>
																	<td align="center" class="body-copy" style="padding-top:15px;padding-bottom:40px;padding-right:0;padding-left:0;font-family:Helvetica, Arial, sans-serif;font-size:15px;mso-line-height-rule:exactly;font-weight:100;line-height:26px;color:#626262;" >
																		Lorem ipsum dolor sit amet, vix facer virtute conceptam ne. Te qui consul graeco imperdiet, omnes pertinax torquatos eu quo, nam et summo admodum sensibus. Et veri ludus petentium eam. Mandamus erroribus ei has, cu his illud veniam.
																	</td>
																</tr>
																<tr>
																	<td align="center">
																		<table cellpadding="0" cellspacing="0" border="0">
																			<tr>
																				<td align="center" bgcolor="#32A9D6" class="button" style="-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;" ><a href="###" target="_blank" style="font-family:Helvetica, Arial, sans-serif;font-size:15px;color:#ffffff;font-weight:bold;text-decoration:none;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;border-width:1px;border-style:solid;border-color:#32A9D6;display:inline-block;padding-top:12px;padding-bottom:12px;padding-right:40px;padding-left:40px;" >
																					Call to Action
																				</a></td>
																			</tr>
																			<tr>
																				<td height="50" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td align="center">
															<table width="100%" cellpadding="0" cellspacing="0"  class="container" style="max-width:600px;" >
																<tr>
																	<td style="text-align:center;vertical-align:top;font-size:0;" >
																		<div style="width:300px;display:inline-block;vertical-align:top;" >
																			<table align="center" width="90%" cellpadding="0" cellspacing="0" border="0">
																				<tr>
																					<td height="40" class="spacer border" style="font-size:0;line-height:0;" >&nbsp;</td>
																				</tr>
																			</table>
																			<table class="sub-column" align="center" width="90%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="border-bottom-width:2px;border-bottom-style:solid;border-bottom-color:#32A9D6;padding-bottom:20px;" >
																				<tr>
																					<td align="center" class="sub-header" style="font-family:Helvetica, Arial, sans-serif;font-weight:100;font-size:22px;mso-line-height-rule:exactly;line-height:26px;color:#2D2D2D;padding-top:35px;padding-bottom:0;padding-right:20px;padding-left:20px;" >
																						Lorem ipsum
																					</td>
																				</tr>
																				<tr>
																					<td class="sub-copy" style="font-family:Helvetica, Arial, sans-serif;font-size:15px;mso-line-height-rule:exactly;font-weight:100;line-height:26px;color:#626262;padding-top:30px;padding-bottom:25px;padding-right:20px;padding-left:20px;" >
																						Lorem ipsum dolor sit amet, vix facer virtute conceptam ne. Te qui consul graeco imperdiet, omnes pertinax torquatos eu quo, nam et summo admodum sensibus. Et veri ludus petentium eam. Mandamus erroribus ei has&hellip;
																					</td>
																				</tr>
																				<tr>
																					<td align="center">
																						<table cellpadding="0" cellspacing="0" border="0">
																							<tr>
																								<td align="center" class="button-sub"><a href="###" target="_blank" style="font-family:Helvetica, Arial, sans-serif;font-weight:100;font-size:15px;color:#32A9D6;text-decoration:none;" >
																								Read More
																								</a></td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																				</tr>
																			</table>
																		</div>
																		<div style="width:300px;display:inline-block;vertical-align:top;" >
																			<table align="center" width="90%" cellpadding="0" cellspacing="0" border="0">
																				<tr>
																					<td height="40" class="spacer border" style="font-size:0;line-height:0;" >&nbsp;</td>
																				</tr>
																			</table>
																			<table class="sub-column"  align="center" width="90%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="border-bottom-width:2px;border-bottom-style:solid;border-bottom-color:#32A9D6;padding-bottom:20px;" >
																				<tr>
																					<td align="center" class="sub-header" style="font-family:Helvetica, Arial, sans-serif;font-weight:100;font-size:22px;mso-line-height-rule:exactly;line-height:26px;color:#2D2D2D;padding-top:35px;padding-bottom:0;padding-right:20px;padding-left:20px;" >Lorem ipsum</td>
																				</tr>
																				<tr>
																					<td class="sub-copy" style="font-family:Helvetica, Arial, sans-serif;font-size:15px;mso-line-height-rule:exactly;font-weight:100;line-height:26px;color:#626262;padding-top:30px;padding-bottom:25px;padding-right:20px;padding-left:20px;" >
																						Lorem ipsum dolor sit amet, vix facer virtute conceptam ne. Te qui consul graeco imperdiet, omnes pertinax torquatos eu quo, nam et summo admodum sensibus. Et veri ludus petentium eam. Mandamus erroribus ei has&hellip;
																					</td>
																				</tr>
																				<tr>
																					<td align="center">
																						<table cellpadding="0" cellspacing="0" border="0">
																							<tr>
																								<td align="center"  class="button-sub"><a href="###" target="_blank" style="font-family:Helvetica, Arial, sans-serif;font-weight:100;font-size:15px;color:#32A9D6;text-decoration:none;" >
																								Read More
																								</a></td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td height="30" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																				</tr>
																			</table>
																		</div>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="40" class="spacer border" style="font-size:0;line-height:0;" >&nbsp;</td>
													</tr>
													<tr>
														<td class="footer-wrapper" style="background-color:#32A9D6;" >
															<table cellpadding="0" cellspacing="0" border="0" width="100%">
																<tr>
																	<td>
																		<table cellpadding="0" cellspacing="0" border="0" width="50%" align="left" class="stack-left">
																			<tr>
																				<td class="logo-bottom" align="left" valign="top" style="padding-top:40px;padding-bottom:0px;padding-right:5px;padding-left:40px;" >
																					<img src="http://localhost/coreboscrm/storage/kcimages/images/Logowhite.png" width="155" height="28" alt="NURTURE" border="0" style="border-width:0;outline-style:none;text-decoration:none;display:block;" />
																				</td>
																			</tr>
																			<tr>
																				<td class="unsub" align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif;font-size:10px;line-height:16px;color:#ffffff;padding-top:15px;padding-bottom:0;padding-right:0;padding-left:40px;" ><a href="###" target="_blank" style="text-decoration:none;color:#ffffff;" >Unsubscribe</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="###" target="_blank" style="text-decoration:none;color:#ffffff;" >Update Preferences</a></td>
																			</tr>
																		</table>
																		<table cellpadding="0" cellspacing="0" border="0" width="50%" align="right" class="stack-right">
																			<tr>
																				<td align="right" valign="top" class="sender-name" style="font-family:Helvetica, Arial, sans-serif;font-size:10px;line-height:16px;color:#ffffff;padding-top:50px !important;padding-bottom:0;padding-right:40px;padding-left:40px;" >[Sender_Name]</td>
																			</tr>
																			<tr>
																				<td align="right" valign="top" class="footer" style="font-family:Helvetica, Arial, sans-serif;font-size:10px;line-height:16px;color:#ffffff;padding-top:0;padding-bottom:0;padding-right:40px;padding-left:40px;" >[Sender_Address]</td>
																			</tr>
																			<tr>
																				<td align="right" valign="top" class="footer" style="font-family:Helvetica, Arial, sans-serif;font-size:10px;line-height:16px;color:#ffffff;padding-top:0;padding-bottom:0;padding-right:40px;padding-left:40px;" >[Sender_City], [Sender_State] [Sender_Zip]</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td height="40" class="spacer" style="font-size:0;line-height:0;" >&nbsp;</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>','templateonlytext' => '')
			);
			include_once 'modules/MsgTemplate/MsgTemplate.php';
			$modimpdata = new MsgTemplate();
			global $current_user,$site_URL,$adb;
			$modimpdata->column_fields['assigned_user_id'] = $current_user->id;
			foreach ($vtiger_actions as $action) {
				$rs = $adb->pquery('select 1 from vtiger_msgtemplate where reference=?', array($action['reference']));
				if ($adb->num_rows($rs)>0) {
					continue;
				}
				$modimpdata->column_fields['reference'] = $action['reference'];
				$modimpdata->column_fields['msgt_type'] = $action['actions_type'];
				$modimpdata->column_fields['msgt_status'] = $action['actions_status'];
				$modimpdata->column_fields['msgt_language'] = $action['actions_language'];
				$modimpdata->column_fields['subject'] = $action['subject'];
				$modimpdata->column_fields['template'] = str_replace('http://localhost/coreboscrm', $site_URL, $action['template']);
				$modimpdata->column_fields['templateonlytext'] = $action['templateonlytext'];
				$modimpdata->save('MsgTemplate');
				$this->sendMsg('Template '.$action['reference'].' added');
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}