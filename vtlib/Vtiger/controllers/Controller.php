<?php

require_once 'Smarty_setup.php';

class CoreBOS_Controller {

	private $viewer;

	public function __construct() {
		$this->viewer = new vtigerCRM_Smarty();
	}

	/**
	 * Get viewer instance
	 *
	 * @return vtigerCRM_Smarty Smarty instance
	 */
	public function getViewer() {
		return $this->viewer;
	}
}