<html>
<head>
<title>Creazione Vista</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<?php
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
<div id="selDb">
    <select id="dbList" name="dbList" onchange="selDB()" >
        <option SELECTED VALUE="Selezionare il database:">Selezionare il database:</option>
        <?php for($j=0; $j<9; $j++) {  ?>
        <option id="<?php echo $dbList[$j]; ?>" name="<?php  echo $dbList[$j]; ?>"><?php  echo $dbList[$j]; ?></option>
        <?php  } ?>
    </select>
</div>
<div id="tabForm">
    <ul id="selTab" name="selTab[]" ></ul>
    <label id="labelNameView">Inserire il nome della vista provaaaaa</label>
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
