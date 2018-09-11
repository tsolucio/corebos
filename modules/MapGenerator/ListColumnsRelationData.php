
<?php
/*
 * @Author: Edmond Kacaj
 * @Date: 2018-09-11 11:43:41
 * @Last Modified by:   edmondikacaj@gmail.com
 * @Last Modified time: 2018-09-11 11:43:41
 */

// ListColumnsRelationData

include 'XmlContent.php';
include 'All_functions.php';

$mm = $_REQUEST['mod'];

echo GetModuleMultiToOne($mm);

?>

