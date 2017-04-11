<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_MapAjax_Action extends Vtiger_BasicAjax_Action {

    public function process(Vtiger_Request $request) {
        switch ($request->get("mode")) {
            case 'getLocation':$result = $this->getLocation($request);
                break;
        }
        echo json_encode($result);
    }

    /**
     * get address for the record, based on the module type.
     * @param Vtiger_Request $request
     * @return type 
     */
    function getLocation(Vtiger_Request $request) {
        $address = Google_Map_Helper::getLocation($request);
        return empty($address) ? "" : array("address" => join(",", $address));
    }
    
    public function validateRequest(Vtiger_Request $request) { 
        $request->validateReadAccess(); 
    } 

}

?>
