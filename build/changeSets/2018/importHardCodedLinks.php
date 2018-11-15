<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

include_once 'vtlib/Vtiger/Link.php';

class importHardCodedLinks extends cbupdaterWorker {

	private static $__cacheSchemaChanges = array();

	public static function __getUniqueId() {
		global $adb;
		return $adb->getUniqueID('vtiger_links');
	}

	private static function __initSchema() {
		if (empty(self::$__cacheSchemaChanges['vtiger_links'])) {
			if (!Vtiger_Utils::CheckTable('vtiger_links')) {
				Vtiger_Utils::CreateTable(
					'vtiger_links',
					'(linkid INT NOT NULL PRIMARY KEY,
					tabid INT, linktype VARCHAR(20), linklabel VARCHAR(30), linkurl VARCHAR(255), linkicon VARCHAR(100), sequence INT, status INT(1) NOT NULL DEFAULT 1)',
					true
				);
				Vtiger_Utils::ExecuteQuery(
					'CREATE INDEX link_tabidtype_idx on vtiger_links(tabid,linktype)'
				);
			}
			self::$__cacheSchemaChanges['vtiger_links'] = true;
		}
		global $adb;
		$lns=$adb->getColumnNames('vtiger_links');
		if (!in_array('onlyonmymodule', $lns)) {
			$adb->query('ALTER TABLE `vtiger_links` ADD `onlyonmymodule` BOOLEAN NOT NULL DEFAULT FALSE');
		}
	}

	public static function insertRecordsIntoVtigerLinksTable($tabid, $type, $label, $url, $iconpath = '', $sequence = 0, $handlerInfo = null, $onlyonmymodule = false) {
		global $adb;
		self::__initSchema();
		$checkres = $adb->pquery(
			'SELECT linkid FROM vtiger_links WHERE tabid=? AND linktype=? AND linkurl=? AND linkicon=? AND linklabel=?',
			array($tabid, $type, $url, $iconpath, $label)
		);
		if (!$adb->num_rows($checkres)) {
			$uniqueid = self::__getUniqueId();
			$sql = 'INSERT INTO vtiger_links (linkid,tabid,linktype,linklabel,linkurl,linkicon,sequence';
			$params = array($uniqueid, $tabid, $type, $label, $url, $iconpath, (int)$sequence);
			if (!empty($handlerInfo)) {
				$sql .= (', handler_path, handler_class, handler');
				$params[] = (isset($handlerInfo['path']) ? $handlerInfo['path'] : '');
				$params[] = (isset($handlerInfo['class']) ? $handlerInfo['class'] : '');
				$params[] = (isset($handlerInfo['method']) ? $handlerInfo['method'] : '');
			}
			$params[] = $onlyonmymodule;
			$sql .= (', onlyonmymodule) VALUES ('.generateQuestionMarks($params).')');
			$adb->pquery($sql, $params);
		}
	}

	public function applyChange() {

		$hardCodedLinks = array(
			//DETAILVIEWBASIC hard-coded links
			array(
				"modulename" => "SalesOrder",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Create Invoice",
				"linkurl" => "javascript: document.DetailView.module.value='Invoice'; document.DetailView.action.value='EditView'; document.DetailView.return_module.value='SalesOrder'; document.DetailView.return_action.value='DetailView'; document.DetailView.return_id.value='\$RECORD\$'; document.DetailView.record.value='\$RECORD\$'; document.DetailView.convertmode.value='sotoinvoice'; document.DetailView.submit();",
				"linkicon" => "themes/images/actionGenerateInvoice.gif"
			),
			array(
				"modulename" => "Quotes",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Generate Sales Order",
				"linkurl" => "javascript: document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.convertmode.value='quotetoso'; document.DetailView.module.value='SalesOrder'; document.DetailView.action.value='EditView'; document.DetailView.return_id.value='\$RECORD\$'; document.DetailView.submit();",
				"linkicon" => "themes/images/actionGenerateSalesOrder.gif"
			),
			array(
				"modulename" => "PurchaseOrder",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Send Email With PDF",
				"linkurl" => "javascript: document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.module.value='\$MODULE\$'; document.DetailView.action.value='SendPDFMail'; document.DetailView.record.value='\$RECORD\$'; document.DetailView.return_id.value='\$RECORD\$'; sendpdf_submit();",
				"linkicon" => "themes/images/PDFMail.gif"
			),
			array(
				"modulename" => "SalesOrder",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Send Email With PDF",
				"linkurl" => "javascript: document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.module.value='\$MODULE\$'; document.DetailView.action.value='SendPDFMail'; document.DetailView.record.value='\$RECORD\$'; document.DetailView.return_id.value='\$RECORD\$'; sendpdf_submit();",
				"linkicon" => "themes/images/PDFMail.gif"
			),
			array(
				"modulename" => "Quotes",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Send Email With PDF",
				"linkurl" => "javascript: document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.module.value='\$MODULE\$'; document.DetailView.action.value='SendPDFMail'; document.DetailView.record.value='\$RECORD\$'; document.DetailView.return_id.value='\$RECORD\$'; sendpdf_submit();",
				"linkicon" => "themes/images/PDFMail.gif"
			),
			array(
				"modulename" => "Invoice",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Send Email With PDF",
				"linkurl" => "javascript: document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.module.value='\$MODULE\$'; document.DetailView.action.value='SendPDFMail'; document.DetailView.record.value='\$RECORD\$'; document.DetailView.return_id.value='\$RECORD\$'; sendpdf_submit();",
				"linkicon" => "themes/images/PDFMail.gif"
			),
			array(
				"modulename" => "SalesOrder",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Export To PDF",
				"linkurl" => "index.php?module=\$MODULE\$&action=CreateSOPDF&return_module=\$MODULE\$&return_action=DetailView&record=\$RECORD\$&return_id=\$RECORD\$",
				"linkicon" => "themes/images/actionGeneratePDF.gif"
			),
			array(
				"modulename" => "PurchaseOrder",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Export To PDF",
				"linkurl" => "index.php?module=\$MODULE\$&action=CreatePDF&return_module=\$MODULE\$&return_action=DetailView&record=\$RECORD\$&return_id=\$RECORD\$",
				"linkicon" => "themes/images/actionGeneratePDF.gif"
			),
			array(
				"modulename" => "Quotes",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Export To PDF",
				"linkurl" => "index.php?module=\$MODULE\$&action=CreatePDF&return_module=\$MODULE\$&return_action=DetailView&record=\$RECORD\$&return_id=\$RECORD\$",
				"linkicon" => "themes/images/actionGeneratePDF.gif"
			),
			array(
				"modulename" => "Invoice",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Export To PDF",
				"linkurl" => "index.php?module=\$MODULE\$&action=CreatePDF&return_module=\$MODULE\$&return_action=DetailView&record=\$RECORD\$&return_id=\$RECORD\$",
				"linkicon" => "themes/images/actionGeneratePDF.gif"
			),
			array(
				"modulename" => "Quotes",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Generate Invoice",
				"linkurl" => "javascript: document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.convertmode.value='quotetoinvoice'; document.DetailView.module.value='Invoice'; document.DetailView.action.value='EditView'; document.DetailView.return_id.value='\$RECORD\$'; document.DetailView.submit();",
				"linkicon" => "themes/images/actionGenerateInvoice.gif"
			),
			array(
				"modulename" => "Products",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Create Quote",
				"linkurl" => "javascript: document.DetailView.module.value='Quotes'; document.DetailView.action.value='EditView'; document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.return_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.product_id.value='\$RECORD\$'; document.DetailView.record.value=''; document.DetailView.submit();",
				"linkicon" => "themes/images/actionGenerateQuote.gif"
			),
			array(
				"modulename" => "Products",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Create Invoice",
				"linkurl" => "javascript: document.DetailView.module.value='Invoice'; document.DetailView.action.value='EditView'; document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.return_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.product_id.value='\$RECORD\$'; document.DetailView.record.value=''; document.DetailView.submit();",
				"linkicon" => "themes/images/actionGenerateInvoice.gif"
			),
			array(
				"modulename" => "Products",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Create Sales Order",
				"linkurl" => "javascript: document.DetailView.module.value='SalesOrder'; document.DetailView.action.value='EditView'; document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.return_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.product_id.value='\$RECORD\$'; document.DetailView.record.value=''; document.DetailView.submit();",
				"linkicon" => "themes/images/actionGenerateSalesOrder.gif"
			),
			array(
				"modulename" => "Products",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Create Purchase Order",
				"linkurl" => "javascript: document.DetailView.module.value='PurchaseOrder'; document.DetailView.action.value='EditView'; document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.return_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.product_id.value='\$RECORD\$'; document.DetailView.record.value=''; document.DetailView.submit();",
				"linkicon" => "themes/images/actionGenPurchaseOrder.gif"
			),
			array(
				"modulename" => "Services",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Create Quote",
				"linkurl" => "javascript: document.DetailView.module.value='Quotes'; document.DetailView.action.value='EditView'; document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.return_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.record.value=''; document.DetailView.submit();",
				"linkicon" => "themes/images/actionGenerateQuote.gif"
			),
			array(
				"modulename" => "Services",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Create Invoice",
				"linkurl" => "javascript: document.DetailView.module.value='Invoice'; document.DetailView.action.value='EditView'; document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.return_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.record.value=''; document.DetailView.submit();",
				"linkicon" => "themes/images/actionGenerateInvoice.gif"
			),
			array(
				"modulename" => "Services",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Create Sales Order",
				"linkurl" => "javascript: document.DetailView.module.value='SalesOrder'; document.DetailView.action.value='EditView'; document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.return_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.record.value=''; document.DetailView.submit();",
				"linkicon" => "themes/images/actionGenerateSalesOrder.gif"
			),
			array(
				"modulename" => "Services",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Create Purchase Order",
				"linkurl" => "javascript: document.DetailView.module.value='PurchaseOrder'; document.DetailView.action.value='EditView'; document.DetailView.return_module.value='\$MODULE\$'; document.DetailView.return_action.value='DetailView'; document.DetailView.return_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.record.value=''; document.DetailView.submit();",
				"linkicon" => "themes/images/actionGenPurchaseOrder.gif"
			),
			array(
				"modulename" => "Vendors",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Create Purchase Order",
				"linkurl" => "javascript: document.DetailView.module.value='PurchaseOrder'; document.DetailView.action.value='EditView'; document.DetailView.return_module.value='Vendors'; document.DetailView.return_action.value='DetailView'; document.DetailView.return_id.value='\$RECORD\$'; document.DetailView.parent_id.value='\$RECORD\$'; document.DetailView.vendor_id.value='\$RECORD\$'; document.DetailView.record.value=''; document.DetailView.submit();",
				"linkicon" => "themes/images/actionGenPurchaseOrder.gif"
			),
			array(
				"modulename" => "Leads",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "LBL_CONVERT_BUTTON_LABEL",
				"linkurl" => "javascript: callConvertLeadDiv('\$RECORD\$');",
				"linkicon" => "themes/images/Leads.gif"
			),
			array(
				"modulename" => "HelpDesk",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "LBL_CONVERT_AS_FAQ_BUTTON_LABEL",
				"linkurl" => "index.php?return_module=\$MODULE\$&return_action=DetailView&record=\$RECORD\$&return_id=\$RECORD\$&module=\$MODULE\$&action=ConvertAsFAQ",
				"linkicon" => "themes/images/convert.gif"
			),
			array(
				"modulename" => "Potentials",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Create Invoice",
				"linkurl" => "index.php?return_module=\$MODULE\$&return_action=DetailView&return_id=\$RECORD\$&convertmode=potentoinvoice&module=Invoice&action=EditView",
				"linkicon" => "themes/images/actionGenerateInvoice.gif"
			),
			array(
				"modulename" => "Contacts",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "LBL_SENDMAIL_BUTTON_LABEL",
				"linkurl" => "javascript:fnvshobj('.actionlink_lbl_sendmail_button_label .webMnu','sendmail_cont');sendmail('\$MODULE\$',\$RECORD\$);",
				"linkicon" => "themes/images/sendmail.png"
			),
			array(
				"modulename" => "Leads",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "LBL_SENDMAIL_BUTTON_LABEL",
				"linkurl" => "javascript:fnvshobj('.actionlink_lbl_sendmail_button_label .webMnu','sendmail_cont');sendmail('\$MODULE\$',\$RECORD\$);",
				"linkicon" => "themes/images/sendmail.png"
			),
			array(
				"modulename" => "Accounts",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "LBL_SENDMAIL_BUTTON_LABEL",
				"linkurl" => "javascript:fnvshobj('.actionlink_lbl_sendmail_button_label .webMnu','sendmail_cont');sendmail('\$MODULE\$',\$RECORD\$);",
				"linkicon" => "themes/images/sendmail.png"
			),
			array(
				"modulename" => "Accounts",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Add event",
				"linkurl" => "index.php?module=cbCalendar&action=EditView&return_module=\$MODULE\$&return_action=DetailView&return_id=\$RECORD\$&cbfromid=\$RECORD\$&rel_id=\$RECORD\$",
				"linkicon" => "themes/images/AddEvent.gif"
			),
			array(
				"modulename" => "Contacts",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Add Event",
				"linkurl" => "index.php?module=cbCalendar&action=EditView&return_module=\$MODULE\$&return_action=DetailView&return_id=\$RECORD\$&cbfromid=\$RECORD\$",
				"linkicon" => "themes/images/AddEvent.gif"
			),
			array(
				"modulename" => "Leads",
				"linktype" => "DETAILVIEWBASIC",
				"linklabel" => "Add Event",
				"linkurl" => "index.php?module=cbCalendar&action=EditView&return_module=\$MODULE\$&return_action=DetailView&return_id=\$RECORD\$&cbfromid=\$RECORD\$&rel_id=\$RECORD\$",
				"linkicon" => "themes/images/AddEvent.gif"
			),
			array(
				"modulename" => "Documents",
				"linktype" => "DETAILVIEWWIDGET",
				"linklabel" => "Document actions",
				"linkurl" => "module=Documents&action=DocumentsAjax&file=documentsWidget&record=\$RECORD\$",
				"linkicon" => ""
			),
			//LISTVIEWBASIC hard-coded links
			array(
				"modulename" => "Accounts",
				"linktype" => "LISTVIEWBASIC",
				"linklabel" => "LBL_SEND_MAIL_BUTTON",
				"linkurl" => "return eMail('\$MODULE\$',this);",
				"linkicon" => ""
			),
			array(
				"modulename" => "Contacts",
				"linktype" => "LISTVIEWBASIC",
				"linklabel" => "LBL_SEND_MAIL_BUTTON",
				"linkurl" => "return eMail('\$MODULE\$',this);",
				"linkicon" => ""
			),
			array(
				"modulename" => "Leads",
				"linktype" => "LISTVIEWBASIC",
				"linklabel" => "LBL_SEND_MAIL_BUTTON",
				"linkurl" => "return eMail('\$MODULE\$',this);",
				"linkicon" => ""
			),
			array(
				"modulename" => "Accounts",
				"linktype" => "LISTVIEWBASIC",
				"linklabel" => "LBL_MAILER_EXPORT",
				"linkurl" => "return mailer_export();",
				"linkicon" => ""
			),
		);

		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset ' . get_class($this) . ' already applied!');
		} else {
			foreach ($hardCodedLinks as $value) {
				$tabid = getTabid($value['modulename']);
				$type = $value['linktype'];
				$label = $value['linklabel'];
				$url = $value['linkurl'];
				$iconpath = $value['linkicon'];
				self::insertRecordsIntoVtigerLinksTable($tabid, $type, $label, $url, $iconpath, 0, null, 0);
			}

			$this->sendMsg('Changeset ' . get_class($this) . ' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}