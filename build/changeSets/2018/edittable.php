<?php

class edittable extends cbupdaterWorker{

    public function applyChange(){
        global $adb, $current_user;
		if ($this->hasError()) {
			$this->sendError();
        }
        if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
        }
        else {
            $var=$adb->query("SELECT tabid FROM vtiger_tab WHERE vtiger_tab.name='ProductComponent'");
            $tid=$adb->query_result($var,0,'tabid');  
		    $sql='Update vtiger_relatedlists set related_tabid = '.$tid.'  where vtiger_relatedlists.name="get_products" or vtiger_relatedlists.name="get_parent_products"';
            $adb->query($sql);

            include_once 'include/Webservices/Create.php';
            $var2=$adb->query("SELECT vtiger_seproductsrel.*
                                FROM vtiger_seproductsrel 
                                INNER JOIN vtiger_crmentity AS c1 ON vtiger_seproductsrel.crmid = c1.crmid 
                                INNER JOIN vtiger_crmentity AS c2 ON vtiger_seproductsrel.productid = c2.crmid
                                INNER JOIN vtiger_products ON vtiger_products.productid = vtiger_seproductsrel.crmid
                                WHERE c1.deleted = 0 AND c2.deleted = 0 AND vtiger_seproductsrel.setype = 'Products'");
            $usrwsid = vtws_getEntityId('Users').'x'.$current_user->id;
                        $default_values = array(
                        'assigned_user_id' => $usrwsid,
                        'relmode' => 'Required',
                        'relfrom' => date('Y-m-d'),
                        'relto' => '2030-01-01',
                        'quantity' => '1',
                        'instructions' => '',
                        );
                        $rec = $default_values;
            while ($row=$adb->fetch_array($var2)) {
                $rec['frompdo'] = vtws_getEntityId('Products').'x'.$row['productid'];
                $rec['topdo'] = vtws_getEntityId('Products').'x'.$row['crmid'];
            }
            vtws_create('ProductComponent', $rec, $current_user);
		    
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();

    }    
}
?>