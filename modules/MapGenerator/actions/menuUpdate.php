<html>
<head><title>Aggiorna viste</title></head>
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
?>
<div id="divUpdate">
    <ul id="elements">
        <li>
           <select id="dbListViews" name="dbListViews" onchange="selDBViews()" >
                <option SELECTED VALUE="Selezionare la tabella:">Selezionare il database:</option>
                <?php for($j=0; $j<count($dbList); $j++) {  ?>
                    <option id="<?php echo $dbList[$j]; ?>" name="<?php echo $dbList[$j]; ?>"><?php echo $dbList[$j]; ?></option>
                <?php  } ?>
           </select>  
        </li>
        <li><select id="selViews" name="selViews" size="10" ></select></li>
        <li> <button id="updateView" value="Aggiorna vista" onclick="updateView()">Aggiorna vista</button></li>   
        <li><div id="immagine" style="display:none"><img src="modules/MapGenerator/image/ajax-loader.gif" /></div></li>
        <li><div id="resultView"></div></li>
    </ul>
</div>
</html>