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
//include 'modules/';
require_once('include/fpdi/fpdf.php');
require_once('include/fpdi/fpdi.php');
// initiate FPDI  
$pdf =& new FPDI();  
// add a page
$pdf->AddPage();  
// set the sourcefile  
$pdf->setSourceFile('storage/opt_levico08.pdf');  
// import page 1  
$tplIdx = $pdf->importPage(1);  
// use the imported page and place it at point 10,10 with a width of 200 mm   (This is the image of the included pdf)
$pdf->useTemplate($tplIdx, 10, 10, 200);  
// now write some text above the imported page
$pdf->SetTextColor(0,0,255);
 
$pdf->SetFont('Times','',10);
$pdf->SetTextColor(194,8,8);
$pdf->SetXY(175, 26);
$pdf->Write(0, "testing");
$pdf->Image("test/logo/upload_2014-05-22_at_10.19.53_am.jpeg", 150, 5, 40, 20, 'jpeg');
//$this->Cell($width, $height, $string, 0, 0, "");
$file = 'storage/test_pdf.pdf';
    
    if (file_exists($file)) unlink($file);
          
  	$pdf->Output($file);
        //$pdf->Output();

//$pdf->addContent('include/fpdm/opt_levico08.pdf');
//$pdf->Output('newpdffile.pdf', 'I');

