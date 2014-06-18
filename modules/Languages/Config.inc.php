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
 *************************************************************************************************
 *  Module       : Languages
 *  Version      : 5.4.0
 *  Author       : Opencubed
 * the code is based on the work of Gaëtan KRONEISEN technique@expert-web.fr and  Pius Tschümperlin ep-t.ch
 *************************************************************************************************/

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