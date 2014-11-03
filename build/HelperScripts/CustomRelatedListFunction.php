<?php
function customRelatedListFunction($recordid, $module, $relatedTabId, $actions, $obj) {
	$ret = array(
		'header' => array(0 => 'My special related list block'),
		'entries' =>  'My special related list block <b>CONTENTS</b>',
		'navigation' => array(),
		'CUSTOM_BUTTON' => '',
	);
	return $ret;
}
?>
