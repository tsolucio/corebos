<?php
 /*************************************************************************************************
 * Copyright 2014 Opencubed -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module       : Adocmaster
 *  Version      : 5.4.0
 *  Author       : Opencubed
 *************************************************************************************************/
include_once('vtlib/Vtiger/Module.php');
global $adb, $log;
ini_set('max_execution_time', '5000');
$adocmasterid = $_REQUEST['adocmasterid'];

function generate_file_csv($values, $csv_filename) {

    global $adb, $log;

    $fp = fopen($csv_filename, 'w');
    fputcsv($fp, array_keys($values[0]), ';');
    for ($i = 0; $i < sizeof($values); $i++) {

        fputcsv($fp, $values[$i], ';');
    }

    fclose($fp);
}

$headers[] = array("Product Code",
    "Description",
    "Quantity",
    "Net weight",
    "Gross Weight",
    "Volume",
    "Total Net Weight",
    "Total Gross Weight",
    "Total Box",
    "Total volume"
);

$sql = $adb->pquery("
         Select prod.codice_articolo, ce_prod.description, adocdet.adoc_quantity, 
         prod.netw, prod.grossw, prod.vol, prod.pcsforbox,prod.boxforpcs
         FROM vtiger_adocdetail adocdet
         INNER JOIN vtiger_crmentity ce ON ce.crmid=adocdet.adocdetailid
         INNER JOIN vtiger_adocmaster adoc ON adoc.adocmasterid=adocdet.adoctomaster
         INNER JOIN vtiger_products prod ON prod.productid=adocdet.adoc_product
         INNER JOIN vtiger_crmentity ce_prod ON ce_prod.crmid=prod.productid
         WHERE ce.deleted=0 AND ce_prod.deleted=0 AND adoc.adocmasterid=?", array($adocmasterid));
for ($el = 0; $el < $adb->num_rows($sql); $el++) {
    $qty = $adb->query_result($sql, $el, 'adoc_quantity');
    $netWeight = $adb->query_result($sql, $el, 'netw');
    $grossWeight = $adb->query_result($sql, $el, 'grossw');
    $pcsforbox = $adb->query_result($sql, $el, 'pcsforbox');
    $volume = $boxforpcs = $adb->query_result($sql, $el, 'boxforpcs');
    $adb->query_result($sql, $el, 'vol');
    $allvalues[$el]['Product Code'] = $adb->query_result($sql, $el, 'codice_articolo');
    $allvalues[$el]['Description'] = $adb->query_result($sql, $el, 'description');
    $allvalues[$el]['Quantity'] = $qty;
    $allvalues[$el]['Net weight'] = $netWeight;
    $allvalues[$el]['Gross Weight'] = $grossWeight;
    $allvalues[$el]['Volume'] = $volume;
    $allvalues[$el]['Total Net Weight'] = $qty * $netWeight / $pcsforbox;
    $allvalues[$el]['Total Gross Weight'] = $qty * $grossWeight / $pcsforbox;
    $allvalues[$el]['Total Box'] = $qty / $pcsforbox * $boxforpcs;
    $allvalues[$el]['Total volume'] = $qty * $volume / $pcsforbox;
}
$filename = "export.csv";
generate_file_csv($allvalues, $filename);
header("Content-type:application/csv");
header("Content-Disposition:attachment;filename='$filename'");
readfile("$filename");
?>
