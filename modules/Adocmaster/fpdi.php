<?php
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

