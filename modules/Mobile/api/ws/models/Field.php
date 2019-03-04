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

	public function initData($fieldData) {
		$this->data = $fieldData;
	}

	public function uitype() {
		return $this->data['uitype'];
	}

	public function name() {
		return $this->data['name'];
	}

	public function value() {
		if ($this->data['uitype'] == '15' || $this->data['uitype'] == '33' || $this->data['uitype'] == '26') {
			$rawValue = $this->data['type']['value'];
			if (is_array($rawValue)) {
				return $rawValue['value'];
			}
			return $rawValue;
		} elseif ($this->data['uitype'] == '53') {
			$rawValue = $this->data['type']['value'];
			if (is_array($rawValue)) {
				return $rawValue['value'];
			}
		} else {
			$rawValue = $this->data['value'];
			if (is_array($rawValue)) {
				return $rawValue['value'];
			}
			return $rawValue;
		}
	}

	public function valueLabel() {
		$rawValue = $this->data['value'];
		if (is_array($rawValue)) {
			return $rawValue['label'];
		}
		return $rawValue;
	}

	public function label() {
		return $this->data['label'];
	}

	public function isReferenceType() {
		static $options = array('101', '117', '357', '51', '52', '53', '57', '66', '73', '76', '77', '78', '80');
		if (isset($this->data['uitype'])) {
			$uitype = $this->data['uitype'];
			if (in_array($uitype, $options)) {
				return true;
			}
		} elseif (isset($this->data['type'])) {
			switch ($this->data['type']['name']) {
				case 'reference':
				case 'owner':
					return true;
			}
		}
		return $this->isMultiReferenceType();
	}

	public function isMultiReferenceType() {
		static $options = array('10', '68');
		$uitype = $this->data['uitype'];
		if (in_array($uitype, $options)) {
			return true;
		}
		return false;
	}

	public static function buildModelsFromResponse($fields) {
		$instances = array();
		foreach ($fields as $fieldData) {
			$instance = new self();
			$instance->initData($fieldData);
			$instances[] = $instance;
		}
		return $instances;
	}

	public function typeofdata() {
		return $this->data['typeofdata'];
	}

	public function ismandatory() {
		return $this->data['mandatory'];
	}

	public function quickcreate() {
		return $this->data['quickcreate'];
	}

	public function displaytype() {
		return $this->data['displaytype'];
	}
}