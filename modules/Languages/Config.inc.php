<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Gaëtan KRONEISEN technique@expert-web.fr
 ********************************************************************************/

	//General Directory structure information
	$modulesDirectory='modules';

	//protect Languages against manipulations, this lockfor value has priority before database lockfor value
	//   0 : allow edit and delete
	//   1 : allow edit but no delete
	//   2 : allow delete but no edit
	//   3 : no edit and no delete
	$ProtectedLanguages=array('en_us'=>'1',);
	
	//Allow to upload languages Packs
	$LanguagesPackUpload=true;
	
	//Allow to edit languages Packs
	$LanguagesPackEditor=true;
		
	//Make backups when changing filecontents
	$make_backups =false;

?>