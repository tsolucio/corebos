<?php
include "modules/MapGenerator/dbclass.php";
$db=$_POST['nameDb'];
$connect = new MysqlClass();
$connect->connetti($db);
$tableName = $_POST['table'];
$fieldId = $_POST['field'];
$selezionato = $_POST['selezionato'];
$fields = $connect->getFields($tableName,$db);

?>
<script>
svuota();
riempi();
function svuota(){
	num_option=document.getElementById('<?php echo $fieldId; ?>').options.length;
	for(a=num_option;a>=0;a--){
		document.getElementById('<?php echo $fieldId; ?>').options[a]=null;
	}
}
function riempi(){
selezionato= <?php echo $selezionato; ?>;
<?php for($i=0;$i<count($fields);$i++){ ?>
        var select = document.getElementById('<?php echo $fieldId; ?>');
        select.options[select.options.length] = new Option('<?php echo $fields[$i]?>', '<?php echo $fields[$i]?>');
       
    if (!selezionato){
            var select = document.getElementById('<?php echo 'leftValues'; ?>');
            select.options[select.options.length] = new Option('<?php echo $tableName.'.'.$fields[$i]?>', '<?php echo $fields[$i]?>');
        }
       
 
 
 
 
 
        
<?php }?>
}
function create_optgroup(){
 
selezionato= <?php echo $selezionato; ?>;
if (!selezionato){   
objSelect=document.getElementById('<?php echo 'leftValues'; ?>');
optGroup = document.createElement('optgroup');
optGroup.label = "<?php echo $tableName;?>";
<?php for($i=0;$i<count($fields);$i++){ ?>
objOption=document.createElement("option");
objOption.innerHTML = "<?php echo $fields[$i]; ?>";
objOption.value = "<?php echo $fields[$i]; ?>";
objSelect.appendChild(optGroup);
optGroup.appendChild(objOption);
<?php }?>


}
}

</script>