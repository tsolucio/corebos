<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

require_once ('include/utils/utils.php');
require_once ('Smarty_setup.php');
require_once ('include/database/PearDatabase.php');
require_once ('include/CustomFieldUtil.php');
require_once ('data/Tracker.php');
include_once 'All_functions.php';
include_once 'Staticc.php';




$GetALLMaps= explode("#", $_POST['GetALLMaps']);
$MypType=$GetALLMaps[0];
$MapID=$GetALLMaps[1];
$QueryHistory=$GetALLMaps[2];

if (empty($GetALLMaps)) {
	echo showError("Something was wrong","Missing the Type of map");
}


if ($MypType=="Mapping") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {

			Mapping_View($QueryHistory,$MapID);

		} else {
			throw new Exception(" Missing the MapID also the Id of mapgenartor_mvqueryhistory", 1);
		}
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
         echo showError("An error has occurred","Something was wrong check the Exception in log file");
         LogFile($ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
	}

}elseif ($MypType=="MasterDetailLayout") {

	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			Master_detail($QueryHistory,$MapID);
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
	}
	
}else if ($MypType==="ListColumns") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			// echo GetModuleMultiToOneForLOadListColumns("Potentials","Entitylog");
			// echo  Get_First_Moduls_TextVal("Entitylog");
			
			List_Clomns($QueryHistory,$MapID);

		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		 echo showError("Something was wrong",$ex->getMessage());
		 LogFile($ex);
	}
	
}else if ($MypType==="Condition Query") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			ConditionQuery($QueryHistory,$MapID);			

		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
	}
	
}else if ($MypType==="Module Set Mapping") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			 Module_Set_Mapping($QueryHistory,$MapID);
		
		

		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
	}
	
}else if ($MypType==="IOMap") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			 Module_IOMap($QueryHistory,$MapID);
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
	}
	
}else if ($MypType==="FieldDependency") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			 FieldDependency($QueryHistory,$MapID);
			 

		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
	}
	
}else if ($MypType==="FieldDependencyPortal") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			 FieldDependencyPortal($QueryHistory,$MapID);
			 

		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
	}
	
}else if ($MypType==="GlobalSearchAutocomplete") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			 GlobalSearchAutocomplete($QueryHistory,$MapID);
			 

		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
	}
	
}else if ($MypType==="Condition Expression") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			 ConditionExpression($QueryHistory,$MapID);

		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
	}
	
}else if ($MypType==="CREATEVIEWPORTAL") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			 CREATEVIEWPORTAL($QueryHistory,$MapID);
		
		print_r($Alldatas);
		exit();

		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
	}
	
}else if ($MypType==="DETAILVIEWBLOCKPORTAL") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			 DETAILVIEWBLOCKPORTAL($QueryHistory,$MapID);		

		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
	}
	
}else if ($MypType==="MENUSTRUCTURE") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			 MENUSTRUCTURE($QueryHistory,$MapID);
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
	}
	
}else if ($MypType==="Record Access Control") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			RecordAccessControl($QueryHistory,$MapID);		
			
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
	}
	
}else if ($MypType==="DuplicateRecords") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			DuplicateRecords($QueryHistory,$MapID);		
			
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
		LogFile($ex);
	}
	
}else if ($MypType==="RendicontaConfig") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			RendicontaConfig($QueryHistory,$MapID);		
			
			
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		

	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		LogFile($ex);
		echo showError("Something was wrong",$ex->getMessage());
	}
	
}else if ($MypType==="Import") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			 Import($QueryHistory,$MapID);		
			
			
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		
		
	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		LogFile($ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
	}
	
}else if ($MypType==="Record Set Mapping") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			RecordSetMapping($QueryHistory,$MapID);		
			
			
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		
		
	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		LogFile($ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
	}
	
}else if ($MypType==="Extended Field Information") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			ExtendedFieldInformation($QueryHistory,$MapID);		
			
			
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		
		
	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		LogFile($ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
	}
	
}else if ($MypType==="WS") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			WSMap($QueryHistory,$MapID);	
			
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		
		
	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		LogFile($ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
	}
	
}else if ($MypType==="WS Validation") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			WSValidation($QueryHistory,$MapID);	
			
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		
		
	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		LogFile($ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
	}
	
}else if ($MypType==="RelatedPanes") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			 RelatedPanes($QueryHistory,$MapID);
			
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		
		
	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		LogFile($ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
	}
	
}else if ($MypType==="Field Set") {
	
	try
	{
		if (!empty($QueryHistory) || !empty($MapID)) {
			
			FieldSet($QueryHistory,$MapID);

			// $Allhistory=get_All_History($QueryHistory);
			// 	$Alldatas=array();

			// 	foreach ($Allhistory as $value)
			// 	{
			// 		$xml=new SimpleXMLElement($value['query']);
			// 		$MyArray=array();
			// 		foreach ($xml->module as $module)
			// 		{
			// 			$modulename=(string)$module->name;
			// 			foreach ($module->fields->field as $valuefield) {
			// 					$temparray=[
			// 						'DefaultText'=>(string)explode("#", Get_First_Moduls_TextVal($modulename))[1],
			// 						'fs-modules'=>(string)$$modulename,
			// 						'fs-modulesText'=>(string)explode("#", Get_First_Moduls_TextVal($modulename))[1],
			// 						'JsonType'=>"Fields",
			// 						'Moduli'=>"",
			// 						'fs-fields'=>explode(",",CheckAllFirstForAllModules((string)$valuefield->name))[0],
			// 						'fs-fieldsText'=>explode(",",CheckAllFirstForAllModules((string)$valuefield->name))[1],
			// 						'fs-fieldsoptionGroup'=>(string)explode("#", Get_First_Moduls_TextVal($modulename))[1],
			// 						'fs-information'=>(!empty((string)$valuefield->info)?(string)$valuefield->info:""),
			// 						'fs-informationText'=>(!empty((string)$valuefield->info)?(string)$valuefield->info:""),
			// 					];
			// 					array_push($MyArray,$temparray);	
			// 				}
			// 			}
			// 		array_push($Alldatas,$MyArray);
			// 	}
			// print_r($Alldatas);
			// exit();
			
		} else {
			throw new Exception(" Missing the MapID also the Id of History", 1);
		}		
		
		
	}catch(Exception $ex)
	{
		$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
		LogFile($ex);
		// echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		echo showError("Something was wrong",$ex->getMessage());
	}
	
}else
{
	// echo "Not Exist This Type of Map? \n Please check the type of mapping and try again.... ";

	echo showError("Something was wrong","Not Exist This Type of Map in Load Map \n Please check the type of mapping and try again.... ");
}






/**
 * All Function Needet 
 */

#Region All Function Needet to genaret Map

	function FieldSet($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		

		try{
			if (!empty($QueryHistory))
			{
				$FirstModuleSelected="<option value=''>Select module</option>".Get_First_Moduls();
				$Allhistory=get_All_History($QueryHistory);
				$Alldatas=array();

				foreach ($Allhistory as $value)
				{
					$xml=new SimpleXMLElement($value['query']);
					$MyArray=array();
					foreach ($xml->module as $module)
					{
						$modulename=(string)$module->name;
						foreach ($module->fields->field as $valuefield) {
								$temparray=[
									'DefaultText'=>(string)explode("#", Get_First_Moduls_TextVal($modulename))[1],
									'fs-modules'=>(string)$modulename,
									'fs-modulesText'=>(string)explode("#", Get_First_Moduls_TextVal($modulename))[1],
									'JsonType'=>"Fields",
									'Moduli'=>"",
									'fs-fields'=>explode(",",CheckAllFirstForAllModules((string)$valuefield->name))[0],
									'fs-fieldsText'=>explode(",",CheckAllFirstForAllModules((string)$valuefield->name))[1],
									'fs-fieldsoptionGroup'=>(string)explode("#", Get_First_Moduls_TextVal($modulename))[1],
									'fs-information'=>(!empty((string)$valuefield->info)?(string)$valuefield->info:""),
									'fs-informationText'=>(!empty((string)$valuefield->info)?(string)$valuefield->info:""),
								];
								array_push($MyArray,$temparray);	
							}
						}
					array_push($Alldatas,$MyArray);
				}


				//this is for save as 
					$MapName=get_form_MapQueryID($QueryHistory,"mapname");
					$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
				//this is for save as map
					$data="MapGenerator,saveFieldSetMap";
					$dataid="ListData,MapName";
					$savehistory="true";
					$saveasfunction="ShowLocalHistoryFieldSet";
				//  //assign tpl
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);
				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);
				$smarty->assign("HistoryMap",$HistoryMap);
				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

				$smarty->assign("PopupJS",$Alldatas);
				$output = $smarty->fetch('modules/MapGenerator/fieldset.tpl');
				echo $output;
				// print_r($Alldatas);
				exit();

			}else{
				//TODO:: this is if not find the idquery to load map by Id of map 
			}
		}catch(Exception $ex){
			echo showError("Something was wrong",$ex->getMessage());
			LogFile($ex);
		}
	}


	function RelatedPanes($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		

		try{
			if (!empty($QueryHistory))
			{
				$FirstModuleSelected="<option value=''>Select module</option>".Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
				
				$Allhistory=get_All_History($QueryHistory);
				$Alldatas=array();

				foreach ($Allhistory as $value)
				{
					$xml=new SimpleXMLElement($value['query']);
					$MyArray=array();
					foreach ($xml->panes->pane as $valuepanes)
					{
						$LabelPanes=(string)$valuepanes->label;
						$SequencePanes=(string)$valuepanes->sequence;
						if ($LabelPanes!=="More information") {
							foreach ($valuepanes->blocks->block as $valueblock) {
								$temparray=[
									'DefaultText'=>(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1],
									'FirstModule'=>(string)$xml->originmodule->originname,
									'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1],
									'JsonType'=>"JsonType",
									'Moduli'=>"",
									'MoreInformationChb'=>($LabelPanes!=="More information"?"0":"1"),
									'MoreInformationChbText'=>($LabelPanes!=="More information"?"0":"1"),
									'blockType'=>(!empty((string)$valueblock->type)?(string)$valueblock->type:""),
									'blockTypeText'=>(!empty((string)$valueblock->type)?(string)$valueblock->type:""),
									'rp-block-label'=>(!empty((string)$valueblock->label)?(string)$valueblock->label:""),
									'rp-block-labelText'=>(!empty((string)$valueblock->label)?(string)$valueblock->label:""),
									'rp-block-loadfrom'=>(!empty((string)$valueblock->loadfrom)?(string)$valueblock->loadfrom:""),
									'rp-block-loadfromText'=>(!empty((string)$valueblock->loadfrom)?(string)$valueblock->loadfrom:""),
									'rp-block-sequence'=>(string)$valueblock->sequence,
									'rp-block-sequenceText'=>(string)$valueblock->sequence,
									'rp-label'=>(!empty((string)$LabelPanes)?(string)$LabelPanes:""),
									'rp-labelText'=>(!empty((string)$LabelPanes)?(string)$LabelPanes:""),
									'rp-sequence'=>(string)$SequencePanes,
									'rp-sequenceText'=>(string)$SequencePanes,
								];
								array_push($MyArray,$temparray);	
							}
						}else
						{
							$temparray1=[
								'DefaultText'=>(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1],
								'FirstModule'=>(string)$xml->originmodule->originname,
								'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1],
								'JsonType'=>"JsonType",
								'Moduli'=>"",
								'MoreInformationChb'=>($LabelPanes!=="More information"?"0":"1"),
								'MoreInformationChbText'=>($LabelPanes!=="More information"?"0":"1"),
								'blockType'=>(""),
								'blockTypeText'=>(""),
								'rp-block-label'=>(""),
								'rp-block-labelText'=>(""),
								'rp-block-loadfrom'=>(""),
								'rp-block-loadfromText'=>(""),
								'rp-block-sequence'=>(""),
								'rp-block-sequenceText'=>(""),
								'rp-label'=>(!empty((string)$LabelPanes)?(string)$LabelPanes:""),
								'rp-labelText'=>(!empty((string)$LabelPanes)?(string)$LabelPanes:""),
								'rp-sequence'=>(string)$SequencePanes,
								'rp-sequenceText'=>(string)$SequencePanes,
							];
							array_push($MyArray,$temparray1);

							if(isset($valuepanes->blocks))
							{
								foreach ($valuepanes->blocks->block as $valueblock) {
									$temparray2=[
										'DefaultText'=>(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1],
										'FirstModule'=>(string)$xml->originmodule->originname,
										'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1],
										'JsonType'=>"JsonType",
										'Moduli'=>"",
										'MoreInformationChb'=>($LabelPanes!=="More information"?"0":"1"),
										'MoreInformationChbText'=>($LabelPanes!=="More information"?"0":"1"),
										'blockType'=>(!empty((string)$valueblock->type)?(string)$valueblock->type:""),
										'blockTypeText'=>(!empty((string)$valueblock->type)?(string)$valueblock->type:""),
										'rp-block-label'=>(!empty((string)$valueblock->label)?(string)$valueblock->label:""),
										'rp-block-labelText'=>(!empty((string)$valueblock->label)?(string)$valueblock->label:""),
										'rp-block-loadfrom'=>(!empty((string)$valueblock->loadfrom)?(string)$valueblock->loadfrom:""),
										'rp-block-loadfromText'=>(!empty((string)$valueblock->loadfrom)?(string)$valueblock->loadfrom:""),
										'rp-block-sequence'=>(string)$valueblock->sequence,
										'rp-block-sequenceText'=>(string)$valueblock->sequence,
										'rp-label'=>(!empty((string)$LabelPanes)?(string)$LabelPanes:""),
										'rp-labelText'=>(!empty((string)$LabelPanes)?(string)$LabelPanes:""),
										'rp-sequence'=>(string)$SequencePanes,
										'rp-sequenceText'=>(string)$SequencePanes,
									];
									array_push($MyArray,$temparray2);	
								}
							}
						}
					}
					array_push($Alldatas,$MyArray);
			    }


				//this is for save as 
					$MapName=get_form_MapQueryID($QueryHistory,"mapname");
					$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
				//this is for save as map
					$data="MapGenerator,saveRelatedPanes";
					$dataid="ListData,MapName";
					$savehistory="true";
					$saveasfunction="ShowLocalHistoryRelatedPanes";
				//  //assign tpl
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);
				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);
				$smarty->assign("HistoryMap",$HistoryMap);
				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

				$smarty->assign("PopupJS",$Alldatas);
				$output = $smarty->fetch('modules/MapGenerator/relatedpanes.tpl');
				echo $output;
				// print_r($Alldatas);
				exit();

			}else{
				//TODO:: this is if not find the idquery to load map by Id of map 
			}
		}catch(Exception $ex){
			echo showError("Something was wrong",$ex->getMessage());
			LogFile($ex);
		}
	}



	function WSValidation($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		

		try{
			if (!empty($QueryHistory))
			{
				$FirstModuleSelected="<option value=''>Select module</option>".Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
				$SecondModuleSelected="<option value=''>Select module</option>".Get_First_Moduls(get_The_history($QueryHistory,"secondmodule"));
				
				$Allhistory=get_All_History($QueryHistory);
				$Alldatas=array();

				foreach ($Allhistory as $value) {
					$xml=new SimpleXMLElement($value['query']);
					$MyArray=array();
					foreach ($xml->fields->field  as  $valuexml) {
							$temparray=[
								'DefaultText'=>(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1],
								'FirstModule'=>(string)$xml->originmodule->originname,
								'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1],
								'JsonType'=>"Field",
								'Moduli'=>"",
								'TargetModule'=>(isset($xml->targetmodule->targetname)?$xml->targetmodule->targetname:""),
								'TargetModuleText'=>(isset($xml->targetmodule->targetname)?
													(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1]:""),
								'ws-val-name'=>(string)$valuexml->fieldname,
								'ws-val-nameText'=>(string)$valuexml->fieldname,
								'ws-val-origin-select'=>(string)$valuexml->origin,
								'ws-val-origin-selectText'=>(string)$valuexml->origin,
								'ws-val-validation'=>!empty((string)$valuexml->validationtype)?(string)$valuexml->validationtype:"",
								'ws-val-validationText'=>!empty((string)$valuexml->validationtype)?(string)$valuexml->validationtype:"",
								'ws-val-value'=>!empty((string)$valuexml->fieldvalue)?(string)$valuexml->fieldvalue:"",
								'ws-val-valueText'=>!empty((string)$valuexml->fieldvalue)?(string)$valuexml->fieldvalue:"",
							];
							array_push($MyArray,$temparray);
						}
					array_push($Alldatas,$MyArray);
				}


				//this is for save as 
					$MapName=get_form_MapQueryID($QueryHistory,"mapname");
					$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
				//this is for save as map
					$data="MapGenerator,saveWebServiceValidation";
					$dataid="ListData,MapName";
					$savehistory="true";
					$saveasfunction="ShowLocalHistoryWSValidation";
				//  //assign tpl
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);
				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);
				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("SecondModule",$SecondModuleSelected);
				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

				$smarty->assign("PopupJS",$Alldatas);
				$output = $smarty->fetch('modules/MapGenerator/WSValidation.tpl');
				echo $output;
				// print_r($Alldatas);
				exit();

			}else{
				//TODO:: this is if not find the idquery to load map by Id of map 
			}
		}catch(Exception $ex){
			echo showError("Something was wrong",$ex->getMessage());
			LogFile($ex);
		}
	}



 
	/**
	 * Function to get load the map from XML to design 
	 *
	 * @param [type] $QueryHistory
	 * @param [type] $MapID
	 * @return void
	 */
	function WSMap($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		include_once('All_functions.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		try {
			
			if (!empty($QueryHistory)) {
				//TODO: if have query history
				
				$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
				$showfields='<option value="" >(Select a module)</option>';
				$module=get_The_history($QueryHistory,"firstmodule");
				if (!empty(MappingRelationFields($module))) {
					foreach (MappingRelationFields($module) as $value) {
						if ($value!==$module) {
							$showfields.='<option value="'.$value.'">'.$value.'</option>'; 
						}        
					}
					
				} else {
				echo "<option value=''>None</option>";
				}
				$SecondModulerelation=$showfields;

				$FirstModuleFields=getModFields(get_The_history($QueryHistory,"firstmodule"));
				

				$MapName=get_form_Map($MapID,"mapname");

				$HistoryMap=$QueryHistory.",".$MapID;
				//all history 
				$Allhistory=get_All_History($QueryHistory);

				$Alldatas=array();

				foreach ($Allhistory as $value) {
					
						$MyArray=array();
						$xml=new SimpleXMLElement($value['query']); 
						$nrindex=0;
						$araymy=[
							'DefaultText'=>(string)explode("/",$xml->wsconfig->wsurl)[2],
							'FirstModule' =>(string)explode("#", Get_First_Moduls_TextVal($value['FirstModule']))[0],
							'FirstModuleText' =>(string)explode("#", Get_First_Moduls_TextVal($value['FirstModule']))[1],
							'FirstModuleoptionGroup'=>"udentifined",
							'JsonType' =>"Configuration",
							'fixed-text-addon-pre' =>(string)explode("/",$xml->wsconfig->wsurl)[0]."//",
							'fixed-text-addon-preText'=>(string)explode("/",$xml->wsconfig->wsurl)[0]."//",
							'url-input'=>(string)explode("/",$xml->wsconfig->wsurl)[2],
							'url-inputText'=>(string)explode("/",$xml->wsconfig->wsurl)[2],
							'urlMethod'=>(string)$xml->wsconfig->wshttpmethod,
							'urlMethodText'=>(string)$xml->wsconfig->wshttpmethod,
							'ws-input-type'=>(string)$xml->wsconfig->inputtype,
							'ws-input-typeText'=>(string)$xml->wsconfig->inputtype,
							'ws-output-type'=>(string)$xml->wsconfig->outputtype,
							'ws-output-typeText'=>(string)$xml->wsconfig->outputtype,
							'ws-password'=>(!empty((string)$xml->wsconfig->wspass)?(string)$xml->wsconfig->wspass:"Empty"),
							'ws-passwordText'=>(!empty((string)$xml->wsconfig->wspass)?(string)$xml->wsconfig->wspass:"Empty"),
							'ws-proxy-host'=>(!empty((string)$xml->wsconfig->wsproxyhost)?(string)$xml->wsconfig->wsproxyhost:"Empty"),
							'ws-proxy-hostText'=>(!empty((string)$xml->wsconfig->wsproxyhost)?(string)$xml->wsconfig->wsproxyhost:"Empty"),
							'ws-proxy-port'=>(!empty((string)$xml->wsconfig->wsproxyport)?(string)$xml->wsconfig->wsproxyport:"Empty"),
							'ws-proxy-portText'=>(!empty((string)$xml->wsconfig->wsproxyport)?(string)$xml->wsconfig->wsproxyport:"Empty"),
							'ws-response-time'=>(!empty((string)$xml->wsconfig->wsresponsetime)?(string)$xml->wsconfig->wsresponsetime:"Empty"),
							'ws-response-timeText'=>(!empty((string)$xml->wsconfig->wsresponsetime)?(string)$xml->wsconfig->wsresponsetime:"Empty"),
							'ws-start-tag'=>(!empty((string)$xml->wsconfig->wsstarttag)?(string)$xml->wsconfig->wsstarttag:"Empty"),
							'ws-start-tagText'=>(!empty((string)$xml->wsconfig->wsstarttag)?(string)$xml->wsconfig->wsstarttag:"Empty"),
							'ws-user'=>(!empty((string)$xml->wsconfig->wsuser)?(string)$xml->wsconfig->wsuser:"Empty"),
							'ws-userText'=>(!empty((string)$xml->wsconfig->wsuser)?(string)$xml->wsconfig->wsuser:"Empty"),
							];
							array_push($MyArray,$araymy);

						if (isset($xml->wsconfig->wsheader)) {
							foreach ($xml->wsconfig->wsheader->header as  $valueheader) {
								$header=[
									'JsonType'=>"Header",
									'DefaultText'=>"",
									'Moduli'=>"",
									'ws-key-name'=>(String)$valueheader->keyname,
									'ws-key-nameText'=>(String)$valueheader->keyname,
									'ws-key-value'=>(String)$valueheader->keyvalue,
									'ws-key-valueText'=>(String)$valueheader->keyvalue,
								];
								array_push($MyArray,$header);
							}
						}
						if (isset($xml->input->fields))
						{
							foreach ($xml->input->fields->field as  $valueinput)
							{
								$Inputfields=[
									'JsonType'=>"Input",
									'DefaultText'=>(!empty((string)explode("#", Get_First_Moduls_TextVal($valueinput->Relfield->RelModule))[1])?(string)explode("#", Get_First_Moduls_TextVal($valueinput->Relfield->RelModule))[1]:(string)explode("#", Get_First_Moduls_TextVal($value['FirstModule']))[1] ),
									'FirstModule' =>(!empty((string)explode("#", Get_First_Moduls_TextVal($valueinput->Relfield->RelModule))[0])?(string)explode("#", Get_First_Moduls_TextVal($valueinput->Relfield->RelModule))[0]:(string)explode("#", Get_First_Moduls_TextVal($value['FirstModule']))[0] ),
									'ws-select-multipleoptionGroup' =>(!empty((string)explode("#", Get_First_Moduls_TextVal($valueinput->Relfield->RelModule))[0])?(string)explode("#", Get_First_Moduls_TextVal($valueinput->Relfield->RelModule))[0]:(string)explode("#", Get_First_Moduls_TextVal($value['FirstModule']))[0] ),
									'FirstModuleText' =>(!empty((string)explode("#", Get_First_Moduls_TextVal($valueinput->Relfield->RelModule))[1])?(string)explode("#", Get_First_Moduls_TextVal($valueinput->Relfield->RelModule))[1]:(string)explode("#", Get_First_Moduls_TextVal($value['FirstModule']))[1] ),
									'Moduli'=>"",
									'ws-input-Origin'=>(String)$valueinput->origin,
									'ws-input-OriginText'=>(String)$valueinput->origin,
									'ws-input-attribute'=>(!empty((String)$valueinput->attribute)?(String)$valueinput->attribute:"Empty"),
									'ws-input-attributeText'=>(!empty((String)$valueinput->attribute)?(String)$valueinput->attribute:"Empty"),
									'ws-input-default'=>(!empty((String)$valueinput->default)?(String)$valueinput->default:"Empty"),
									'ws-input-defaultText'=>(!empty((String)$valueinput->default)?(String)$valueinput->default:"Empty"),
									'ws-input-format'=>(!empty((String)$valueinput->format)?(String)$valueinput->format:"Empty"),
									'ws-input-formatText'=>(!empty((String)$valueinput->format)?(String)$valueinput->format:"Empty"),
									'ws-input-name'=>(!empty((String)$valueinput->fieldname)?(String)$valueinput->fieldname:"Empty"),
									'ws-input-nameText'=>(!empty((String)$valueinput->fieldname)?(String)$valueinput->fieldname:"Empty"),
									'ws-input-static'=>(empty(explode(",",CheckAllFirstForAllModules(explode(',',$valueinput->fieldvalue)[0]))[0])?explode(',',$valueinput->fieldvalue)[0]:"Empty"),
									'ws-input-staticText'=>(empty(explode(",",CheckAllFirstForAllModules(explode(',',$valueinput->fieldvalue)[0]))[0])?explode(',',$valueinput->fieldvalue)[0]:"Empty"),
									'Anotherdata'=>array(),
								];

								foreach (explode(',',$valueinput->fieldvalue) as $valuefields)
								{
									if(!empty(explode(",",CheckAllFirstForAllModules(explode(',',$valuefields)[0]))[0]))
									{
										$anotherdata=[
											"DataText"=>explode(",",CheckAllFirstForAllModules(explode(',',$valuefields)[0]))[1],
											"DataValues"=>explode(",",CheckAllFirstForAllModules(explode(',',$valuefields)[0]))[0],
										];
										$Inputfields['Anotherdata'][]=$anotherdata;
									}
								}

								array_push($MyArray,$Inputfields);
							}	
						}

						if (isset($xml->Output->fields))
						{
							foreach ($xml->Output->fields->field as  $valueOutput)
							{
								$Outputfields=[
									'JsonType'=>"Output",
									'DefaultText'=>(string)explode("#", Get_First_Moduls_TextVal($value['FirstModule']))[1],
									'FirstModule' =>(!empty((string)explode("#", Get_First_Moduls_TextVal($valueOutput->Relfield->RelModule))[0])?(string)explode("#", Get_First_Moduls_TextVal($valueOutput->Relfield->RelModule))[0]:(string)explode("#", Get_First_Moduls_TextVal($value['FirstModule']))[0] ),
									'ws-output-select-multipleoptionGroup' =>(!empty((string)explode("#", Get_First_Moduls_TextVal($valueinput->Relfield->RelModule))[0])?(string)explode("#", Get_First_Moduls_TextVal($valueinput->Relfield->RelModule))[0]:(string)explode("#", Get_First_Moduls_TextVal($value['FirstModule']))[0] ),
									'FirstModuleText' =>(!empty((string)explode("#", Get_First_Moduls_TextVal($valueOutput->Relfield->RelModule))[1])?(string)explode("#", Get_First_Moduls_TextVal($valueOutput->Relfield->RelModule))[1]:(string)explode("#", Get_First_Moduls_TextVal($value['FirstModule']))[1] ),
									'Moduli'=>"",
									'ws-label'=>(!empty((String)$valueOutput->fieldlabel)?(String)$valueOutput->fieldlabel:"Empty"),
									'ws-labelText'=>(!empty((String)$valueOutput->fieldlabel)?(String)$valueOutput->fieldlabel:"Empty"),
									'ws-output-attribute'=>(!empty((String)$valueOutput->attribute)?(String)$valueOutput->attribute:"Empty"),
									'ws-output-attributeText'=>(!empty((String)$valueOutput->attribute)?(String)$valueOutput->attribute:"Empty"),
									'ws-output-name'=>(!empty((String)$valueOutput->fieldname)?(String)$valueOutput->fieldname:"Empty"),
									'ws-output-nameText'=>(!empty((String)$valueOutput->fieldname)?(String)$valueOutput->fieldname:"Empty"),
									'ws-input-static'=>(empty(explode(",",CheckAllFirstForAllModules(explode(',',$valueOutput->fieldvalue)[0]))[0])?explode(',',$valueOutput->fieldvalue)[0]:"Empty"),
									'ws-input-staticText'=>(empty(explode(",",CheckAllFirstForAllModules(explode(',',$valueOutput->fieldvalue)[0]))[0])?explode(',',$valueOutput->fieldvalue)[0]:"Empty"),
									'Anotherdata'=>array(),
								];

								foreach (explode(',',$valueOutput->fieldvalue) as $valuefields)
								{
									if(!empty(explode(",",CheckAllFirstForAllModules(explode(',',$valuefields)[0]))[0]))
									{
										$anotherdata=[
											"DataText"=>explode(",",CheckAllFirstForAllModules(explode(',',$valuefields)[0]))[1],
											"DataValues"=>explode(",",CheckAllFirstForAllModules(explode(',',$valuefields)[0]))[0],
										];
										$Outputfields['Anotherdata'][]=$anotherdata;
									}
								}

								array_push($MyArray,$Outputfields);
							}	
						}

						if (isset($xml->valuemap->fields))
						{
							foreach ($xml->valuemap->fields->field as  $valueValueMap)
							{
								$Outputfields=[
									'JsonType'=>"Value Map",
									'DefaultText'=>(string)$valueValueMap->fieldname,
									'Moduli'=>"",
									'ws-value-map-destinamtion'=>(string)$valueValueMap->fielddest,
									'ws-value-map-destinamtionText'=>(string)$valueValueMap->fielddest,
									'ws-value-map-name'=>(string)$valueValueMap->fieldname,
									'ws-value-map-nameText'=>(string)$valueValueMap->fieldname,
									'ws-value-map-source-input'=>(string)$valueValueMap->fieldsrc,
									'ws-value-map-source-inputText'=>(string)$valueValueMap->fieldsrc,
								];
								array_push($MyArray,$Outputfields);
							}	
						}

						if (isset($xml->errorhandler->field))
						{
							$ErrorHandlerfields=[
									'JsonType'=>"Error Handler",
									'DefaultText'=>(string)explode("#", Get_First_Moduls_TextVal($value['FirstModule']))[1],
									'Moduli'=>"",
									'ws-error-message'=>(string)$xml->errorhandler->field->errormessage,
									'ws-error-messageText'=>(string)$xml->errorhandler->field->errormessage,
									'ws-error-name'=>(string)$xml->errorhandler->field->fieldname,
									'ws-error-nameText'=>(string)$xml->errorhandler->field->fieldname,
									'ws-error-value'=>(string)$xml->errorhandler->field->value,
									'ws-error-valueText'=>(string)$xml->errorhandler->field->value,
							];
								array_push($MyArray,$ErrorHandlerfields);
								
						}

						array_push($Alldatas,$MyArray);	
					}



				// value for Save As 
				$data="MapGenerator,saveWebServiceMap";
				$dataid="ListData,MapName";
				$savehistory="true";
				$saveasfunction="ShowLocalHistoryWS";
				
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);

				$smarty->assign("HistoryMap",$HistoryMap);

				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));
				$smarty->assign("FirstModuleFields",$FirstModuleFields);
				$smarty->assign("PopupJS",$Alldatas);
				$output = $smarty->fetch('modules/MapGenerator/WS.tpl');
				echo $output;



			}elseif (!empty($MapID)) {
				//TODO: if exist MAp id but not exist the Query History (put the function here if you want to insert data without history this is for all maps )
			}else
			{
				throw new Exception("Missing the MApID also The QueryHIstory", 1);
				
			}

		} catch (Exception $ex) {
			$log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception ".$ex);
			echo $ex;
		}
	}







	/**
		* function to generate the map type Extended Field Information
		* @param [type] $QueryHistory
		* @param [type] $MapID
		* @return void
	*/
	function ExtendedFieldInformation($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		

		try{
			if (!empty($QueryHistory))
			{
				$FirstModuleSelected="<option value=''>Select module</option>".Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
				//fields 
				$fields="<option value=''>Select a Field</option>".getModFields(explode(',',get_The_history($QueryHistory,"firstmodule"))[0]);
				$FirstModuleFields=$fields;
				$Allhistory=get_All_History($QueryHistory);
				$Alldatas=array();

				foreach ($Allhistory as $value) {
					$xml=new SimpleXMLElement($value['query']);
					$MyArray=array();
					foreach ($xml->fields->field  as  $valuexml) {
						$temparray=[
								'DefaultText'=>(string) explode(",",CheckAllFirstForAllModules((string)$valuexml->fieldname))[1],
								'FirstFields'=>(string) explode(",",CheckAllFirstForAllModules((string)$valuexml->fieldname))[0],
								'FirstFieldsText'=>(string) explode(",",CheckAllFirstForAllModules((string)$valuexml->fieldname))[1],
								'FirstFieldsoptionGroup'=>(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1],
								'FirstModule'=>(string)$xml->originmodule->originname,
								'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1],
								'FirstModuleoptionGroup'=>"undefined",
								'JsonType'=>"Field",
								'Moduli'=>(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1],							
							];
							foreach ($valuexml->features->feature as $valuefeature) {
								$temparray['NameInput']=(string)$valuefeature->name;
								$temparray['NameInputText']=(string)$valuefeature->name;
								$temparray['NameInputoptionGroup']="";

								$temparray['ValueInput']=(string)$valuefeature->value;
								$temparray['ValueInputText']=(string)$valuefeature->value;
								$temparray['ValueInputoptionGroup']="";
							}

							array_push($MyArray,$temparray);
						}
									
					array_push($Alldatas,$MyArray);
				}


				//this is for save as 
				$MapName=get_form_MapQueryID($QueryHistory,"mapname");
				$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
				//this is for save as map
				$data="MapGenerator,saveExtendetFieldInformation";
				$dataid="ListData,MapName";
				$savehistory="true";
				$saveasfunction="ShowLocalHistoryExtendetFieldMap";
				//  //assign tpl
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);
				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);
				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("FirstModuleFields",$FirstModuleFields);
				$smarty->assign("update",$update);

				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

				$smarty->assign("PopupJS",$Alldatas);
				$output = $smarty->fetch('modules/MapGenerator/ExtendedFieldInformationMapping.tpl');
				echo $output;
				// print_r($Alldatas);
				exit();

			}else{
				//TODO:: this is if not find the idquery to load map by Id of map 
			}
		}catch(Exception $ex){
			echo showError("Something was wrong",$ex->getMessage());
			LogFile($ex);
		}
	}



	/**
	 * Function to load the map type RecordSetMapping
	 *
	 * @param      string  $QueryHistory  The query history
	 * @param      <type>  $MapID         The map id
	 */
	function RecordSetMapping($QueryHistory,$MapID)
	{
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		include_once('modfields.php');

		try{
			if (!empty($QueryHistory))
			{
				$FirstModuleSelected="<option value=''>Select module</option>".Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
				
				$Allhistory=get_All_History($QueryHistory);
				$Alldatas=array();

				foreach ($Allhistory as $value) {
					$xml=new SimpleXMLElement($value['query']);
					$MyArray=array();
					foreach ($xml->records->record as  $valuexml) {
						if (!empty($valuexml->id) ) {
							$temparray=[
								'ActionId'=>(string)$valuexml->action,
								'ActionIdText'=>$valuexml->action,
								'ActionIdoptionGroup'=>"undefined",
								'DefaultText'=>(string)$valuexml->id,
								'JsonType'=>"ID",
								'Moduli'=>"",
								'inputforId'=>(string)$valuexml->id,
								'inputforIdText'=>(string)$valuexml->id,
								'inputforIdoptionGroup'=>"",
							];
							array_push($MyArray,$temparray);
						} else {
							$temparray=[
								'ActionId'=>(string)$valuexml->action,
								'ActionIdText'=>$valuexml->action,
								'ActionIdoptionGroup'=>"undefined",
								'DefaultText'=>(string)$valuexml->value,
								'EntityValueId'=>(string)$valuexml->value,
								'EntityValueIdText'=>(string)$valuexml->value,
								'EntityValueIdoptionGroup'=>"",
								'FirstModule'=>(string)$valuexml->module,  //explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1]
								'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal($valuexml->module))[1],
								'FirstModuleoptionGroup'=>"undefined",
								'JsonType'=>"Entity",
								'Moduli'=>(string)$valuexml->module,
							];
							array_push($MyArray,$temparray);
						}
						
						
					}
									
					array_push($Alldatas,$MyArray);
				}


				//this is for save as 
				$MapName=get_form_MapQueryID($QueryHistory,"mapname");
				$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
				//this is for save as map
				$data="MapGenerator,saveRecordSetMapping";
				$dataid="ListData,MapName";
				$savehistory="true";
				$saveasfunction="ShowLocalHistoryRecordSetMapping";
				//  //assign tpl
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);
				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);
				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("update",$update);

				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

				$smarty->assign("PopupJS",$Alldatas);
				$output = $smarty->fetch('modules/MapGenerator/RecordSetMapping.tpl');
				echo $output;
				// print_r($Alldatas);

			}else{
				//TODO:: this is if not find the idquery to load map by Id of map 
			}
		}catch(Exception $ex){
			echo showError("Something was wrong",$ex->getMessage());
			LogFile($ex);
		}
	}



	/**
	 * Function to load the map type Import Business Mapping
	 *
	 * @param      string  $QueryHistory  The query history
	 * @param      <type>  $MapID         The map id
	 */
	function Import($QueryHistory,$MapID)
	{
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		include_once('modfields.php');

		try{
			if (!empty($QueryHistory))
			{
				$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
				$allfields="<option value=''>Select Field</option>".getModFields(get_The_history($QueryHistory,"firstmodule"));			

				$Allhistory=get_All_History($QueryHistory);
				$Alldatas=array();
				$update="";
				foreach ($Allhistory as $value) {
				$xml=new SimpleXMLElement($value['query']);
				$BlockArray=array();
				// $children =(array) $xml->fields;//->children();
					$joinarray=[
						'fields'=>$xml->fields,
						'matches'=>$xml->matches
					];
					
					$Count=count($joinarray["fields"]->field);
					for($i=0;$i<=$Count-1;$i++) {
							$arratoinsert=[
								'JsonType'=>"Match",
								'FirstModule'=>(string)explode("#", Get_First_Moduls_TextVal($xml->targetmodule->targetname))[0],
								'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal($xml->targetmodule->targetname))[1],
								'Moduli'=>(string)explode("#", Get_First_Moduls_TextVal($xml->targetmodule->targetname))[1],
								'DefaultText'=>explode(",",CheckAllFirstForAllModules((string)$joinarray["fields"]->field[$i]->fieldname))[1],
								'Firstfield'=>explode(",",CheckAllFirstForAllModules((string)$joinarray["fields"]->field[$i]->fieldname))[0],
								'FirstfieldText'=>explode(",",CheckAllFirstForAllModules((string)$joinarray["fields"]->field[$i]->fieldname))[1],
								'FirstfieldoptionGroup'=>(string)explode("#", Get_First_Moduls_TextVal($xml->targetmodule->targetname))[1],
								'UpdateId'=>$xml->options->update,
								'SecondField'=>explode(",",CheckAllFirstForAllModules((string)$joinarray["matches"]->match[$i]->fieldname))[0],
								'SecondFieldText'=>explode(",",CheckAllFirstForAllModules((string)$joinarray["matches"]->match[$i]->fieldname))[1],
								'SecondFieldoptionGroup'=>(string)explode("#", Get_First_Moduls_TextVal($xml->targetmodule->targetname))[1]
							];				
										
						array_push($BlockArray,$arratoinsert);
						}
						array_push($Alldatas,$BlockArray);
						$update=$xml->options->update;
				}

				//this is for save as 
				$MapName=get_form_MapQueryID($QueryHistory,"mapname");
				$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
				//this is for save as map
				$data="MapGenerator,saveImportBussinesMapping";
				$dataid="ListData,MapName,UpdateId";
				$savehistory="true";
				$saveasfunction="ShowLocalHistoryImportBussiness";
				//  //assign tpl
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);
				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);
				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("allfields",$allfields);
				$smarty->assign("update",$update);

				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

				$smarty->assign("PopupJS",$Alldatas);
				$output = $smarty->fetch('modules/MapGenerator/ImportBusinessMapping.tpl');
				echo $output;
				// print_r($Alldatas);

			}else{
				//TODO:: this is if not find the idquery to load map by Id of map 
			}
		}catch(Exception $ex){
			echo showError("Something was wrong",$ex->getMessage());
			LogFile($ex);
		}
	}




	/**
	 * Function to load the map type Rendio Conta Config
	 *
	 * @param      string  $QueryHistory  The query history
	 * @param      <type>  $MapID         The map id
	 */
	function RendicontaConfig($QueryHistory,$MapID)
	{
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		include_once('modfields.php');

		try{
			if (!empty($QueryHistory))
			{
				$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
				$allfields=getModFields(get_The_history($QueryHistory,"firstmodule"));

				$Allhistory=get_All_History($QueryHistory);
				$Alldatas=array();

				foreach ($Allhistory as $value) {
					$xml=new SimpleXMLElement($value['query']);
										
						$temparray=[
								'FirstModule'=>(string)$xml->respmodule,
								'FirstModuleText'=>explode("#", Get_First_Moduls_TextVal((string)$xml->respmodule))[1],
								'JsonType'=>"RendicontaConfig",
								'causalefield'=>(!empty(explode(",",CheckAllFirstForAllModules($xml->causalefield))[0])?explode(",",CheckAllFirstForAllModules($xml->causalefield))[0]:$xml->causalefield),
								'causalefieldText'=>(!empty(explode(",",CheckAllFirstForAllModules($xml->causalefield))[1])?explode(",",CheckAllFirstForAllModules($xml->causalefield))[1]:$xml->causalefield),
								'processtemp'=>explode(",",CheckAllFirstForAllModules($xml->processtemp))[0],
								'processtempText'=>explode(",",CheckAllFirstForAllModules($xml->processtemp))[1],
								'statusfield'=>explode(",",CheckAllFirstForAllModules($xml->statusfield))[0],
								'statusfieldText'=>explode(",",CheckAllFirstForAllModules($xml->statusfield))[1],
								
							];
										
					array_push($Alldatas,$temparray);
				}

				//this is for save as 
				$MapName=get_form_MapQueryID($QueryHistory,"mapname");
				$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
				//this is for save as map
				$data="MapGenerator,saveRendicontaConfig";
				$dataid="ListData,MapName";
				$savehistory="true";
				$saveasfunction="ShowLocalHistoryRendiConfig";
				//  //assign tpl
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);
				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);
				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("allfields",$allfields);
				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

				$smarty->assign("PopupJS",$Alldatas);
				$output = $smarty->fetch('modules/MapGenerator/RendicontaConfig.tpl');
				echo $output;

			}else{
				//TODO:: this is if not find the idquery to load map by Id of map 
			}
		}catch(Exception $ex){
			echo showError("Something was wrong",$ex->getMessage());
			LogFile($ex);
		}
	}




	/**
	 * Function to load the map type Dupliacte records
	 *
	 * @param      string  $QueryHistory  The query history
	 * @param      <type>  $MapID         The map id
	 */
	function DuplicateRecords($QueryHistory,$MapID)
	{
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		

		try{
			if (!empty($QueryHistory))
			{
				$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
				$decondRelatedModule=GetAllRelationDuplicaterecords(get_The_history($QueryHistory,"firstmodule"));
				$dupliactererds="";
				$Allhistory=get_All_History($QueryHistory);
				$Alldatas=array();

				foreach ($Allhistory as $value) {
					$xml=new SimpleXMLElement($value['query']);
					$MyArray=array();
					foreach ($xml->relatedmodules->relatedmodule as  $valuexml) {
						$temparray=[
								'DefaultText'=>(string)$valuexml->module,
								'DuplicateDirectRelationscheck'=>$xml->DuplicateDirectRelations,
								'DuplicateDirectRelationscheckoptionGroup'=>"",
								'DuplicateDirectRelationscheckText'=>$xml->DuplicateDirectRelations,
								'FirstModule'=>(string)$xml->originmodule->originname,
								'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal($xml->originmodule->originname))[1],
								'FirstModuleoptionGroup'=>"undefined",
								'JsonType'=>"Relation",
								'Moduli'=>(string)$xml->originmodule->originname,
								'relatedModule'=>(string)$valuexml->module."#".(string)$valuexml->relation,
								'relatedModuleoptionGroup'=>"undefined",
								
							];
							array_push($MyArray,$temparray);
						
					}
					if ((string)$xml->DuplicateDirectRelations === "1") {
						$dupliactererds="checked='checked'";
					}
					else{
						$dupliactererds="";
					}				
					array_push($Alldatas,$MyArray);
				}

				//this is for save as 
				$MapName=get_form_MapQueryID($QueryHistory,"mapname");
				$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
				//this is for save as map
				$data="MapGenerator,saveDuplicateRecords";
				$dataid="ListData,MapName";
				$savehistory="true";
				$saveasfunction="ShowLocalHistoryDuplicateRecords";
				//  //assign tpl
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);
				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);
				$smarty->assign("HistoryMap",$HistoryMap);
				$smarty->assign("dupliactererds",$dupliactererds);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("AllModulerelated",$decondRelatedModule);
				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

				$smarty->assign("PopupJS",$Alldatas);
				$output = $smarty->fetch('modules/MapGenerator/DuplicateRecords.tpl');
				echo $output;

			}else{
				//TODO:: this is if not find the idquery to load map by Id of map 
			}
		}catch(Exception $ex){
			echo showError("Something was wrong",$ex->getMessage());
			LogFile($ex);
		}
	}



	/**
	 * Function to load the map type Record Access Control
	 *
	 * @param      string  $QueryHistory  The query history
	 * @param      <type>  $MapID         The map id
	 */
	function RecordAccessControl($QueryHistory,$MapID)
	{
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		
		if (!empty($QueryHistory))
		{
			$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
			$decondRelatedModule=GetAllrelationModules(get_The_history($QueryHistory,"firstmodule"));

			$Allhistory=get_All_History($QueryHistory);
			$Alldatas=array();
			$ListDetailFlag=array();
			foreach ($Allhistory as $value) {
				$xml=new SimpleXMLElement($value['query']);
				$MyArray=array();
				if ($value["active"]=="1") {
					$viewcheckListview=(string)$xml->listview->r;
					$AddcheckListview=(string)$xml->listview->c;
					$editcheckListview=(string)$xml->listview->u;
					$deletecheckListview=(string)$xml->listview->d;

					$viewcheckDetailView=(string)$xml->detailview->r;
					$duplicatecheckDetailView=(string)$xml->detailview->c;
					$editcheckDetailView=(string)$xml->detailview->u;
					$deletecheckDetailView=(string)$xml->detailview->d;
					
				}
				foreach ($xml->relatedlists->relatedlist as  $valuexml) {
					$temparray=[
							'AddcheckListview'=>(string)$xml->listview->c,
							'AddcheckListviewoptionGroup'=>"",
							'deletecheckListview'=>(string)$xml->listview->d,
							'deletecheckListviewoptionGroup'=>"",
							'editcheckListview'=>(string)$xml->listview->u,
							'editcheckListviewoptionGroup'=>"",
							'viewcheckListview'=>(string)$xml->listview->r,
							'viewcheckListviewoptionGroup'=>"",

							'deletecheckDetailView'=>(string)$xml->detailview->d,
							'deletecheckDetailViewoptionGroup'=>"",
							'duplicatecheckDetailView'=>(string)$xml->detailview->c,
							'duplicatecheckDetailViewoptionGroup'=>"",
							'editcheckDetailView'=>(string)$xml->detailview->u,
							'editcheckDetailViewoptionGroup'=>"",
							'viewcheckDetailView'=>(string)$xml->detailview->r,
							'viewcheckDetailViewoptionGroup'=>"",

							'DefaultText'=>(!empty(explode("#", Get_First_Moduls_TextVal($xml->targetmodule[0]->targetname))[0])?explode("#", Get_First_Moduls_TextVal((string)$valuexml->modulename))[1]:(string)$valuexml->modulename) ,
							'FirstModule'=>(string)$xml->originmodule->originname,
							'FirstModuleText'=>explode("#", Get_First_Moduls_TextVal((string)$xml->originmodule->originname))[1],
							'FirstModuleoptionGroup'=>'undefined',
							'Moduli'=>(string)$xml->originmodule->originname,
							'JsonType'=>"Related",
							'relatedModule'=>(!empty(explode("#", Get_First_Moduls_TextVal($xml->targetmodule[0]->targetname))[0])?explode("#", Get_First_Moduls_TextVal((string)$valuexml->modulename))[0]:(string)$valuexml->modulename),
							'relatedModuleText'=>(!empty(explode("#", Get_First_Moduls_TextVal($xml->targetmodule[0]->targetname))[1])?explode("#", Get_First_Moduls_TextVal((string)$valuexml->modulename))[1]:(string)$valuexml->modulename),
							'relatedModuleoptionGroup'=>'undefined',
							'viewcheckRelatedlist'=>(string)$valuexml->r,
							'viewcheckRelatedlistoptionGroup'=>"",
							'selectcheckrelatedlist'=>(string)$valuexml->s,
							'selectcheckrelatedlistoptionGroup'=>"",
							'editcheckrelatetlist'=>(string)$valuexml->u,
							'editcheckrelatetlistoptionGroup'=>"",
							'deletecheckrelatedlist'=>(string)$valuexml->d,
							'deletecheckrelatedlistoptionGroup'=>"",
							'addcheckRelatetlist'=>(string)$valuexml->c,
							'addcheckRelatetlistoptionGroup'=>"",						
						];
						array_push($MyArray,$temparray);
					
				}
				array_push($Alldatas,$MyArray);
			}

			//this is for save as 
			$MapName=get_form_MapQueryID($QueryHistory,"mapname");
			$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
			//this is for save as map
			$data="MapGenerator,saveRecordAccessControl";
			$dataid="ListData,MapName";
			$savehistory="true";
			$saveasfunction="ShowLocalHistoryRecordAccessControll";
			//  //assign tpl
			$smarty = new vtigerCRM_Smarty();
			$smarty->assign("MOD", $mod_strings);
			$smarty->assign("APP", $app_strings);
			
			$smarty->assign("MapName", $MapName);
			$NameOFMap=$MapName;
			$smarty->assign("NameOFMap",$NameOFMap);
			$smarty->assign("HistoryMap",$HistoryMap);

			$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
			$smarty->assign("AllModulerelated",$decondRelatedModule);
			$smarty->assign("viewcheckListview",$viewcheckListview);
			$smarty->assign("AddcheckListview",$AddcheckListview);
			$smarty->assign("editcheckListview",$editcheckListview);
			$smarty->assign("deletecheckListview",$deletecheckListview);
			$smarty->assign("viewcheckDetailView",$viewcheckDetailView);
			$smarty->assign("duplicatecheckDetailView",$duplicatecheckDetailView);
			$smarty->assign("editcheckDetailView",$editcheckDetailView);
			$smarty->assign("deletecheckDetailView",$deletecheckDetailView);
			//put the smarty modal
			$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

			$smarty->assign("PopupJS",$Alldatas);
			$output = $smarty->fetch('modules/MapGenerator/RecordAccessControl.tpl');
			echo $output;

		}else{
			//TODO:: this is if not find the idquery to load map by Id of map 
		}
	}

	/**
	 * function to load the MENUSTRUCTURE
	 *
	 * @param      string  $QueryHistory  The query history
	 * @param      <type>  $MapID         The map id
	 */
	function MENUSTRUCTURE($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		if (!empty($QueryHistory))
		{
			$FirstModuleSelected=GetTheresultByFile("firstModule.php");
			$Allhistory=get_All_History($QueryHistory);
			$Alldatas=array();

			foreach ($Allhistory as $value) {
				$xml=new SimpleXMLElement($value['query']);
				$MyArray=array();

				foreach ($xml->menus as $menus) {
					foreach ($menus->children() as $child) {
						if ($child->getName()!="parenttab" && $child->getName()!="profile") {
							$temparrayCondition=[
								"ConditionAllFields"=>explode(",",CheckAllFirstForAllModules((string)$child->getName()))[0],
								"ConditionAllFieldsText"=>explode(",",CheckAllFirstForAllModules((string)$child->getName()))[1],
								'ConditionAllFieldsoptionGroup'=> "",
								'DefaultText'=>"" ,
								'JsonType'=>"Conditions",
								'ms-field_value'=>(string)$child,
								'ms-field_valueText'=>(string)$child,
								'Moduli'=>"",
							];
							array_push($MyArray,$temparrayCondition);
						}
					}
					foreach ($menus->parenttab as  $valuexml) {
						foreach ($valuexml->name as  $valuename) {
							$temparrayModul=[
								'DefaultText'=>(string)$valuexml->label ,
								'FirstModule'=> (string)$valuename,
								'FirstModuleText'=> explode("#", Get_First_Moduls_TextVal((string)$valuename))[1],
								'FirstModuleoptionGroup'=>"udentifined" ,
								'JsonType'=>"Module",
								'LabelName'=>(string)$valuexml->label,
								'LabelNameoptionGroup'=>"",
								'Moduli'=>explode("#", Get_First_Moduls_TextVal((string)$valuename))[1],
							];
							array_push($MyArray,$temparrayModul);
						}
					}
					array_push($Alldatas,$MyArray);
				}
			}

			//this is for save as 
			$MapName=get_form_MapQueryID($QueryHistory,"mapname");
			$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
			//this is for save as map
			$data="MapGenerator,saveMenuStructure";
			$dataid="ListData,MapName";
			$savehistory="true";
			$saveasfunction="ShowLocalHistoryMenuStructure";
			//assign tpl
			$smarty = new vtigerCRM_Smarty();
			$smarty->assign("MOD", $mod_strings);
			$smarty->assign("APP", $app_strings);
			
			$smarty->assign("MapName", $MapName);
			$NameOFMap=$MapName;
			$smarty->assign("NameOFMap",$NameOFMap);
			$smarty->assign("HistoryMap",$HistoryMap);

			$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
			//put the smarty modal
			$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

			$smarty->assign("PopupJS",$Alldatas);
			$output = $smarty->fetch('modules/MapGenerator/MENUSTRUCTURE.tpl');
			echo $output;

		}else{
			//TODO:: this is if not find the idquery to load map by Id of map 
		}
	}



	/**
	 * function to generate the map type DETAILVIEWBLOCKPORTAL
	 *
	 * @param      string  $QueryHistory  The query history
	 * @param      <type>  $MapID         The map id
	 */
	function DETAILVIEWBLOCKPORTAL($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		if (!empty($QueryHistory)) {
			//TODO:: if exist the history check by id of history

			$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
			//fields 
			$FirstModuleFields=getModFields(explode(',',get_The_history($QueryHistory,"firstmodule"))[0]);

				// this is for get the history filter by id 
			$Allhistory=get_All_History($QueryHistory);
			$Alldatas=array();

			foreach ($Allhistory as $value) {
				$xml=new SimpleXMLElement($value['query']);
				$BlockArray=array();
				foreach ($xml->blocks->block as $valueblock) {
					$arratoinsert=[
						'BlockName'=>(string)$valueblock->name,
						'BlockNameText'=>(string)$valueblock->name,
						'BlockNameoptionGroup'=>"",
						'FirstModule'=>explode(',',get_The_history($QueryHistory,"firstmodule"))[0],
						'FirstModuleText'=>explode(',',get_The_history($QueryHistory,"firstmodule"))[0],
						'FirstModuleoptionGroup'=>'udentifined',
						'JsonType'=>"Block",
						'rows'=>array(),
					];				
					foreach ($valueblock->row as $valuecolumns) {
						$insertcolumn=[
							'fields'=>array(),
							'texts'=>array()
						];
						foreach ($valuecolumns->column as $valuee) {
							$insertcolumn['fields'][]=explode(",",CheckAllFirstForAllModules((string)$valuee))[0];
							$insertcolumn['texts'][]=explode(",",CheckAllFirstForAllModules((string)$valuee))[1];
						}
						// array_push($insertcolumn,$arrrow);
						$arratoinsert['rows'][]=$insertcolumn;
					}
					
					array_push($BlockArray,$arratoinsert);
				}
				array_push($Alldatas,$BlockArray);
			}


			// print_r($Alldatas);
			// exit();
			//this is for save as 
			$MapName=get_form_MapQueryID($QueryHistory,"mapname");
			$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
			//this is for save as map
			$data="MapGenerator,saveHIstoryDetailViewBlockPortal";
			$dataid="ListData,MapName";
			$savehistory="true";
			$saveasfunction="SavehistoryCreateViewportal";

			//assign tpl
			$smarty = new vtigerCRM_Smarty();
			$smarty->assign("MOD", $mod_strings);
			$smarty->assign("APP", $app_strings);
			
			$smarty->assign("MapName", $MapName);
			$NameOFMap=$MapName;
			$smarty->assign("NameOFMap",$NameOFMap);
			$smarty->assign("HistoryMap",$HistoryMap);

			$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
			$smarty->assign("FirstModuleFields",$FirstModuleFields);
			//put the smarty modal
			$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

			$smarty->assign("PopupJS",$Alldatas);
			$output = $smarty->fetch('modules/MapGenerator/DETAILVIEWBLOCKPORTAL.tpl');
			echo $output;




		} else {
			//TODO:: if not exist by history check by id Of Map
		}
		
	}

	/**
	 * function to generate the map type CreateViewPortal
	 *
	 * @param      string  $QueryHistory  The query history
	 * @param      <type>  $MapID         The map id
	 */
	function CREATEVIEWPORTAL($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		if (!empty($QueryHistory)) {
			//TODO:: if exist the history check by id of history

			$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
			//fields 
			$FirstModuleFields=getModFields(explode(',',get_The_history($QueryHistory,"firstmodule"))[0]);

				// this is for get the history filter by id 
			$Allhistory=get_All_History($QueryHistory);
			$Alldatas=array();

			foreach ($Allhistory as $value) {
				$xml=new SimpleXMLElement($value['query']);
				$BlockArray=array();
				foreach ($xml->blocks->block as $valueblock) {
					$arratoinsert=[
						'BlockName'=>(string)$valueblock->name,
						'BlockNameText'=>(string)$valueblock->name,
						'BlockNameoptionGroup'=>"",
						'FirstModule'=>explode(',',get_The_history($QueryHistory,"firstmodule"))[0],
						'FirstModuleText'=>explode("#", Get_First_Moduls_TextVal(explode(',',get_The_history($QueryHistory,"firstmodule"))[0]))[1],
						'FirstModuleoptionGroup'=>'udentifined',
						'JsonType'=>"Block",
						'rows'=>array(),
					];				
					foreach ($valueblock->row as $valuecolumns) {
						$insertcolumn=[
							'fields'=>array(),
							'texts'=>array()
						];
						foreach ($valuecolumns->column as $valuee) {
							$insertcolumn['fields'][]=explode(",",CheckAllFirstForAllModules((string)$valuee))[0];
							$insertcolumn['texts'][]=explode(",",CheckAllFirstForAllModules((string)$valuee))[1];
						}
						// array_push($insertcolumn,$arrrow);
						$arratoinsert['rows'][]=$insertcolumn;
					}
					
					array_push($BlockArray,$arratoinsert);
				}
				array_push($Alldatas,$BlockArray);
			}


			// print_r($Alldatas);
			// exit();
			//this is for save as 
			$MapName=get_form_MapQueryID($QueryHistory,"mapname");
			$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
			//this is for save as map
			$data="MapGenerator,saveCreateViewPortal";
			$dataid="ListData,MapName";
			$savehistory="true";
			$saveasfunction="SavehistoryCreateViewportal";

			//assign tpl
			$smarty = new vtigerCRM_Smarty();
			$smarty->assign("MOD", $mod_strings);
			$smarty->assign("APP", $app_strings);
			
			$smarty->assign("MapName", $MapName);
			$NameOFMap=$MapName;
			$smarty->assign("NameOFMap",$NameOFMap);
			$smarty->assign("HistoryMap",$HistoryMap);

			$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
			$smarty->assign("FirstModuleFields",$FirstModuleFields);
			//put the smarty modal
			$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

			$smarty->assign("PopupJS",$Alldatas);
			$output = $smarty->fetch('modules/MapGenerator/CREATEVIEWPORTAL.tpl');
			echo $output;




		} else {
			//TODO:: if not exist by history check by id Of Map
		}
		
	}


	/**
	 * function to generate map type Condition Expression
	 *
	 * @param      <type>  $QueryHistory  The query history
	 * @param      <type>  $MapID         The map id
	 */

	function ConditionExpression($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";

		if (!empty($QueryHistory)) {
			//TODO:: if exist the id of history goes here 
			$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
			//fields 
			$fields="<option value=''>Select a Field</option>".getModFields(explode(',',get_The_history($QueryHistory,"firstmodule"))[0]);
			$FirstModuleFields=$fields;

			// this is for get the history filter by id 
			$Allhistory=get_All_History($QueryHistory);
			$Alldatas=array();
			$Expresionshow="";
			$FunctionNameshow="";
			foreach ($Allhistory as $value) {
				$xml=new SimpleXMLElement($value['query']);
				$ConditionArray = array();
				if (isset($xml->expression)) {
					$temparray=[
						'DefaultText'=>(string)$xml->expression,
						'Firstmodule'=>explode("#", Get_First_Moduls_TextVal($value["FirstModule"]))[0],
						'FirstModuleText'=>explode("#", Get_First_Moduls_TextVal($value["FirstModule"]))[1],
						'FirstModuleoptionGroup'=>"undefined",
						'Firstfield'=>(!empty(explode(",",CheckAllFirstForAllModules((string)$xml->expression))[0]))?explode(",",CheckAllFirstForAllModules((string)$xml->expression))[0]:"0",
						'FirstfieldText'=>(string)$xml->expression,
						'FirstfieldoptionGroup'=>"udentifined",
						'JsonType'=>"Expression",
						'expresion'=>(string)$xml->expression,
						'expresionText'=>(string)$xml->expression,
						'expresionoptionGroup'=>"udentifined",
					];
					$Expresionshow=(string)$xml->expression;
					array_push($ConditionArray,$temparray);
					array_push($Alldatas,$ConditionArray);
				}
				else {
					$FunctionNameshow=(string)$xml->function->name;
					foreach ($xml->function->parameters->parameter as $valuee) {
						
						if (!empty(explode(",",CheckAllFirstForAllModules($valuee))[0])) {
							$temparray=[
								'DefaultText'=>explode(",",CheckAllFirstForAllModules((string)$valuee))[1],
								'Firstfield2Text'=>explode(",",CheckAllFirstForAllModules((string)$valuee))[1],
								'Firstfield2'=>explode(",",CheckAllFirstForAllModules((string)$valuee))[0],
								'Firstfield2optionGroup'=>explode("#", Get_First_Moduls_TextVal($value["FirstModule"]))[0],
								'Firstmodule2'=>explode("#", Get_First_Moduls_TextVal($value["FirstModule"]))[0],
								'Firstmodule2Text'=>explode("#", Get_First_Moduls_TextVal($value["FirstModule"]))[1],
								'Firstfield2optionGroup'=>explode("#", Get_First_Moduls_TextVal($value["FirstModule"]))[0],
								'Firstmodule2optionGroup'=>"undefined",
								'FunctionName'=>(string)$xml->function->name,
								'FunctionNameoptionGroup'=>"",
								'JsonType'=>"Function"
							];
							array_push($ConditionArray,$temparray);
						} else {
							$temparray=[
								'DefaultText'=>(string)$valuee,
								'DefaultValueFirstModuleField_1'=>(string)$valuee,
								'DefaultValueFirstModuleField_1Text'=>(string)$valuee,
								'DefaultValueFirstModuleField_1optionGroup'=>"",
								'Firstmodule2'=>explode("#", Get_First_Moduls_TextVal($value["FirstModule"]))[0],
								'Firstmodule2Text'=>explode("#", Get_First_Moduls_TextVal($value["FirstModule"]))[1],
								'Firstmodule2optionGroup'=>"undefined",
								'FunctionName'=>(string)$xml->function->name,
								'FunctionNameText'=>(string)$xml->function->name,
								'FunctionNameoptionGroup'=>"",
								'JsonType'=>"Parameter"
							];						
							array_push($ConditionArray,$temparray);
						}
						
					}
					array_push($Alldatas,$ConditionArray);
				}
				
				
			}

			// print_r($Alldatas);
			// exit();
			//this is for save as 
			$MapName=get_form_MapQueryID($QueryHistory,"mapname");
			$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
			//this is for save as map
			$data="MapGenerator,saveConditionExpresion";
			$dataid="ListData,MapName";
			$savehistory="true";

			//assign tpl
			$smarty = new vtigerCRM_Smarty();
			$smarty->assign("MOD", $mod_strings);
			$smarty->assign("APP", $app_strings);
			
			$smarty->assign("MapName", $MapName);
			$NameOFMap=$MapName;
			$smarty->assign("NameOFMap",$NameOFMap);
			$smarty->assign("HistoryMap",$HistoryMap);

			$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
			$smarty->assign("FirstModuleFields",$FirstModuleFields);
			$smarty->assign("Expresionshow",$Expresionshow);
			$smarty->assign("FunctionNameshow",$FunctionNameshow);
			//put the smarty modal
			$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings));

			$smarty->assign("PopupJS",$Alldatas);
			$output = $smarty->fetch('modules/MapGenerator/ConditionExpression.tpl');
			echo $output;

		} else {
			//TODO:: if not exist the id history load by Map id 
		}	
	}


	/**
	 * function to load the map type GlobalSearchAutocompleate 
	 *
	 * @param      string  $QueryHistory  The query history
	 * @param      <type>  $MapID         The map id
	 */
	function GlobalSearchAutocomplete($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";

		if (!empty($QueryHistory)) {
			//TODO:: check if exist the history
			$firstmodulearray=explode(',',get_The_history($QueryHistory,"firstmodule"));
			$firstmodule="<option value=''>Select a Module</option>".Get_First_Moduls(end($firstmodulearray));
			$FirstModuleSelected=$firstmodule;
			//fields 
			$FirstModuleFields=getModFields(explode(',',get_The_history($QueryHistory,"firstmodule"))[0]);

			$arrayName = array();
			$Picklistdropdown=getModFields(get_The_history($QueryHistory,"firstmodule"),'',$arrayName,"15,33");
			
				//all history 
			$Allhistory=get_All_History($QueryHistory);

			$Alldatas=array();

			foreach ($Allhistory as $value) {
				
				$xml=new SimpleXMLElement($value['query']);

				// echo $xml->searchin[0]->module->searchfields;
				$MyArray=array();

				foreach ($xml->searchin->module as $value) {
					$PutInArray=[
						'DefaultText'=>explode("#", Get_First_Moduls_TextVal($value->name))[1],
						'FirstModule'=>explode("#", Get_First_Moduls_TextVal($value->name))[0],
						'FirstModuleoptionGroup'=>'udentifined',
						'Firstfield'=>array(),
						'Firstfield2'=>array(),
						'Firstfield2optionGroup'=>explode("#", Get_First_Moduls_TextVal($value->name))[1],
						'FirstfieldoptionGroup'=>explode("#", Get_First_Moduls_TextVal($value->name))[1],
						'JsonType'=>"Search",
						'startwithchck'=>($value->searchcondition=="startswith")?1:0,
						'startwithchckoptionGroup'=>"",
					];

					foreach (explode(",",$value->searchfields) as $valueseaerch) {
						$PutInArray["Firstfield"][]=(!empty(explode(",",CheckAllFirstForAllModules($valueseaerch))[0])) ?explode(",",CheckAllFirstForAllModules($valueseaerch))[0] : (string) $valueseaerch;
					}
					foreach (explode(",",$value->showfields) as $valueshow) {
						$PutInArray["Firstfield2"][]=(!empty(explode(",",CheckAllFirstForAllModules($valueshow))[0])) ?explode(",",CheckAllFirstForAllModules($valueshow))[0] : (string) $valueshow;
					}
					array_push($MyArray,$PutInArray);
				}
				array_push($Alldatas,$MyArray);
			}

			// print_r($Alldatas);

			//this is for save as 
			$MapName=get_form_MapQueryID($QueryHistory,"mapname");
			$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
			//this is for save as map
			$data="MapGenerator,saveGlobalSearchAutocomplete";
			$dataid="ListData,MapName";
			$savehistory="true";
			$saveasfunction="LoaclHistoryGSA";

			//assign tpl
			$smarty = new vtigerCRM_Smarty();
			$smarty->assign("MOD", $mod_strings);
			$smarty->assign("APP", $app_strings);
			
			$smarty->assign("MapName", $MapName);
			$NameOFMap=$MapName;
			$smarty->assign("NameOFMap",$NameOFMap);
			$smarty->assign("HistoryMap",$HistoryMap);

			$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
			$smarty->assign("FirstModuleFields",$FirstModuleFields);
			//put the smarty modal
			$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

			$smarty->assign("PopupJS",$Alldatas);
			$output = $smarty->fetch('modules/MapGenerator/GlobalSearchAutocomplete.tpl');
			echo $output;


		}else
		{
			//TODO:: if not exist the history check by Map id
		}
	}

	/**
	 * function to load all history for map ty Field Dependency
	 *
	 * @param      <type>  $QueryHistory  The Id of History Table
	 * @param      <type>  $MapID         The id Of map 
	 */
	function FieldDependency($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";

		if (!empty($QueryHistory)) {
			//TODO:: if find the id of of history table 
			
			$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
			//fields 
			$FirstModuleFields=getModFields(get_The_history($QueryHistory,"firstmodule"));

			//
			$arrayName = array();
			$select="<option selected value=''>Select a Picklist</option>";
			$select.=getModFields(get_The_history($QueryHistory,"firstmodule"),'',$arrayName,"15,33");
			$Picklistdropdown=$select;
			
				//all history 
			$Allhistory=get_All_History($QueryHistory);

			$Alldatas=array();

			foreach ($Allhistory as $value) {
					
				$xml= new SimpleXMLElement($value['query']);	
				// echo $value['query'];	
				///for condition query
				$MyArray=array();

				//this is for responsabile fields 
				foreach ($xml->fields->field->Responsiblefield as  $xmlval) {
					
					$ResponsibileArr=[
						'DefaultText'=>(string)(!empty(explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[1])) ?explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[1] :  (string)$xmlval->fieldname,
						'DefaultValueResponsibel'=>(string)$xmlval->fieldvalue,
						'DefaultValueResponsibeloptionGroup'=>"",
						'FirstModule'=>(string)$xml->targetmodule->targetname,
						'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal((string)$xml->targetmodule->targetname))[1],
						'FirstModuleoptionGroup'=>'undefined',
						'Firstfield'=>(string)(!empty(explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[0])) ?explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[0] :  (string)$xmlval->fieldname,
						'FirstfieldoptionGroup'=>(string)$xml->targetmodule->targetname,
						'JsonType'=>'Responsible',
						'Conditionalfield'=>(string)$xmlval->comparison,
						'ConditionalfieldoptionGroup'=>'undefined'


					];

					array_push($MyArray,$ResponsibileArr);
				}

				//this is for Fields 
				foreach ($xml->fields->field->Orgfield as $xmlval) {
					
					$FieldsArray=[
						'DefaultText'=>(string)(!empty(explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[1])) ?explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[1] :  (string)$xmlval->fieldname,
						'FirstModule'=>(string)$xml->targetmodule->targetname,
						'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal((string)$xml->targetmodule->targetname))[1],
						'FirstModuleoptionGroup'=>'undefined',
						'Firstfield2'=>(string)(!empty(explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[0])) ?explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[0] :  (string)$xmlval->fieldname,
						'Firstfield2optionGroup'=>(string)$xml->targetmodule->targetname,
						'JsonType'=>'Field',
						'ReadonlycheckoptionGroup'=>'',
						'ShowHidecheckoptionGroup'=>'',
						'mandatorychk'=>(string)(!empty($xmlval->mandatory))?1:0,
						'mandatorychkoptionGroup'=>'',
						'ShowHidecheck'=>($xmlval->fieldaction=='show')?0:1,
						'Readonlycheck'=>($xmlval->fieldaction=='readonly')?1:0,
					];
					// foreach ($xmlval->fieldaction as  $value) {
						
					// }

					array_push($MyArray,$FieldsArray);

				}

				// this is for pick list
				foreach ($xml->fields->field->Picklist as $picklistval) {
					$PicklistArray=[
						'DefaultText'=>(!empty(explode(",",CheckAllFirstForAllModules($picklistval->fieldname))[1])) ?explode(",",CheckAllFirstForAllModules($picklistval->fieldname))[1] :(string)$picklistval->fieldname,
						'JsonType'=>'Picklist',
						'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal(get_The_history($QueryHistory,"firstmodule")))[1],
						'FirstModule'=>(string)explode("#", Get_First_Moduls_TextVal(get_The_history($QueryHistory,"firstmodule")))[0],
						'PickListFields'=>(!empty(explode(",",CheckAllFirstForAllModules($picklistval->fieldname))[0])) ?explode(",",CheckAllFirstForAllModules($picklistval->fieldname))[0] :(string)$picklistval->fieldname,
						'PickListFieldsoptionGroup'=>'udentifined',
						// 'length'=>$picklistval->values,
						

					];
					$index=count($picklistval->values);
					for ($i=0; $i <=$index-1 ; $i++) { 
						$PicklistArray['DefaultValueFirstModuleField_'.($i+1)]=(string)$picklistval->values[$i];
						$PicklistArray['DefaultValueFirstModuleField_'.($i+1).'optionGroup']='undefined';
						// array_push($PicklistArray,$values);
					}
					array_push($MyArray,$PicklistArray);
				}
				array_push($Alldatas,$MyArray);
			}

			//this is for save as 
			$MapName=get_form_MapQueryID($QueryHistory,"mapname");
			$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
			$NameOFMap=get_form_MapQueryID($QueryHistory,"mapname");
			// $smarty=new vtigerCRM_Smarty();
			$data="MapGenerator,saveFieldDependency";
			$dataid="ListData,MapName";
			$savehistory="true";
			$saveasfunction="ShowLocalHistoryFD";

			//assign the tpl
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);

				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("FirstModuleFields",$FirstModuleFields);
				$smarty->assign("Picklistdropdown",$Picklistdropdown);
				$smarty->assign("NameOFMap",$NameOFMap);
				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction));

				$smarty->assign("PopupJS",$Alldatas);
				$output = $smarty->fetch('modules/MapGenerator/FieldDependency.tpl');
				echo $output;

		} else {
			//TODO:: this is if not find the id in history table 
			
		
		}
	}


	/**
	 * function to load all history for map ty Field Dependency Portal
	 *
	 * @param      <type>  $QueryHistory  The Id of History Table
	 * @param      <type>  $MapID         The id Of map 
	 */
	function FieldDependencyPortal($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";

		if (!empty($QueryHistory)) {
			//TODO:: if find the id of of history table 
			
			$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
			//fields 
			$FirstModuleFields=getModFields(get_The_history($QueryHistory,"firstmodule"));

			//
			$arrayName = array();
			$select="<option selected value=''>Select a Picklist</option>";
			$select.=getModFields(get_The_history($QueryHistory,"firstmodule"),'',$arrayName,"15,33");
			$Picklistdropdown=$select;
			
				//all history 
			$Allhistory=get_All_History($QueryHistory);

			$Alldatas=array();

			foreach ($Allhistory as $value) {
					
				$xml= new SimpleXMLElement($value['query']);	
				// echo $value['query'];	
				///for condition query
				$MyArray=array();

				//this is for responsabile fields 
				foreach ($xml->fields->field->Responsiblefield as  $xmlval) {
					
					$ResponsibileArr=[
						'DefaultText'=>(string)(!empty(explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[1])) ?explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[1] :  (string)$xmlval->fieldname,
						'DefaultValueResponsibel'=>(string)$xmlval->fieldvalue,
						'DefaultValueResponsibeloptionGroup'=>"",
						'FirstModule'=>(string)$xml->targetmodule->targetname,
						'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal((string)$xml->targetmodule->targetname))[1],
						'FirstModuleoptionGroup'=>'undefined',
						'Firstfield'=>(string)(!empty(explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[0])) ?explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[0] :  (string)$xmlval->fieldname,
						'FirstfieldoptionGroup'=>(string)$xml->targetmodule->targetname,
						'JsonType'=>'Responsible',
						'Conditionalfield'=>(string)$xmlval->comparison,
						'ConditionalfieldoptionGroup'=>'undefined'
					];

					array_push($MyArray,$ResponsibileArr);
				}

				//this is for Fields 
				foreach ($xml->fields->field->Orgfield as $xmlval) {
					
					$FieldsArray=[
						'DefaultText'=>(string)(!empty(explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[1])) ?explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[1] :  (string)$xmlval->fieldname,
						'FirstModule'=>(string)$xml->targetmodule->targetname,
						'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal((string)$xml->targetmodule->targetname))[1],
						'FirstModuleoptionGroup'=>'undefined',
						'Firstfield2'=>(string)(!empty(explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[0])) ?explode(",",CheckAllFirstForAllModules($xmlval->fieldname))[0] :  (string)$xmlval->fieldname,
						'Firstfield2optionGroup'=>(string)$xml->targetmodule->targetname,
						'JsonType'=>'Field',
						'ReadonlycheckoptionGroup'=>'',
						'ShowHidecheckoptionGroup'=>'',
						'mandatorychk'=>(string)(!empty($xmlval->mandatory))?1:0,
						'mandatorychkoptionGroup'=>'',
						'ShowHidecheck'=>($xmlval->fieldaction=='show')?0:1,
						'Readonlycheck'=>($xmlval->fieldaction=='readonly')?1:0,
					];
					// foreach ($xmlval->fieldaction as  $value) {
						
					// }

					array_push($MyArray,$FieldsArray);

				}

				// this is for pick list
				foreach ($xml->fields->field->Picklist as $picklistval) {
					$PicklistArray=[
						'DefaultText'=>(!empty(explode(",",CheckAllFirstForAllModules($picklistval->fieldname))[1])) ?explode(",",CheckAllFirstForAllModules($picklistval->fieldname))[1] :(string)$picklistval->fieldname,
						'JsonType'=>'Picklist',
						'FirstModuleText'=>(string)explode("#", Get_First_Moduls_TextVal(get_The_history($QueryHistory,"firstmodule")))[1],
						'FirstModule'=>(string)explode("#", Get_First_Moduls_TextVal(get_The_history($QueryHistory,"firstmodule")))[0],
						'PickListFields'=>(!empty(explode(",",CheckAllFirstForAllModules($picklistval->fieldname))[0])) ?explode(",",CheckAllFirstForAllModules($picklistval->fieldname))[0] :(string)$picklistval->fieldname,
						'PickListFieldsoptionGroup'=>'udentifined',
						// 'length'=>$picklistval->values,

					];
					$index=count($picklistval->values);
					for ($i=0; $i <=$index-1 ; $i++) { 
						$PicklistArray['DefaultValueFirstModuleField_'.($i+1)]=(string)$picklistval->values[$i];
						$PicklistArray['DefaultValueFirstModuleField_'.($i+1).'optionGroup']='undefined';
						// array_push($PicklistArray,$values);
					}
					array_push($MyArray,$PicklistArray);
				}
				array_push($Alldatas,$MyArray);
			}

			//this is for save as 
			$MapName=get_form_MapQueryID($QueryHistory,"mapname");
			$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
			$NameOFMap=get_form_MapQueryID($QueryHistory,"mapname");
			// $smarty=new vtigerCRM_Smarty();
			$data="MapGenerator,saveFieldDependencyPortal";
			$dataid="ListData,MapName";
			$savehistory="true";
			$saveasfunction="ShowLocalHistoryFD";

			//assign the tpl
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);

				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("FirstModuleFields",$FirstModuleFields);
				$smarty->assign("Picklistdropdown",$Picklistdropdown);
				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,saveasfunction));

				$smarty->assign("PopupJS",$Alldatas);
				$smarty->assign("NameOFMap",$NameOFMap);
				$output = $smarty->fetch('modules/MapGenerator/FieldDependencyPortal.tpl');
				echo $output;

		} else {
			//TODO:: this is if not find the id in history table 
			
		
		}
	}




	/**
	 * function to make the load map type IOMap
	 *
	 * @param      string     $QueryHistory  The query history
	 * @param      <type>     $MapID         The map id
	 *
	 * @throws     Exception  if something goes wrong pas as exception
	 */
	function Module_IOMap($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		if (!empty($QueryHistory)) {
			
				$FirstModuleSelected=explode("#",GetAllModules());
				$allfields="<option value=''>( Select a field )</option>";
				foreach ($FirstModuleSelected as $value) {
						$allfields.=getModFields($value, $acno.$dbname);
					}	


				//all history 
				$Allhistory=get_All_History($QueryHistory);

				$Alldatas=array();
				foreach ($Allhistory as $valuehistory) {
					
					$xml= new SimpleXMLElement($valuehistory['query']);				
					///for condition query
					$MyArray=array();
					foreach ($xml->input->fields->field as $value) {
						$arrayy=array();
						if (!empty(explode(",",CheckAllFirstForAllModules($value->fieldname))[0])) {
							$arrayy=[
								"DefaultText"=>explode(",",CheckAllFirstForAllModules($value->fieldname))[1],
								"AllFieldsInput"=>explode(",",CheckAllFirstForAllModules($value->fieldname))[0],
								"AllFieldsInputText"=>explode(",",CheckAllFirstForAllModules($value->fieldname))[1],
								"AllFieldsInputoptionGroup"=>explode("#",containsArray($value->fieldname,explode(",",$valuehistory['labels'])))[1],
								"JsonType"=>"Input",
								"Moduli"=>explode("#",containsArray($value->fieldname,explode(",",$valuehistory['labels'])))[0]
							];
						} else {
							$arrayy=[
								"DefaultText"=>$value->fieldname,
								"AllFieldsInputByhand"=>$value->fieldname,
								"AllFieldsInputByhandText"=>$value->fieldname,
								"AllFieldsInputByhandoptionGroup"=>"",
								"JsonType"=>"Input",
								"Moduli"=>""
							];
						}
						array_push($MyArray,$arrayy);
						// print_r($arrayy);
					}

					foreach ($xml->output->fields->field as $value) {
						if (!empty(explode(",",CheckAllFirstForAllModules($value->fieldname))[0])) {
							$arrayy=[
								"DefaultText"=>explode(",",CheckAllFirstForAllModules($value->fieldname))[1],
								"AllFieldsOutputselect"=>explode(",",CheckAllFirstForAllModules($value->fieldname))[0],
								"AllFieldsOutputselectText"=>explode(",",CheckAllFirstForAllModules($value->fieldname))[1],
								"AllFieldsOutputselectoptionGroup"=>explode("#",containsArray($value->fieldname,explode(",",$valuehistory['labels'])))[1],
								"JsonType"=>"Output",
								"Moduli"=>explode("#",containsArray($value->fieldname,explode(",",$valuehistory['labels'])))[0]
							];
						} else {
							$arrayy=[
								"DefaultText"=>$value->fieldname,
								"AllFieldsOutputbyHand"=>$value->fieldname,
								"AllFieldsOutputbyHandText"=>$value->fieldname,
								"AllFieldsOutputbyHandoptionGroup"=>"",
								"JsonType"=>"Output",
								"Moduli"=>""
							];
						}
						
						array_push($MyArray,$arrayy);
						// print_r($arrayy);
					}	
					array_push($Alldatas,$MyArray);
				}
				$MapName=get_form_MapQueryID($QueryHistory,"mapname");

				$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
				$smarty=new vtigerCRM_Smarty();
				$data="MapGenerator,saveTypeIOMap";
				$dataid="ListData,MapName";
				$savehistory="true";
				
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);
				// $smarty->assign("Allfilds", $MapName);

				$smarty->assign("HistoryMap",$HistoryMap);
				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);

				$smarty->assign("allfields",$allfields);
				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings));

				$smarty->assign("PopupJS",$Alldatas);
				$output = $smarty->fetch('modules/MapGenerator/IOMap.tpl');
				echo $output;
			
			
			}else if(!empty($MapID)) {

				# code...
				# 
			}else{
				throw new Exception("Missing the MApID also The QueryHIstory", 1);
			}
	} 



	/**
	 * this is to load the moduloe set 
	 *
	 * @param      string     $QueryHistory  The query history
	 * @param      <type>     $MapID         The map id
	 *
	 * @throws     Exception  (description)
	 */
	function Module_Set_Mapping($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		if (!empty($QueryHistory)) {
			
			$FirstModuleSelected=GetTheresultByFile("firstModule.php");

			$FirstModuleFields=getModFields(get_The_history($QueryHistory,"firstmodule"));

			//all history 
				$Allhistory=get_All_History($QueryHistory);

				$Alldatas=array();

				foreach ( $Allhistory as $value) {
					
					$xml= new SimpleXMLElement($value['query']);		
					///for condition query
					$MyArray=array();
					foreach ($xml->modules->module as $value) {
						$arrayy=[
							"DefaultText"=>explode("#", Get_First_Moduls_TextVal($value))[1],
							"FirstModule"=>explode("#", Get_First_Moduls_TextVal($value))[0],
							"FirstModuleText"=>explode("#", Get_First_Moduls_TextVal($value))[1],
							"firstModuleoptionGroup"=>"undefined",
							"HistoryValueToShow"=>" ",
							"HistoryValueToShowText"=>" ",
							"HistoryValueToShowoptionGroup"=>" ",
							"JsonType"=>"Module",
							"Moduli"=>"",
						];
						array_push($MyArray,$arrayy);
						// print_r($arrayy);
					}
					array_push($Alldatas,$MyArray);

				}
				// print_r($Allhistory);
				// exit();
				

				$MapName=get_form_MapQueryID($QueryHistory,"mapname");
				$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
				$smarty=new vtigerCRM_Smarty();
				$data="MapGenerator,saveModuleSet";
				$dataid="ListData,MapName";
				$savehistory="true";
				
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);
				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);
				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings));

				$smarty->assign("PopupJS",$Alldatas);
				$output = $smarty->fetch('modules/MapGenerator/Module_Set.tpl');
				echo $output;

			}else if(!empty($MapID)) {

				# code...
				# 
			}else{
				throw new Exception("Missing the MApID also The QueryHIstory", 1);
			}
	}




	/**
	 * function to generate the map selected  for Condition query 
	 * @param [type] $QueryHistory QueryId from tables of history
	 * @param [type] $MapID        the id of Map
	 */
	function ConditionQuery($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		if (!empty($QueryHistory)) {
			
			$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
			
			$SecondModulerelation=GetModulToAll(get_The_history($QueryHistory,"firstmodule"),get_The_history($QueryHistory,"secondmodule"));

			$FirstModuleID=get_The_history($QueryHistory,"firstmodulelabel");
			$SecondModuleID=get_The_history($QueryHistory,"secondmodulelabel");

			$ArrayLabels=explode(',',get_The_history($QueryHistory,"labels"));
			$Arrayfields= array();
			foreach ($ArrayLabels as $value) {
				$Arrayfields[]= explode(':',$value)[2];
			}

			$xml= new SimpleXMLElement(get_The_history($QueryHistory,"query"));
			
			$FieldsArrayall=getModFields(get_The_history($QueryHistory,"firstmodule"),$dbname,$Arrayfields).getModFields(get_The_history($QueryHistory,"secondmodule"),$dbname,$Arrayfields);

			$ReturnFieldsValue=explode(",",Get_Modul_fields_check_from_load(get_The_history($QueryHistory,"firstmodule"),(string)$xml->return))[0];
			$ReturnFieldsText=explode(",",Get_Modul_fields_check_from_load(get_The_history($QueryHistory,"firstmodule"),(string)$xml->return))[1];


			$MapName=get_form_MapQueryID($QueryHistory,"mapname");
			$HistoryMap=$QueryHistory.",".get_form_MapQueryID($QueryHistory,"cbmapid");
			


			$Allhistory=get_All_History($QueryHistory);

			$Alldatas=array();

			foreach ($Allhistory as $value) {
				$xml=new SimpleXMLElement(get_The_history($QueryHistory,"query",$value['sequence']));
				$Allhistorys = [
					'FirstModuleJSONfield'=>get_The_history($QueryHistory,"firstmodulelabel",$value['sequence']),
					'FirstModuleJSONtext'=>get_The_history($QueryHistory,"firstmoduletext",$value['sequence']),
					'FirstModuleJSONvalue'=>get_The_history($QueryHistory,"firstmodule",$value['sequence']),
					'Labels'=>get_The_history($QueryHistory,"labels",$value['sequence']),
					'returnvaluestetx'=>(string) explode(",",Get_Modul_fields_check_from_load(get_The_history($QueryHistory,"secondmodule",$value['sequence']),$xml->return))[1],
					'SecondModuleJSONfield'=>get_The_history($QueryHistory,"secondmodulelabel",$value['sequence']),
					'SecondModuleJSONtext'=>get_The_history($QueryHistory,"secondmoduletext",$value['sequence']),
					'SecondModuleJSONvalue'=>get_The_history($QueryHistory,"secondmodule",$value['sequence']),
					'ValuesParagraf'=>$xml->sql,
					'idJSON'=>$value['sequence'],				
					'returnvaluesval'=>(string) explode(",",Get_Modul_fields_check_from_load(get_The_history($QueryHistory,"secondmodule",$value['sequence']),$xml->return))[0]
				];
				array_push($Alldatas,$Allhistorys);
			}






			$smarty = new vtigerCRM_Smarty();
			$smarty->assign("MOD", $mod_strings);
			$smarty->assign("APP", $app_strings);
			
			$smarty->assign("MapName", $MapName);
			$smarty->assign("MapID",$HistoryMap);

			$smarty->assign("FieldsArrayall",$FieldsArrayall);

			$smarty->assign("FmoduleID",$FirstModuleID);
			$smarty->assign("SmoduleID",$SecondModuleID);

			// for where condition 
			$smarty->assign("MODULE", $currentModule);
			$smarty->assign("IMAGE_PATH", $image_path);
			// $smarty->assign("DATEFORMAT", $current_user->date_format);
			$smarty->assign("QUERY", (string)$xml->sql);

			$smarty->assign("valueli",putThecondition($QueryHistory,(string)$xml->sql,$ArrayLabels));
			// $smarty->assign("texticombo", $texticombo);
			$smarty->assign("FOPTION", '');

			$NameOFMap=$MapName;
			$smarty->assign("NameOFMap",$NameOFMap);

			// $smarty->assign("FIELDLABELS", $campiSelezionatiLabels);
			$smarty->assign("JS_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT'])); 
			
			// $smarty->assign("ValueForCondition",putThecondition($QueryHistory,(string)$xml->sql,$ArrayLabels));
			$smarty->assign("PopupJS",$Alldatas);
			$smarty->assign("ReturnFieldsValue",$ReturnFieldsValue);
			$smarty->assign("ReturnFieldsText",$ReturnFieldsText);

			$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
			$smarty->assign("SecondModulerelation",$SecondModulerelation);

			//put the smarty modal
			// $smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings));
			// $smarty->assign("FirstModuleFields",$FirstModuleFields);
			// $smarty->assign("PopupJS",$MyArray);

			// $smarty->assign("SecondModuleFields",$SecondModuleFields);

			$output = $smarty->fetch('modules/MapGenerator/createJoinCondition.tpl');
			echo $output;
			
			
			}else if(!empty($MapID)) {

				# code...
				# 
			}else{
				throw new Exception("Missing the MApID also The QueryHIstory", 1);
			}
		
	}


	/**
	 * List_Coluns function is to load the map type list coluns
	 * @param [type] $QueryHistory  The id of History of Map 
	 * @param [type] $MapID        The id of Map 
	 */
	function List_Clomns($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";

		try {

			if (!empty($QueryHistory)) 
			{
				//TODO: if have query history
				
				$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));

				$SecondModulerelation="<option value=''>Select a module</option>".GetModuleMultiToOne(get_The_history($QueryHistory,"firstmodule"),get_The_history($QueryHistory,"secondmodule"));

				$FirstModuleFields="<option value=''>Select a Field</option>".getModFields(get_The_history($QueryHistory,"firstmodule"));

				$SecondModuleFields="<option value=''>Select a Field</option>".getModFields(get_The_history($QueryHistory,"secondmodule"));

				$MapName=get_form_Map($MapID,"mapname");

				$HistoryMap=$QueryHistory.",".$MapID;

				
				$xml=new SimpleXMLElement(get_The_history($QueryHistory,"query"));
				$SmoduleID=(string) $xml->relatedlists[0]->relatedlist->linkfield;
				$FmoduleID=(string)  $xml->popup->linkfield;

				//all history 
				$Allhistory=get_All_History($QueryHistory);
				$Allhistoryload = array();
				foreach ($Allhistory as $value) {
					$MyArray=array();
					$xml= new SimpleXMLElement($value['query']); 
					foreach($xml->relatedlists->relatedlist as $field)
					{
						$ArrayRelated=[
							
							'DefaultText'=>(string)$field->columns->field->label,
							'DefaultValue' =>(string)$field->columns->field->label,
							'DefaultValueoptionGroup'=>"",
							'FirstModule' =>(string) $field->module,
							'FirstModuleoptionGroup' =>"undefined",
							'FirstfieldID'=>(string)$xml->popup->linkfield,
							'FirstfieldIDoptionGroup'=>"",
							'JsonType'=>"Related List",
							'SecondField'=>(string)explode(",",Get_Modul_fields_check_from_load($xml->originmodule->originname,$field->columns->field->name))[0],
							'SecondFieldoptionGroup'=>(string)$xml->originmodule->originname,
							'SecondfieldID'=>(string)$field->linkfield,
							'SecondfieldIDoptionGroup'=>"",
							'secmodule'=>(string)explode(",",GetModuleMultiToOneForLOadListColumns($field->module,$xml->originmodule->originname))[0],
							'secmoduleText'=>(string)explode(",",GetModuleMultiToOneForLOadListColumns($field->module,$xml->originmodule->originname))[1],
							'secmoduleoptionGroup'=>"undefined",
						];
						array_push($MyArray,$ArrayRelated);
					}

					foreach ($xml->popup->columns->field as $popupi) {

						$Arraypopup=[
							
							'DefaultText'=>(string)$popupi->label,
							'DefaultValueFirstModuleField' =>(string)$popupi->label,
							'DefaultValueFirstModuleFieldoptionGroup'=>"",
							'FirstModule' =>(string) $field->module,
							'FirstModuleoptionGroup' =>"undefined",
							'Firstfield'=>(string)explode(",",Get_Modul_fields_check_from_load(get_The_history($QueryHistory,"firstmodule"),$popupi->name))[0],
							'FirstfieldID'=>(string)$xml->popup->linkfield,
							'FirstfieldIDoptionGroup'=>"",
							'FirstfieldoptionGroup'=>(string)get_The_history($QueryHistory,"firstmoduletext"),
							'JsonType'=>"Popup Screen",
							
						];
						array_push($MyArray,$Arraypopup);
					}
					array_push($Allhistoryload,$MyArray);
				}

				// print_r($Allhistoryload);
				// exit();
				$data="MapGenerator,SaveListColumns";
				$dataid="ListData,MapName";
				$savehistory="true";
				
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);

				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FmoduleID",$FmoduleID);
				$smarty->assign("SmoduleID",$SmoduleID);

				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("SecondModulerelation",$SecondModulerelation);

				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings));

				$smarty->assign("FirstModuleFields",$FirstModuleFields);

				$smarty->assign("PopupJS",$Allhistoryload);

				$smarty->assign("SecondModuleFields",$SecondModuleFields);

				$output = $smarty->fetch('modules/MapGenerator/ListColumns.tpl');
				echo $output;

				
			} elseif (!empty($MapID)) {
				//TODO:: execute when not find the QueryID
				
				$xml= new SimpleXMLElement(get_form_Map($MapID)); 
				$FirstModuleSelected=Get_First_Moduls(get_form_Map($MapID,"targetname"));
				$QueryHistory=get_form_Map($MapID,"mvqueryid");
				$SecondModulerelation=GetModulRelOneTomulti(get_form_Map($MapID,"targetname"),$xml->originmodule->originname);

				$FirstModuleFields=getModFields(get_form_Map($MapID,"targetname"));

				$SecondModuleFields=getModFields($xml->originmodule->originname);

				$MapName=get_form_Map($MapID,"mapname");

				$HistoryMap=$QueryHistory.",".$MapID;

				$MyArray=array();

				$SmoduleID=(string) $xml->relatedlists[0]->relatedlist->linkfield;
				$FmoduleID=(string)  $xml->popup->linkfield;

				foreach($xml->relatedlists->relatedlist as $field)
				{
					$ArrayRelated=[
						
						'DefaultText'=>(string)$field->columns->field->label,
						'DefaultValue' =>(string)$field->columns->field->label,
						'DefaultValueoptionGroup'=>"",
						'FirstModule' =>(string) $field->module,
						'FirstModuleoptionGroup' =>"undefined",
						'FirstfieldID'=>(string)$xml->popup->linkfield,
						'FirstfieldIDoptionGroup'=>"",
						'JsonType'=>"Related",
						'SecondField'=>(string)explode(",",Get_Modul_fields_check_from_load($xml->originmodule->originname,$field->columns->field->name))[0],
						'SecondFieldoptionGroup'=>(string)$xml->originmodule->originname,
						'SecondfieldID'=>(string)$field->linkfield,
						'SecondfieldIDoptionGroup'=>"",
						'secmodule'=>(string)explode(",",GetModuleMultiToOneForLOadListColumns($field->module,$xml->originmodule->originname))[0],
						'secmoduleoptionGroup'=>"undefined",
					];
					array_push($MyArray,$ArrayRelated);
				}

				foreach ($xml->popup->columns->field as $popupi) {

					$Arraypopup=[
						
						'DefaultText'=>(string)$popupi->label,
						'DefaultValueFirstModuleField' =>(string)$popupi->label,
						'DefaultValueFirstModuleFieldoptionGroup'=>"",
						'FirstModule' =>(string) $field->module,
						'FirstModuleoptionGroup' =>"undefined",
						'Firstfield'=>(string)explode(",",Get_Modul_fields_check_from_load(get_form_Map($MapID,"targetname"),$popupi->name))[0],
						'FirstfieldID'=>(string)$xml->popup->linkfield,
						'FirstfieldIDoptionGroup'=>"",
						'FirstfieldoptionGroup'=>(string)get_The_history($QueryHistory,"firstmoduletext"),
						'JsonType'=>"Popup",
						
					];
					array_push($MyArray,$Arraypopup);
				}

				$data="MapGenerator,SaveListColumns";
				$dataid="ListData,MapName";
				$savehistory="true";
				
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);

				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FmoduleID",$FmoduleID);
				$smarty->assign("SmoduleID",$SmoduleID);


				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("SecondModulerelation",$SecondModulerelation);

				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings));

				$smarty->assign("FirstModuleFields",$FirstModuleFields);

				$smarty->assign("PopupJS",$MyArray);

				$smarty->assign("SecondModuleFields",$SecondModuleFields);

				$output = $smarty->fetch('modules/MapGenerator/ListColumns.tpl');
				echo $output;
				
			}else{
				throw new Exception("Missing the MApID also The QueryHIstory", 1);
			}
			
			
		} catch (Exception $ex) {
			$log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception ".$ex);
			echo TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex;
		}
	}

	/**
	 * function to continue with master detail map
	 *
	 * @param      [type]     $QueryHistory  the ID of Query History
	 * @param      [type]     $MapID         The Id of MAp
	 *
	 * @throws     Exception  (description)
	 */
	function Master_detail($QueryHistory,$MapID)
	{
		include_once('modfields.php');
		include_once('All_functions.php');
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		try {
			
			if (!empty($QueryHistory)) {
				//TODO: if have query history
				
				$FirstModuleSelected=Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
				$showfields='<option value="" >(Select a module)</option>';
				$module=get_The_history($QueryHistory,"firstmodule");
				if (!empty(MappingRelationFields($module))) {
					foreach (MappingRelationFields($module) as $value) {
						if ($value!==$module) {
							$showfields.='<option value="'.$value.'">'.$value.'</option>'; 
						}        
					}
					
				} else {
				echo "<option value=''>None</option>";
				}
				$SecondModulerelation=$showfields;

				$FirstModuleFields=getModFields(get_The_history($QueryHistory,"firstmodule"));

				$SecondModuleFields=getModFields(get_The_history($QueryHistory,"secondmodule"));

				$MapName=get_form_Map($MapID,"mapname");

				$HistoryMap=$QueryHistory.",".$MapID;
				$xml=new SimpleXMLElement(get_The_history($QueryHistory,"query"));
				$FmoduleID=(string) $xml->linkfields->targetfield;
				$SmoduleID=(string) $xml->linkfields->originfield;


				//all history 
				$Allhistory=get_All_History($QueryHistory);

				$Alldatas=array();

				foreach ($Allhistory as $value) {
					
						$MyArray=array();
						$xml=new SimpleXMLElement($value['query']); 
						$nrindex=0;
						foreach($xml->detailview->fields->field as $field)
						{
							$araymy=[
								
								'DefaultText'=>"Edmondi Default",
								'FirstModule' =>(string)explode("#", Get_First_Moduls_TextVal($xml->targetmodule[0]))[0],
								'FirstModuleoptionGroup'=>"udentifined",
								'Firstfield' =>(string) explode(",",Get_Modul_fields_check_from_load($xml->targetmodule[0],$field->fieldname))[0],
								'FirstfieldID' =>(string) $xml->linkfields[0]->targetfield,
								'FirstfieldIDoptionGroup'=>"",
								'FirstfieldText'=>(string)explode(",",  Get_Modul_fields_check_from_load($xml->targetmodule[0],$field->fieldname))[1],
								'FirstfieldoptionGroup'=>(string)$xml->targetmodule,
								'JsonType'=>"Default",
								'SecondfieldID'=>(string)$xml->linkfields->originfield,

								'editablechk'=>(string) $field->editable,
								'editablechkoptionGroup'=>"",

								'hiddenchk'=>(string) $field->hidden,
								'hiddenchkoptionGroup'=>"",

								'mandatorychk'=>(string)$field->mandatory,

								'secmodule' =>explode("#", Get_First_Moduls_TextVal($xml->originmodule))[0],
								'secmoduleoptionGroup'=>"udentifined",

								'sortt6ablechk'=>((string)$xml->sortfield===(string)$field->fieldname)?1:0,
								'sortt6ablechkoptionGroup'=>"",
								

							];

							array_push($MyArray,$araymy);
						}
						array_push($Alldatas,$MyArray);
					}



				// value for Save As 
				$data="MapGenerator,SaveMasterDetail";
				$dataid="ListData,MapName";
				$savehistory="true";
				
				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);

				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FmoduleID",$FmoduleID);
				$smarty->assign("SmoduleID",$SmoduleID);

				$NameOFMap=$MapName;
				$smarty->assign("NameOFMap",$NameOFMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("SecondModulerelation",$SecondModulerelation);

				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings));

				$smarty->assign("FirstModuleFields",$FirstModuleFields);

				$smarty->assign("PopupJS",$Alldatas);

				$smarty->assign("SecondModuleFields",$SecondModuleFields);

				$output = $smarty->fetch('modules/MapGenerator/MasterDetail.tpl');
				echo $output;



			}elseif (!empty($MapID)) {
				//TODO: if exist MAp id 
				
				$xml=new SimpleXMLElement(get_form_Map($MapID)); 
				$FirstModuleSelected=Get_First_Moduls((string) $xml->targetmodule[0]);
				$SecondModulerelation=GetAllrelation1TOManyMaps(get_The_history($QueryHistory,"firstmodule"),get_The_history($QueryHistory,"secondmodule"));
				$FirstModuleFields=getModFields((string)$xml->targetmodule[0]);
				$SecondModuleFields=getModFields((string)$xml->originmodule[0]);
				$MapName=get_form_Map($MapID,"mapname");
				$HistoryMap=get_form_Map($MapID,"mvqueryid").",".$MapID;

				// value for Save As 
				$data="MapGenerator,SaveMasterDetail";
				$dataid="ListData,MapName";
				$savehistory="true";
				
				$MyArray=array();
				// $xml=new SimpleXMLElement(get_The_history($QueryHistory,"query")); 

				$FmoduleID=(string) $xml->linkfields->targetfield;
				$SmoduleID=(string) $xml->linkfields->originfield;

				$nrindex=0;
				foreach($xml->detailview->fields->field as $field)
				{
					$araymy=[
						
						'DefaultText'=>"Edmondi Default",
						'FirstModule' =>(string)explode("#", Get_First_Moduls_TextVal($xml->targetmodule[0]))[0],
						'FirstModuleoptionGroup'=>"udentifined",
						'Firstfield' =>(string) explode(",",Get_Modul_fields_check_from_load($xml->targetmodule[0],$field->fieldname))[0],
						'FirstfieldID' =>(string) $xml->linkfields[0]->targetfield,
						'FirstfieldIDoptionGroup'=>"",
						'Firstfield_Text'=>(string)explode(",",  Get_Modul_fields_check_from_load($xml->targetmodule[0],$field->fieldname))[1],
						'FirstfieldoptionGroup'=>(string)$xml->targetmodule,
						'JsonType'=>"Default",
						'SecondfieldID'=>(string)$xml->linkfields->originfield,

						'editablechk'=>(string) $field->editable,
						'editablechkoptionGroup'=>"",

						'hiddenchk'=>(string) $field->editable,
						'hiddenchkoptionGroup'=>"",

						'mandatorychk'=>(string)$field->mandatory,

						'secmodule' =>(string)explode("#",GetModulRelOneTomultiTextVal($xml->targetmodule,$xml->originmodule))[0],
						'secmoduleoptionGroup'=>"udentifined",

						'sortt6ablechk'=>((string)$xml->sortfield===(string)$field->fieldname)?1:0,
						'sortt6ablechkoptionGroup'=>"",
						

					];

					array_push($MyArray,$araymy);
				}

				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);

				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FmoduleID",$FmoduleID);
				$smarty->assign("SmoduleID",$SmoduleID);


				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("SecondModulerelation",$SecondModulerelation);

				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings));

				$smarty->assign("FirstModuleFields",$FirstModuleFields);

				$smarty->assign("PopupJS",$MyArray);

				$smarty->assign("SecondModuleFields",$SecondModuleFields);

				$output = $smarty->fetch('modules/MapGenerator/MasterDetail.tpl');
				echo $output;



				
			}else
			{
				throw new Exception("Missing the MApID also The QueryHIstory", 1);
				
			}

		} catch (Exception $ex) {
			$log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception ".$ex);
			echo $ex;
		}
	}



	/**
	 * function to load the map type Mapping
	 *
	 * @param      [type]  $QueryHistory  Type string is the id of query
	 * @param      [type]  $MapID         string is the MapId if missing the QueryId
	 * @return     The   template loaded
	 */
	function Mapping_View($QueryHistory,$MapID)
	{	
		include_once("All_functions.php");
		include_once('modfields.php');	
		global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $root_directory, $current_user,$log;
		$theme_path = "themes/" . $theme . "/";
		$image_path = $theme_path . "images/";
		try {

			if (!empty($QueryHistory)) {
				
				$FirstModuleSelected="<option values=''>Select module</option>". Get_First_Moduls(get_The_history($QueryHistory,"firstmodule"));
				$SecondModulerelation=GetAllrelation1TOManyMaps(get_The_history($QueryHistory,"firstmodule"),get_The_history($QueryHistory,"secondmodule"));

				$FirstModuleFields="<option values=''>Select field</option>".getModFields(get_The_history($QueryHistory,"firstmodule"));
				
				$showfields="<option values=''>Select field</option>";
				foreach (MappingRelationFields(get_The_history($QueryHistory,"secondmodule")) as $value) {
					$showfields.=getModFields($value);          
				}
				$SecondModuleFields=$showfields;
				$MapName=get_form_Map($MapID,"mapname");
				$HistoryMap=$QueryHistory.",".$MapID;
				$NameOFMap=get_form_Map($MapID,"mapname");
				// value for Save As 
				$data="MapGenerator,SaveTypeMaps";
				$dataid="ListData,MapName";
				$savehistory="true";
				
				$popupArray=array();
				//all history 
				$Allhistory=get_All_History($QueryHistory);

				$Alldatas=array();
				foreach ($Allhistory as $value) {
					$MyArray=array();
					$xml=new SimpleXMLElement($value['query']); 
					$nrindex=0;
					foreach($xml->fields->field as $field)
					{
						$araymy=[
							'FirstFieldtxt' =>explode(",",CheckAllFirstForAllModules($field->fieldname))[1],
							'FirstFieldval' => explode(",",CheckAllFirstForAllModules($field->fieldname))[0],

							'FirstModuleval' =>explode("#", Get_First_Moduls_TextVal($xml->targetmodule[0]->targetname))[0],

							'FirstModuletxt' =>explode("#", Get_First_Moduls_TextVal($xml->targetmodule[0]->targetname))[1],

							'SecondModuletxt' =>explode("#",Get_First_Moduls_TextVal($xml->originmodule[0]->originname))[1],

							'SecondModuleval' =>explode("#",Get_First_Moduls_TextVal($xml->originmodule[0]->originname))[1],

							'idJSON'=>$nrindex++
							

						];
						
						//LogFileSimple($secondfieldval);
						if (!empty(CheckAllFirstForAllModules($field->Orgfields->Relfield->RelfieldName))) {
							$araymy["SecondFieldval"]=explode(",",CheckAllFirstForAllModules($field->Orgfields->Relfield->RelfieldName))[0];
							$araymy["SecondFieldtext"]=explode(",",CheckAllFirstForAllModules($field->Orgfields->Relfield->RelfieldName))[1];
							$araymy["SecondFieldOptionGrup"]=$field->Orgfields->Relfield->RelModule;
						}else if (!empty(CheckAllFirstForAllModules($field->Orgfields->Orgfield->OrgfieldName))) {
							$araymy["SecondFieldval"]=explode(",",CheckAllFirstForAllModules($field->Orgfields->Orgfield->OrgfieldName))[0];
							$araymy["SecondFieldtext"]=explode(",",CheckAllFirstForAllModules($field->Orgfields->Orgfield->OrgfieldName))[1];
							$araymy["SecondFieldOptionGrup"]=$xml->originmodule[0]->originname;
						}else{
							$araymy["SecondFieldval"]=$field->value;
							$araymy["SecondFieldtext"]= $field->value."  (default value)";
							$araymy["SecondFieldOptionGrup"]=$xml->originmodule[0]->originname;
						}	

						array_push($MyArray,$araymy);
					}
					array_push($Alldatas,$MyArray);
				}


				

				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);

				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				$smarty->assign("SecondModulerelation",$SecondModulerelation);

				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings));

				$smarty->assign("FirstModuleFields",$FirstModuleFields);

				$smarty->assign("PopupJson",$Alldatas);
				$smarty->assign("NameOFMap",$NameOFMap);
				$smarty->assign("SecondModuleFields",$SecondModuleFields);

				$output = $smarty->fetch('modules/MapGenerator/MappingView.tpl');
				echo $output;
				exit();

			}elseif(!empty($MapID)){

				$xml=new SimpleXMLElement(get_form_Map($MapID)); 
				$FirstModuleSelected=Get_First_Moduls( $xml->targetmodule[0]->targetname);
				$SecondModulerelation=GetModulRelOneTomulti($xml->targetmodule[0]->targetname ,$xml->originmodule[0]->originname);
				$FirstModuleFields=getModFields($xml->targetmodule[0]->targetname);
				$SecondModuleFields=getModFields($xml->originmodule[0]->originname);
				$MapName=get_form_Map($MapID,"mapname");
				$HistoryMap=get_form_Map($MapID,"mvqueryid").",".$MapID;

				// value for Save As 
				$data="MapGenerator,SaveTypeMaps";
				$dataid="ListData,MapName";
				$savehistory="true";

				$popupArray=array();
				
				$MyArray=array();
				$xml=new SimpleXMLElement(get_form_Map($MapID)); 
				$nrindex=0;
				foreach($xml->fields->field as $field)
				{
					$araymy=[
						'FirstFieldtxt' =>explode(",",  Get_Modul_fields_check_from_load($xml->targetmodule[0]->targetname,$field->fieldname))[1],
						'FirstFieldval' => explode(",",Get_Modul_fields_check_from_load($xml->targetmodule[0]->targetname,$field->fieldname))[0],

						'FirstModuleval' =>explode("#", Get_First_Moduls_TextVal($xml->targetmodule[0]->targetname))[0],

						'FirstModuletxt' =>explode("#", Get_First_Moduls_TextVal($xml->targetmodule[0]->targetname))[1],

						'SecondModuletxt' =>explode("#",GetModulRelOneTomultiTextVal($xml->targetmodule[0]->targetname,$xml->originmodule[0]->originname))[1],

						'SecondModuleval' =>explode("#",GetModulRelOneTomultiTextVal($xml->targetmodule[0]->targetname,$xml->originmodule[0]->originname))[1],

						'SecondFieldval' =>explode("#",GetModulRelOneTomultiTextVal($xml->targetmodule[0]->targetname,$xml->originmodule[0]->originname))[0],
						'idJSON'=>$nrindex++,
						'SecondFieldtext' => explode(",",Get_Modul_fields_check_from_load($field->Orgfields->Relfield->RelModule,$field->Orgfields->Relfield->RelfieldName))[1],

						'SecondFieldval' => explode(",",Get_Modul_fields_check_from_load($field->Orgfields->Relfield->RelModule,$field->Orgfields->Relfield->RelfieldName))[0],
						'SecondFieldOptionGrup'=>explode("#", Get_First_Moduls_TextVal($xml->targetmodule[0]->targetname))[0]

					];

					array_push($MyArray,$araymy);
				}


				$smarty = new vtigerCRM_Smarty();
				$smarty->assign("MOD", $mod_strings);
				$smarty->assign("APP", $app_strings);
				
				$smarty->assign("MapName", $MapName);

				$smarty->assign("HistoryMap",$HistoryMap);

				$smarty->assign("FirstModuleSelected",$FirstModuleSelected);
				// $smarty->assign("SecondModulerelation",$SecondModulerelation);

				$smarty->assign("PopupJson",$MyArray);

				//put the smarty modal
				$smarty->assign("Modali",put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings));

				// $smarty->assign("FirstModuleFields",$FirstModuleFields);
				// $smarty->assign("SecondModuleFields",$SecondModuleFields);

				$output1 = $smarty->fetch('modules/MapGenerator/MappingView.tpl');
				echo $output1;
			}

			
		} catch (Exception $ex) {
			$log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception ".$ex);
			LogFile($ex);
			// echo "Missing the Id of the Map and also the Id of query history ";
			showError("Something was wrong","Missing the Id of the Map and also the Id of query history");
		}
	}

#end Region


#Region Alll helpet functions

	function putThecondition($QueryHistory,$generatetQuery,$sendarrays=[])
	{
		///
			// require_once('Smarty_setup.php');
			// global $app_strings, $mod_strings, $current_language, $currentModule, $theme, $adb, $root_directory, $current_user;
			// $theme_path = "themes/" . $theme . "/";
			// $image_path = $theme_path . "images/";
			// $smarty = new vtigerCRM_Smarty();

			$sendarray = array();
			for ($j = 0; $j < count($sendarrays); $j++)
			{
				$expdies = explode(":", $sendarrays[$j]);
				$sendarray[] = array(
					'Values' =>explode(",",Get_Modul_fields_check_from_load(get_The_history($QueryHistory,"firstmodule"),(string)$expdies[2]))[0] ,
					'Texti' => explode(",",Get_Modul_fields_check_from_load(get_The_history($QueryHistory,"firstmodule"),(string)$expdies[2]))[1],
				);
			}
			return $sendarray;
	}


	/**
	 * [put_the_modal_SaveAs description] this function is to insert the modal for save as in every Load map
	 * @param  [type] $data         String Are the name of the modul and the file when you save the Map type Mapping 
	 * @param  [type] $dataid       String the id (Html elements ) to take the values 
	 * @param  [type] $savehistory  String Flag if you want the history or not 
	 * @param  [type] $mod_strings  For Translate 
	 * @param  [type] $app_strings  
	 * @return [type]                return string which contains the modal 
	 */
	function put_the_modal_SaveAs($data,$dataid,$savehistory,$mod_strings,$app_strings,$saveasfunction="")
	{	
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign("MOD", $mod_strings);
		$smarty->assign("APP", $app_strings);
		$smarty->assign("Datas", $data);
		$smarty->assign("dataid", $dataid);
		$smarty->assign("savehistory", $savehistory);  
		$smarty->assign("anotherfunction", $saveasfunction);    
		$output = $smarty->fetch('modules/MapGenerator/Modal.tpl');
		return $output;

	}



	/**
	 * This function is to get all moduls and if also to check if someone from those is equels vith the values 
	 * @param string $value  is a param type string to check the the list of moduls
	 */
	function Get_First_Moduls($value="")
	{
		global $adb, $root_directory, $log;

		$query = "SELECT * from vtiger_tab where isentitytype=1 and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier' and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and presence=0";

		$result = $adb->query($query);
		$num_rows = $adb->num_rows($result);
		if ($num_rows != 0) {
		//echo "if num rows eshte e madhe se 0 ";
		for ($i = 1; $i <= $num_rows; $i++) {
			//echo "brenda ciklit for ".$i;
			$modul1 = $adb->query_result($result, $i - 1, 'name');
			if (strlen($value) != 0 && $value == $modul1) {
				$a .= '<option selected value="' . $modul1 . '">' . str_replace("'", "", getTranslatedString($modul1)) . '</option>';
				// echo "nese plotesohet kushti firstmodulexml";
			} else {
				$a .= '<option  value="' . $modul1 . '">' . str_replace("'", "", getTranslatedString($modul1)) . '</option>';
				///echo "nese nuk  plotesohet kushti firstmodulexml";
			}
		}
		}
		return $a;
	}


	function Get_First_Moduls_TextVal($value="")
	{
		global $adb, $root_directory, $log;

		$query = "SELECT * from vtiger_tab where isentitytype=1 and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier' and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and presence=0";

		$result = $adb->query($query);
		$num_rows = $adb->num_rows($result);
		if ($num_rows != 0) {
		//echo "if num rows eshte e madhe se 0 ";
		for ($i = 1; $i <= $num_rows; $i++) {
			//echo "brenda ciklit for ".$i;
			$modul1 = $adb->query_result($result, $i - 1, 'name');
			if (strlen($value) != 0 && $value == $modul1) {
				$a =$modul1 . '#' . str_replace("'", "", getTranslatedString($modul1));
				// echo "nese plotesohet kushti firstmodulexml";
			}
		}
		}
		return $a;
	}




	/**
	 * function to select the query fom mapgeneration_mvqueryhistory
	 * @param  string $Id_Encrypt  the id to  filter by this id 
	 * @return the query of mapgenertion_mvhistory
	 */
	function get_The_history($Id_Encrypt="",$field_take="query",$sequence='')
	{
		global $adb,$root_directory, $log;

		if(empty($Id_Encrypt))
		{
			throw new Exception(TypeOFErrors::INFOLG." The ID for history is Emtpy", 1);
		}

		try {

			$q="SELECT * FROM ".TypeOFErrors::Tabele_name." Where id='$Id_Encrypt'";
			if (!empty($sequence)) {
				$q.="  AND sequence='$sequence' ";
			}else
			{
				$q.=" AND active=1 ";
			}

			$result = $adb->query($q);
			$num_rows = $adb->num_rows($result);
			if (empty($field_take)) {
				throw new Exception(TypeOFErrors::ERRORLG."r Missing the Filed you wat to take", 1);
			}

			if ($num_rows>0) {
				$Resulti = $adb->query_result($result,0, $field_take);

				if (!empty($Resulti)) {
					return $Resulti;
				} else {
					throw new Exception(TypeOFErrors::ERRORLG." Something was wrong RESULT IS EMPTY", 1);
				}
			} else {
				throw new Exception(TypeOFErrors::ERRORLG."Not exist daata with this ID="+$Id_Encrypt,1);
			}
		} catch (Exception $ex) {
			$log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception ".$ex);
			return "";
		}
	}


	/**
	 * Gets all history.function to get all data from ".TypeOFErrors::Tabele_name."
	 * table
	 *
	 * @param      integer|string  $Id_Encrypt  The Id of ".TypeOFErrors::Tabele_name."
	 * @param      string          $field_take  The field you want to take (Default
	 *                                          is query)
	 *
	 * @throws     Exception       (description)
	 *
	 * @return     array          All history.
	 */
	function get_All_History($Id_Encrypt="",$field_take="query")
	{
		global $adb,$root_directory, $log;
		$datas=array();
		if(empty($Id_Encrypt))
		{
			throw new Exception(TypeOFErrors::INFOLG." The ID for history is Emtpy", 1);
		}

		try {

			$q=" SELECT * FROM `".TypeOFErrors::Tabele_name."` WHERE id='$Id_Encrypt' ORDER BY `".TypeOFErrors::Tabele_name."`.`sequence` ASC ";

			$result = $adb->query($q);
			$num_rows = $adb->num_rows($result);
			if (empty($field_take)) {
				throw new Exception(TypeOFErrors::ERRORLG." Missing the Filed you wnat to take", 1);
			}

			if ($num_rows>0) {
				for($i=1 ; $i <= $num_rows; $i++)
				{
					array_push($datas,[
						"$field_take"=>$adb->query_result($result,$i-1, $field_take),
						"sequence"=>$adb->query_result($result,$i-1,"sequence"),
						"FirstModule"=>$adb->query_result($result,$i-1,"firstmodule"),
						"Secondmodule"=>$adb->query_result($result,$i-1,"secondmodule"),
						"active"=>$adb->query_result($result,$i-1,"active"),
						"labels"=>$adb->query_result($result,$i-1,"labels")
					]);
				}

				if (!empty($datas)) {
					return $datas;
				} else {
					throw new Exception(TypeOFErrors::ERRORLG." Something was wrong RESULT IS EMPTY", 1);
				}
			} else {
				throw new Exception(TypeOFErrors::ERRORLG."Not exist daata with this ID="+$Id_Encrypt,1);
			}
		} catch (Exception $ex) {
			$log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception ".$ex);
			return $ex;
		}
	}


	/**
	 * get the value from cbmap 
	 * @param  string  $MapID      The id of map 
	 * @param  string $field_take  what field you want to take from cbmap 
	 * @return string  return the values of the fields from cbmap
	 */
	function get_form_Map($MapID,$field_take='content')
	{
		global $adb,$root_directory, $log;

		if(empty($MapID))
		{
			throw new Exception(TypeOFErrors::INFOLG." The ID for Map is Emtpy", 1);
		}

		try {

			$q="SELECT cb.*,cr.* FROM vtiger_cbmap cb JOIN vtiger_crmentity cr ON cb.cbmapid=cr.crmid WHERE cr.deleted=0 and cb.cbmapid='$MapID'";

			$result = $adb->query($q);
			$num_rows = $adb->num_rows($result);
			if (empty($field_take)) {
				throw new Exception(TypeOFErrors::ERRORLG." Missing the Filed you wnat to take", 1);
			}

			if ($num_rows>0) {
				$Resulti = $adb->query_result($result,0, $field_take);

				if (!empty($Resulti)) {
					return $Resulti;
				} else {
					throw new Exception(TypeOFErrors::ERRORLG." Something was wrong RESULT IS EMPTY", 1);
				}
			} else {
				throw new Exception(TypeOFErrors::ERRORLG."Not exist Map with this ID=".$MapID,1);
			}
		} catch (Exception $ex) {
			$log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception ".$ex);
			return "";
		}
	}



	/**
	 * get the value from cbmap 
	 * @param  string  $QueryID      The id of  mvquery from map
	 * @param  string $field_take  what field you want to take from cbmap 
	 * @return string  return the values of the fields from cbmap
	 */
	function get_form_MapQueryID($Queryid,$field_take='content')
	{
		global $adb,$root_directory, $log;

		if(empty($Queryid))
		{
			throw new Exception(TypeOFErrors::INFOLG." The ID for Map is Emtpy", 1);
		}

		try {

			$q="SELECT cb.*,cr.* FROM vtiger_cbmap cb JOIN vtiger_crmentity cr ON cb.cbmapid=cr.crmid WHERE cr.deleted=0 and cb.mvqueryid='$Queryid'";

			$result = $adb->query($q);
			$num_rows = $adb->num_rows($result);
			if (empty($field_take)) {
				throw new Exception(TypeOFErrors::ERRORLG." Missing the Filed you wnat to take", 1);
			}

			if ($num_rows>0) {
				$Resulti = $adb->query_result($result,0, $field_take);

				if (!empty($Resulti)) {
					return $Resulti;
				} else {
					throw new Exception(TypeOFErrors::ERRORLG." Something was wrong RESULT IS EMPTY", 1);
				}
			} else {
				throw new Exception(TypeOFErrors::ERRORLG."Not exist Map with this ID=".$Queryid,1);
			}
		} catch (Exception $ex) {
			$log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception ".$ex);
			return "";
		}
	}



	/**
	 * Function to take the fields from modul 
	 * @param  [type] $module Madul name
	 * @param  [type] $dbname Null
	 *  @param  [type] $Checkname check the field with the filed from map
	 * @return [type]         return the filed of modul 
	 */
	function Get_Modul_fields_check_from_load($module,$checkname,$dbname)
	{
		// echo $module." ## ".$checkname;
		// exit();
		global $log;
		$log->debug(TypeOFErrors::INFOLG."Entering getAdvSearchfields(".$module.") method ...");
		global $adb;
		global $current_user;
		global $mod_strings,$app_strings;
		$OPTION_SET.= '';
		$tabid = getTabid($module,$dbname);
		if($tabid==9)
			$tabid="9,16";


		$sql = "select * from  vtiger_field ";
		$sql.= " where vtiger_field.tabid in(?) and";
		$sql.= " vtiger_field.displaytype in (1,2,3) and vtiger_field.presence in (0,2)";
		if($tabid == 13 || $tabid == 15)
		{
			$sql.= " and vtiger_field.fieldlabel != 'Add Comment'";
		}
		if($tabid == 14)
		{
			$sql.= " and vtiger_field.fieldlabel != 'Product Image'";
		}
		if($tabid == 9 || $tabid==16)
		{
			$sql.= " and vtiger_field.fieldname not in('notime','duration_minutes','duration_hours')";
		}
		if($tabid == 4)
		{
			$sql.= " and vtiger_field.fieldlabel != 'Contact Image'";
		}
		if($tabid == 13 || $tabid == 10)
		{
			$sql.= " and vtiger_field.fieldlabel != 'Attachment'";
		}
		$sql.= " group by vtiger_field.fieldlabel order by block,sequence";

		$params = array($tabid);


		$result = $adb->pquery($sql, $params);
		$noofrows = $adb->num_rows($result);
		$block = '';
		$select_flag = '';
		//echo "edmondi 2";
		for($i=0; $i<$noofrows; $i++)
		{
			$fieldtablename = $adb->query_result($result,$i,"tablename");
			$fieldcolname = $adb->query_result($result,$i,"columnname");
			$fieldname = $adb->query_result($result,$i,"fieldname");
			$block = $adb->query_result($result,$i,"block");
			$fieldtype = $adb->query_result($result,$i,"typeofdata");
			$fieldtype = explode("~",$fieldtype);
			$fieldtypeofdata = $fieldtype[0];
			if($fieldcolname == 'account_id' || $fieldcolname == 'accountid' || $fieldcolname == 'product_id' || $fieldcolname == 'vendor_id' || $fieldcolname == 'contact_id' || $fieldcolname == 'contactid' || $fieldcolname == 'vendorid' || $fieldcolname == 'potentialid' || $fieldcolname == 'salesorderid' || $fieldcolname == 'quoteid' || $fieldcolname == 'parentid' || $fieldcolname == "recurringtype" || $fieldcolname == "campaignid" || $fieldcolname == "inventorymanager" ||  $fieldcolname == "currency_id")
				$fieldtypeofdata = "V";
			if($fieldcolname == "discontinued" || $fieldcolname == "active")
				$fieldtypeofdata = "C";
			$fieldlabel = $mod_strings[$adb->query_result($result,$i,"fieldlabel")];

			// Added to display customfield label in search options
			if($fieldlabel == "")
				$fieldlabel = $adb->query_result($result,$i,"fieldlabel");

			if($fieldlabel == "Related To")
			{
				$fieldlabel = "Related to";
			}
			if($fieldlabel == "Start Date & Time")
			{
				$fieldlabel = "Start Date";
				if($module == 'Activities' && $block == 19)
					$module_columnlist['vtiger_activity:time_start::Activities_Start Time:I'] = 'Start Time';

			}
			//$fieldlabel1 = str_replace(" ","_",$fieldlabel); // Is not used anywhere
			//Check added to search the lists by Inventory manager
			if($fieldtablename == 'vtiger_quotes' && $fieldcolname == 'inventorymanager')
			{
				$fieldtablename = 'vtiger_usersQuotes';
				$fieldcolname = 'user_name';
			}
			if($fieldtablename == 'vtiger_contactdetails' && $fieldcolname == 'reportsto')
			{
				$fieldtablename = 'vtiger_contactdetails2';
				$fieldcolname = 'lastname';
			}
			if($fieldtablename == 'vtiger_notes' && $fieldcolname == 'folderid'){
				$fieldtablename = 'vtiger_attachmentsfolder';
				$fieldcolname = 'foldername';
			}
			if($fieldlabel != 'Related to')
			{
				if ($i==0)
					$select_flag = "";

				$mod_fieldlabel = $mod_strings[$fieldlabel];
				if($mod_fieldlabel =="") $mod_fieldlabel = $fieldlabel;

				if($fieldlabel == "Product Code") {

					$OPTION_SET .= $fieldtablename . ":" . $fieldcolname . ":" . $fieldname . "::" . $fieldtypeofdata . "" . $select_flag.",".$mod_fieldlabel."#";


				}
				if($fieldlabel == "Reports To"){

					$OPTION_SET .=$fieldtablename.":".$fieldcolname.":".$fieldname."::".$fieldtypeofdata."".$select_flag.",".$mod_fieldlabel."#";

				}

				elseif($fieldcolname == "contactid" || $fieldcolname == "contact_id")
				{

					$OPTION_SET .= "vtiger_contactdetails:lastname:".$fieldname."::".$fieldtypeofdata."".$select_flag.",".$app_strings['LBL_CONTACT_LAST_NAME']."#";
					$OPTION_SET .= "vtiger_contactdetails:firstname:".$fieldname."::".$fieldtypeofdata.",".$app_strings['LBL_CONTACT_FIRST_NAME']."#";



				}
				elseif($fieldcolname == "campaignid")
					$OPTION_SET .= "vtiger_campaign:campaignname:".$fieldname."::".$fieldtypeofdata."".$select_flag.",".$mod_fieldlabel."#";
				else
					$OPTION_SET .=$fieldtablename.":".$fieldcolname.":".$fieldname."::".$fieldtypeofdata." ".$select_flag.",".str_replace("'","`",$fieldlabel)."#";
			}
		}
		//Added to include Ticket ID in HelpDesk advance search
		if($module == 'HelpDesk')
		{
			$mod_fieldlabel = $mod_strings['Ticket ID'];
			if($mod_fieldlabel =="") $mod_fieldlabel = 'Ticket ID';

			$OPTION_SET .= "vtiger_crmentity:crmid:".$fieldname."::".$fieldtypeofdata.",".$mod_fieldlabel."#";
		}
		//Added to include activity type in activity advance search
		if($module == 'Activities')
		{
			$mod_fieldlabel = $mod_strings['Activity Type'];
			if($mod_fieldlabel =="") $mod_fieldlabel = 'Activity Type';

			$OPTION_SET .= "vtiger_activity.activitytype:".$fieldname."::".$fieldtypeofdata.",".$mod_fieldlabel."#";
		}
		$log->debug(TypeOFErrors::INFOLG."Exiting getAdvSearchfields method ...");

		$returncorrectdata=explode("#", $OPTION_SET);

		foreach ($returncorrectdata as $value) {
			$log->debug(TypeOFErrors::ERRORLG." Get_Modul_fields_check_from_load #### ".$value);
			if (contains(explode(",", $value)[0],trim($checkname)) == true) {
				return $value;
			}
		}

		return "";
	}


	/**
	 * function get all relation module only multi to one
	 * @param [type] $m         Moduli
	 * @param [type] $CheckNAme  The name of modul you want to check
	 */
	function GetModuleMultiToOneForLOadListColumns($m,$CheckNAme)
	{
		global $log, $mod_strings,$adb;
		$j = 0;
	  $query1 = "SELECT  module, columnname, fieldlabel from  vtiger_fieldmodulerel 
				join  vtiger_field on  vtiger_field.fieldid= vtiger_fieldmodulerel.fieldid
				where relmodule='$m' and module<>'Faq' and module<>'Emails' and module<>'Events' and module<>'Webmails' and module<>'SMSNotifier'
				and module<>'PBXManager' and module<>'Modcomments' and module<>'Calendar' 
				and relmodule in (select name from  vtiger_tab where presence=0) 
				and module in (select name from  vtiger_tab where presence=0)";


		$result1 = $adb->query($query1);
		$num_rows1 = $adb->num_rows($result1);
		if ($num_rows1 != 0) {
			for ($i = 1; $i <= $num_rows1; $i++) {
				$modul1 = $adb->query_result($result1, $i - 1, 'module');
				$column = $adb->query_result($result1, $i - 1, 'columnname');
				$fl = $adb->query_result($result1, $i - 1, 'fieldlabel');
				if (strlen($CheckNAme) != 0 && $CheckNAme == $modul1) {
					$a= $modul1 . '(many);' . $column . ',' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1));
				}
			}
		}
		if ($m == "Accounts") {
			$query2 = "SELECT name, columnname, fieldlabel from  vtiger_field
					join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where (uitype=73 or uitype=50
					or uitype=51 or uitype=68) and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events'
					and name<>'Webmails' and name<>'SMSNotifier' and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' 
					and  vtiger_tab.presence=0";

			$result2 = $adb->query($query2);
			$num_rows2 = $adb->num_rows($result2);
			if ($num_rows2 != 0) {
				for ($i = 1; $i <= $num_rows2; $i++) {
					$modul1 = $adb->query_result($result2, $i - 1, 'name');
					$column = $adb->query_result($result2, $i - 1, 'columnname');
					$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
					$mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
					if ($adb->num_rows($mo) != 0)
						if (strlen($CheckNAme) != 0 && $CheckNAme == $modul1) {
							$a=$modul1 . '(many); ' . $column . ',' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1));
						}                    
					}
			}
		}
		if ($m == "Contacts") {
			$query2 = "SELECT name, columnname, fieldlabel from  vtiger_field 
					join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid 
					where (uitype=57 or uitype=68) and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events' 
					and name<>'Webmails' and name<>'SMSNotifier'and name<>'PBXManager' and name<>'Modcomments' 
					and name<>'Calendar' and  vtiger_tab.presence=0";

			$result2 = $adb->query($query2);
			$num_rows2 = $adb->num_rows($result2);
			if ($num_rows2 != 0) {
				for ($i = 1; $i <= $num_rows2; $i++) {
					$modul1 = $adb->query_result($result2, $i - 1, 'name');
					$column = $adb->query_result($result2, $i - 1, 'columnname');
					$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
					$mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");
					if ($adb->num_rows($mo) != 0)
						if (strlen($CheckNAme) != 0 && $CheckNAme == $modul1) {
						$a =$modul1 . '(many); ' . $column . ',' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1));
						}
					}
			}
		}
		if ($m == "Produts") {
			$query2 = "SELECT columnname,name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where  uitype=59 and name<>'$m'
			and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
			and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

		";


			$result2 = $adb->query($query2);
			$num_rows2 = $adb->num_rows($result2);
			if ($num_rows2 != 0) {
				for ($i = 1; $i <= $num_rows2; $i++) {
					$modul1 = $adb->query_result($result2, $i - 1, 'name');
					$column = $adb->query_result($result2, $i - 1, 'columnname');
					$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
					$mo = $adb->query("select *  from vtiger_tab where name='$modul1' and presence=0");

					if ($adb->num_rows($mo) != 0)
						if (strlen($CheckNAme) != 0 && $CheckNAme == $modul1) {
							$a=  $modul1 . '(many); ' . $column . ',' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ')' . str_replace("'", "", getTranslatedString($fl, $modul1))."#";
						}
				}
			}
		}
		if ($m == "Campaigns") {
			$query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where  uitype=58 and name<>'$m'
			and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
			and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0
		";


			$result2 = $adb->query($query2);
			$num_rows2 = $adb->num_rows($result2);
			if ($num_rows2 != 0) {
				for ($i = 1; $i <= $num_rows2; $i++) {
					$modul1 = $adb->query_result($result2, $i - 1, 'name');
					$column = $adb->query_result($result2, $i - 1, 'columnname');
					$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
					$mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

					if ($adb->num_rows($mo) != 0)
						if (strlen($CheckNAme) != 0 && $CheckNAme == $modul1) {
							$a = $modul1 . '(many); ' . $column . ',' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1));
						}
				}
			}
		}
		if ($m == "Potentials") {
			$query2 = "SELECT columnname, name ,fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=76 and name<>'$m'
			and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
			and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and   vtiger_tab.presence=0

		";


			$result2 = $adb->query($query2);
			$num_rows2 = $adb->num_rows($result2);
			if ($num_rows2 != 0) {
				for ($i = 1; $i <= $num_rows2; $i++) {
					$modul1 = $adb->query_result($result2, $i - 1, 'name');
					$column = $adb->query_result($result2, $i - 1, 'columnname');
					$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
					$mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

					if ($adb->num_rows($mo) != 0)
						if (strlen($CheckNAme) != 0 && $CheckNAme == $modul1) {
							$a= $modul1 . '(many); ' . $column . ',' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1));
						}
				}
			}
		}
		if ($m == "Quotes") {
			$query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=78
			and name<>'$m' and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
			and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

		";

			$result2 = $adb->query($query2);
			$num_rows2 = $adb->num_rows($result2);
			if ($num_rows2 != 0) {
				for ($i = 1; $i <= $num_rows2; $i++) {
					$modul1 = $adb->query_result($result2, $i - 1, 'name');
					$column = $adb->query_result($result2, $i - 1, 'columnname');
					$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
					$mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

					if ($adb->num_rows($mo) != 0)
						if (strlen($CheckNAme) != 0 && $CheckNAme == $modul1) {
							$a = $modul1 . '(many); ' . $column . ',' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1));
						}

				}
			}
		}
		if ($m == "SalesOrder") {
			$query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=81 and uitype=75 and name<>'$m'
			and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
			and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

		";


			$result2 = $adb->query($query2);
			$num_rows2 = $adb->num_rows($result2);
			if ($num_rows2 != 0) {
				for ($i = 1; $i <= $num_rows2; $i++) {
					$modul1 = $adb->query_result($result2, $i - 1, 'name');
					$column = $adb->query_result($result2, $i - 1, 'columnname');
					$$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
					$mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

					if ($adb->num_rows($mo) != 0 && $CheckNAme==$modul1)
						$a =  $modul1 . '(many); ' . $column . ',' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . ') ' . str_replace("'", "", getTranslatedString($fl, $modul1)) ."#" ;


				}
			}
		}
		if ($m == "Vendors") {
			$query2 = "SELECT columnname, name, fieldlabel from  vtiger_field join  vtiger_tab on  vtiger_tab.tabid= vtiger_field.tabid where uitype=80 and name<>'$m'
			and name<>'Faq' and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
			and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and  vtiger_tab.presence=0

		";


			$result2 = $adb->query($query2);
			$num_rows2 = $adb->num_rows($result2);
			if ($num_rows2 != 0) {
				for ($i = 1; $i <= $num_rows2; $i++) {
					$modul1 = $adb->query_result($result2, $i - 1, 'name');
					$column = $adb->query_result($result2, $i - 1, 'columnname');
					$fl = $adb->query_result($result2, $i - 1, 'fieldlabel');
					$mo = $adb->query("select * from  vtiger_tab where name='$modul1' and presence=0");

					if ($adb->num_rows($mo) != 0) {
						if (strlen($CheckNAme) != 0 && $CheckNAme == $modul1) {
							$a =  $modul1 . '(many); ' . $column . ',' . str_replace("'", "", getTranslatedString($modul1)) . '(' . $mod_strings['many'] . '); ' . str_replace("'", "", getTranslatedString($fl, $modul1));
						}
					}
				}
			}
		}
		return $a;
	}


	/**
	 * function to find in string if exist a substring
	 * @param  [type]  $haystack      the string you want to check inside 
	 * @param  [type]  $needle        the substring 
	 * @param  boolean $caseSensitive  flag if you want to use the case sensinitive
	 * @return [type]                 type bool true or false
	 */
	function contains($haystack, $needle, $caseSensitive = false) {
		global $log;
		try
		{
			return $caseSensitive ?
			(strpos($haystack, $needle) === false ? false : true):
			(stripos($haystack, $needle) === false ? false : true);
		}catch(Exception $ex)
		{
			$log->debug(TypeOFErrors::ERRORLG."Something was wrong check the Exception ".$ex);
			return FALSE;
		}
	}


	/**
	 * Gets all modules.
	 *
	 * @return     string  All modules.
	 */
	function GetAllModules()
	{
		global $adb;
		$a='';
		$FirstmoduleXML = "";//"edmondi" . $_POST['MapID'];
		if (isset($_REQUEST['MapID'])) {
			$mapid = $_REQUEST['MapID'];
			$qid = $_REQUEST['queryid'];
			$sql="SELECT * from mvqueryhistory where id=? AND active=?";
			$result=$adb->pquery($sql, array($qid, 1));
			$FirstmoduleXML=$adb->query_result($result,0,'firstmodule');
			//$FirstmoduleXML = takeFirstMOduleFromXMLMap($mapid);
		// echo "brenda kushtit mapID ".$mapid;
		}

		if (isset($_REQUEST['secModule']) && isset($_REQUEST['firstModule'])) {
			$secModule = implode(',', array_keys(array_flip(explode(',', $_REQUEST['secModule']))));
			$modulesAllowed = '"' . $_REQUEST['firstModule'] . '","' . str_replace(',', '","', $secModule) . '"';
			$query = "SELECT * from vtiger_tab where isentitytype=1 and name<>'Faq' 
				and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
				and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and presence=0
				and name in ($modulesAllowed)";
		// echo "brenda ifit seltab etj ";
		} else {
			$query = "SELECT * from vtiger_tab where isentitytype=1 and name<>'Faq' 
				and name<>'Emails' and name<>'Events' and name<>'Webmails' and name<>'SMSNotifier'
				and name<>'PBXManager' and name<>'Modcomments' and name<>'Calendar' and presence=0";
			//echo "brenda elsit nese nuk plotesohet if ";
		}
		$result = $adb->query($query);
		$num_rows = $adb->num_rows($result);
		//echo "para ciklit fore  ";
		if ($num_rows != 0) {
			//echo "if num rows eshte e madhe se 0 ";
			for ($i = 1; $i <= $num_rows; $i++) {
				//echo "brenda ciklit for ".$i;
				$modul1 = $adb->query_result($result, $i - 1, 'name');

			$a .= $modul1 .'#';
					///echo "nese nuk  plotesohet kushti firstmodulexml";
			
			}
		}
		return $a;
	}


	/**
	 * functio to check every module and also for every modul check the fileds
	 *
	 * @param      <type>  $checkname  The checkname
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function CheckAllFirstForAllModules($checkname)
	{
		$FirstModuleSelected=explode("#",GetAllModules());
		$allfields='';
		foreach ($FirstModuleSelected as $value) {
				$field.=Get_Modul_fields_check_from_load($value,$checkname);
				// LogFileSimple(TypeOFErrors::ERRORLG."CheckAllFirstForAllModules #### ".$field);
				if (!empty($field)) {
					return $field;
				}
			}
			return $field;
	}



	/**
	 * Gets the identifier for entity name.
	 *
	 * @param      string     $moduleName  The module name
	 *
	 * @throws     Exception  (description)
	 *
	 * @return     string     The identifier for entity name.
	 */
	function getIdForEntityName($module,$moduleName="entityidfield")
	{
		global $adb,$root_directory, $log;
		try {

			$result = $adb->pquery("Select * from  vtiger_entityname where modulename = ?",array($module));
			$num_rows = $adb->num_rows($result);
			if ($num_rows>0) {
				$Resulti = $adb->query_result($result,0,$moduleName);

				if (!empty($Resulti)) {
					return $Resulti;
				} else {
					throw new Exception(TypeOFErrors::ERRORLG." Something was wrong RESULT IS EMPTY", 1);
				}
			} else {
				throw new Exception(TypeOFErrors::ERRORLG."Not exist Map with this ID=".$Queryid,1);
			}
		} catch (Exception $ex) {
			$log->debug(TypeOFErrors::ERRORLG." Something was wrong check the Exception ".$ex);
			return "";
		}
	}


	/**
	 * This function is to get all maps from database a
	 * @param string $value  this param is if you want to filter by map type
	 * @return  a list of maps 
	 */
	function GetAllrelation1TOManyMaps($module="",$selectedmodule="")
	{
		global $adb, $root_directory, $log;
		if (!empty($module))
		{
			$log->debug("Info!! Value is not ampty");
			// return "is not empty $module";
			$sql="SELECT relmodule FROM `vtiger_fieldmodulerel` WHERE module = '$module' UNION SELECT module FROM `vtiger_fieldmodulerel` WHERE relmodule = '$module' ";
			$result = $adb->query($sql);
			$num_rows=$adb->num_rows($result);
			$historymap="";
			$a='<option value="" >(Select a module)</option>';
			if($num_rows!=0)
			{
				for($i=1;$i<=$num_rows;$i++)
				{
					$Modules = $adb->query_result($result,$i-1,'relmodule');
				
					if (!empty($selectedmodule) && $Modules===$selectedmodule) {
						$a.='<option selected value="'.$Modules.'">'.str_replace("'", "", getTranslatedString($Modules)).'</option>';
					}else{
						$a.='<option value="'.$Modules.'">'.str_replace("'", "", getTranslatedString($Modules)).'</option>';
					}
				
					
				}
			return $a;
			}else{$log->debug("Info!! The database is empty or something was wrong");}
		}else {
			return "";
		}
		
	}

	/**
	 * Gets the allrelation.
	 *
	 * @param      string  $module  The module
	 *
	 * @return     string  The allrelation.
	 */
	function GetAllrelationModules($module="")
	{
		global $adb, $root_directory, $log;
		if (!empty($module))
		{
			$log->debug("Info!! Value is not ampty");
			$idmodul=getModuleID($module,"tabid");
			$sql="SELECT * from vtiger_relatedlists where tabid='$idmodul'";
			$result = $adb->query($sql);
			$num_rows=$adb->num_rows($result);
			$historymap="";
			$a='<option value="" >(Select a module)</option>';
			if($num_rows!=0)
			{
				for($i=1;$i<=$num_rows;$i++)
				{
					$Modules = $adb->query_result($result,$i-1,'label');
				
					$a.='<option value="'.$Modules.'">'.str_replace("'", "", getTranslatedString($Modules)).'</option>';	            
				}
			return $a;
			}else{$log->debug("Info!! The database is empty or something was wrong");}
		}else {
			return "";
		}
		
	}

	/**
	 * function to check if exist in a array a word
	 *
	 * @param      <type>   $str    The string
	 * @param      array    $arr    The arr
	 *
	 * @return     string  ( the value )
	 */
	function containsArray($str, array $arr)
	{
		$value="";
		foreach($arr as $a) {
			if (stripos($str,explode("#",$a)[1]) !== false)
			{
				$value=$a;
			}
		}
		return $value;
	}
#end Region