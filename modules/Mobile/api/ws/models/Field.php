<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/

class crmtogo_UI_FieldModel {
	private $data; 
	
	function initData($fieldData) {
		$this->data = $fieldData;
	}
	
	function uitype() {
		return $this->data['uitype'];
	}
	
	function name() {
		return $this->data['name'];
	}
	
	function value() {
		if ($this->data['uitype'] == '15' || $this->data['uitype'] == '33') {  
			$rawValue = $this->data['type']['value'];
			if (is_array($rawValue)) {           
				return $rawValue['value'];
			}
		    return $rawValue;
		}
		else if($this->data['uitype'] == '53') {
		    $rawValue = $this->data['type']['value'];
			if (is_array($rawValue)) {
				return $rawValue['value'];
			}
		}
		
		else { 
     		$rawValue = $this->data['value'];
			if (is_array($rawValue)) {
				return $rawValue['value'];
			}
			return $rawValue;
		}	
	}
	
	function valueLabel() {
		$rawValue = $this->data['value'];
		if (is_array($rawValue)) {
			return $rawValue['label'];
		}
		return $rawValue;
	}
	
	
	function label() {
		return $this->data['label'];
	}
	
	function isReferenceType() {
		static $options = array('101', '116', '117', '357',
			'50', '51', '52', '53', '57', '59', '66',
			'73', '75', '76', '77', '78', '80', '81'
		);
		if (isset($this->data['uitype'])) {
			$uitype = $this->data['uitype'];
			if (in_array($uitype, $options)) {
				return true;
			}
		} 
		else if(isset($this->data['type'])) {
			switch($this->data['type']['name']) {
				case 'reference':
				case 'owner':
					return true;
			}
		}
		return $this->isMultiReferenceType();
	}
	
	function isMultiReferenceType() {
		static $options = array('10', '68');
		$uitype = $this->data['uitype'];
		if (in_array($uitype, $options)) {
			return true;
		}
		return false;
	}
	
	static function buildModelsFromResponse($fields) {
		$instances = array();
		foreach($fields as $fieldData) {
			$instance = new self();
			$instance->initData($fieldData);
			$instances[] = $instance;
		}
		return $instances;
	}

	function typeofdata() {
		return $this->data['typeofdata'];
	}
	
	function ismandatory() {
		return $this->data['mandatory'];
	}

	function quickcreate() {
		return $this->data['quickcreate'];
	}

	function displaytype() {
		return $this->data['displaytype'];
	}
}