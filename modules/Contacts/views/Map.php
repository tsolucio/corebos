<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_Map_View extends Vtiger_Detail_View {

    /**
     * must be overriden
     * @param Vtiger_Request $request
     * @return boolean 
     */
    function preProcess(Vtiger_Request $request) {
        return true;
    }

    /**
     * must be overriden
     * @param Vtiger_Request $request
     * @return boolean 
     */
    function postProcess(Vtiger_Request $request) {
        return true;
    }

    /**
     * called when the request is recieved.
     * if viewtype : detail then show location
     * TODO : if viewtype : list then show the optimal route.    
     * @param Vtiger_Request $request 
     */
    function process(Vtiger_Request $request) {
        switch ($request->get('viewtype')) {
            case 'detail':$this->showLocation($request);
                break;
            default:break;
        }
    }

    /**
     * display the template.
     * @param Vtiger_Request $request 
     */
    function showLocation(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        // record and source_module values to be passed to populate the values in the template,
        // required to get the respective records address based on the module type.
        $viewer->assign('RECORD', $request->get('record'));
        $viewer->assign('SOURCE_MODULE', $request->get('source_module'));
        $viewer->view('map.tpl', $request->getModule());
    }

}

?>
