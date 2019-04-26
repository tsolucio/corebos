<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once 'include/Webservices/VtigerActorOperation.php';

class VtigerCompanyDetails extends VtigerActorOperation {
	public function create($elementType, $element) {
		$db = PearDatabase::getInstance();
		$result = $db->query('select organization_id from vtiger_organizationdetails limit 1');
		$rowCount = $db->num_rows($result);
		if ($rowCount > 0) {
			$id = $db->query_result($result, 0, 'organization_id');
			$meta = $this->getMeta();
			$element['id'] = vtws_getId($meta->getEntityId(), $id);
			return $this->update($element);
		} else {
			$element = $this->handleFileUpload($element);
			return parent::create($elementType, $element);
		}
	}

	public function handleFileUpload($element) {
		$fileFieldList = $this->meta->getFieldListByType('file');
		foreach ($fileFieldList as $field) {
			$fieldname = $field->getFieldName();
			if (is_array($_FILES[$fieldname])) {
				$element[$fieldname] = vtws_CreateCompanyLogoFile($fieldname);
			}
		}
		return $element;
	}

	public function update($element) {
		$element = $this->handleFileUpload($element);
		return parent::update($element);
	}
}
?>