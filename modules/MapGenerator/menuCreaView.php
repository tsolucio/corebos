<html>
<head>
<title>Creazione Vista</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/
include "modules/MapGenerator/dbclass.php";
$connect = new MysqlClass();
$connect->connettiMysql();
$res = mysql_query("SHOW DATABASES");
$i=0;
while ($row = mysql_fetch_assoc($res)){
    $dbList[$i]=$row['Database'];
    $i++;
}
mostraSel($dbList);
?>
<?php function mostraSel($dbList){ ?>

<div id="tabForm">
    <ul id="selTab" name="selTab[]" ></ul>
    <label id="labelNameView">Inserire il nome della vista</label>
    <input type='text' id='nameView' name="nameView" onfocus="this.value='';"><br>
    <button id="sendTab" onclick="openMenuJoin()">Invia i dati</button>
</div>
<?php } ?>
 
        
<script>
 $(document).ready(function() {
  // se il checkbox è selezionato coloro la label per simulare la colorazione delle select
  $('#selTab').each(function() {
    if ($(this).find(':checkbox').attr('checked')) $(this).addClass('selected');
  });
  // al click sul checkbox metto/tolgo la classe 'selected'
  $('#selTab :checkbox').click(function(e) { 
    var checked = $(this).attr('checked');
    $(this).closest('label').toggleClass('selected', checked);
  });
});
</script>
</body>
</html>
