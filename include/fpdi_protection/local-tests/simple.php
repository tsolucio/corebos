<?php

use setasign\FpdiProtection\FpdiProtection;


require_once '../vendor/autoload.php';
require_once '../src/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(2);
date_default_timezone_set('UTC');
$start = microtime(true);

$files = [
    __DIR__ . '/../tests/_files/pdfs/Noisy-Tube.pdf',
    __DIR__ . '/../tests/_files/pdfs/filters/hex/hex.pdf',
];


$pdf = new FpdiProtection();

$ownerPassword = $pdf->setProtection([FpdiProtection::PERM_PRINT], 'a', null, 3);
var_dump($ownerPassword);

foreach ($files as $file) {
    $pageCount = $pdf->setSourceFile($file);
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $id = $pdf->importPage($pageNo);
        $size = $pdf->getTemplateSize($id);

        $pdf->AddPage($size['orientation'], $size);
        $pdf->useTemplate($id);

        $pdf->SetFont('arial');
        $pdf->Cell(0, 12, 'A simple text!');
    }
}

$pdf->Output('F', 'simple.pdf');

echo microtime(true) - $start;
echo "<br>";
var_dump(memory_get_usage());
unset($pdf);
var_dump(gc_collect_cycles());
echo "<br>";
var_dump(memory_get_usage());
echo "<br>";
echo filesize('simple.pdf');
?>

<iframe src="http://pdfanalyzer2.dev1.setasign.local/plugin?file=<?php echo urlencode(realpath('simple.pdf')); ?>" width="100%" height="92%"></iframe>
