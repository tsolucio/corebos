<?php
function executeBusinessAction($businessactionid) {
	global $current_user;

	$businessAction = (object) vtws_retrieve($businessactionid, $current_user);
	return vtlib_process_widget($businessAction);
}