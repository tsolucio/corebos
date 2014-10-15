<?php
	foreach($_FILES as $key=>$val) {
		foreach ($val as $i=>$v){
			$_FILES[$i]=$val[$i];
		}	
	}
	foreach($_FILES as $key=>$details) {
		$isFile = is_uploaded_file($_FILES[$key]['tmp_name']); 
		$target = "test/".basename($_FILES[$key]['name']) ;
			if($isFile && $_FILES[$key]['error']!= 4){
				echo '<textarea name='.$_FILES[$key]['name'].' id='.$key.'>';
				move_uploaded_file($_FILES[$key]['tmp_name'],$target);
				echo '</textarea>';
			}
	}
		//echo serialize($_FILES);
?>