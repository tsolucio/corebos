<?php

class test_Action extends CoreBOS_ActionController {

	public function init() {
		echo "init method";

		// $this->getViewer returns the smarty instance
		// $this->request returns the request instance
	}

	public function newinit() {
		echo "new init method";
	}
}