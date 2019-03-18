<?php
require_once 'vtlib/Vtiger/controllers/Controller.php';

class CoreBOS_ActionController extends CoreBOS_Controller {

	protected $request;

	/**
	 * Constructor method, calls another controller method
	 * if it exists
	 *
	 * @param CoreBOS_Request $request [description]
	 */
	public function __construct(Vtiger_Request $request) {
		$this->request = $request;
		$method = $request->get("method");
		if ($method != '') {
			if (method_exists($this, $method)) {
				$this->$method();
			} else {
				new Exception("Method does not Exist", 404);
			}
		} else {
			$this->main();
		}
	}
}