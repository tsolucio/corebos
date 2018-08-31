<?php
$fields=  getTables();
$fieldId="selTab1";
function getTables(){
    $mycheck = $_POST['mycheck']; //recupero array delle check
    $tot_mycheck = ""; //inizializzo la variabile
    foreach ($mycheck as $value) { 
    $tot_mycheck .= "$value,"; 
}
$array_value = explode(",",$mycheck);
return $array_value;
}
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
<?php for($i=0;$i<count($fields);$i++){ ?>
        var select = document.getElementById('<?php echo $fieldId; ?>');
        select.options[select.options.length] = new Option('<?php echo $fields[$i]?>', '<?php echo $fields[$i]?>'); 
<?php }?>
}
</script>