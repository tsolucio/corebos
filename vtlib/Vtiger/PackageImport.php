<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('vtlib/Vtiger/PackageExport.php');
include_once('vtlib/Vtiger/Unzip.php');
include_once('vtlib/Vtiger/Module.php');
include_once('vtlib/Vtiger/Event.php');
include_once('vtlib/Vtiger/Cron.php');

/**
 * Provides API to import module into vtiger CRM
 * @package vtlib
 */
class Vtiger_PackageImport extends Vtiger_PackageExport {

	/**
	 * Module Meta XML File (Parsed)
	 * @access private
	 */
	var $_modulexml;
	/**
	 * Module Fields mapped by [modulename][fieldname] which
	 * will be used to create customviews.
	 * @access private
	 */
	var $_modulefields_cache = Array();

	/**
	 * License of the package.
	 * @access private
	 */
	var $_licensetext = false;

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Parse the manifest file
	 * @access private
	 */
	function __parseManifestFile($unzip) {
		$manifestfile = $this->__getManifestFilePath();
		$unzip->unzip('manifest.xml', $manifestfile);
		$this->_modulexml = simplexml_load_file($manifestfile);
		unlink($manifestfile);
	}

	/**
	 * Get type of package (as specified in manifest)
	 */
	function type() {
		if(!empty($this->_modulexml) && !empty($this->_modulexml->type)) {
			return $this->_modulexml->type;
		}
		return false;
	}

	/**
	 * XPath evaluation on the root module node.
	 * @param String Path expression
	 */
	function xpath($path) {
		return $this->_modulexml->xpath($path);
	}

	/**
	 * Get the value of matching path (instead of complete xpath result)
	 * @param String Path expression for which value is required
	 */
	function xpath_value($path) {
		$xpathres = $this->xpath($path);
		foreach($xpathres as $pathkey=>$pathvalue) {
			if($pathkey == $path) return $pathvalue;
		}
		return false;
	}

	/**
	 * Are we trying to import language package?
	 */
	function isLanguageType($zipfile =null) {
		if(!empty($zipfile)) {
			if(!$this->checkZip($zipfile)) {
				return false;
			}
		}
		$packagetype = $this->type();

		if($packagetype) {
			$lcasetype = strtolower($packagetype);
			if($lcasetype == 'language') return true;
		}
		return false;
	}

	/**
	 * checks whether a package is module bundle or not.
	 * @param String $zipfile - path to the zip file.
	 * @return Boolean - true if given zipfile is a module bundle and false otherwise.
	 */
	function isModuleBundle($zipfile = null) {
		// If data is not yet available
		if(!empty($zipfile)) {
			if(!$this->checkZip($zipfile)) {
				return false;
			}
		}

		return (boolean)$this->_modulexml->modulebundle;
	}

	/**
	 * @return Array module list available in the module bundle.
	 */
	function getAvailableModuleInfoFromModuleBundle() {
		$list = (Array)$this->_modulexml->modulelist;
		return (Array)$list['dependent_module'];
	}

	/**
	 * Get the license of this package
	 * NOTE: checkzip should have been called earlier.
	 */
	function getLicense() {
		return $this->_licensetext;
	}

	/**
	 * Check if zipfile is a valid package
	 * @access private
	 */
	function checkZip($zipfile) {
		$unzip = new Vtiger_Unzip($zipfile);
		$filelist = $unzip->getList();

		$manifestxml_found = false;
		$languagefile_found = false;
		$vtigerversion_found = false;

		$modulename = null;
		$language_modulename = null;

		foreach($filelist as $filename=>$fileinfo) {
			$matches = Array();
			preg_match('/manifest.xml/', $filename, $matches);
			if(count($matches)) {
				$manifestxml_found = true;
				$this->__parseManifestFile($unzip);
				$modulename = $this->_modulexml->name;
				$isModuleBundle = (string)$this->_modulexml->modulebundle;

				if($isModuleBundle === 'true' && (!empty($this->_modulexml)) &&
						(!empty($this->_modulexml->dependencies)) &&
						(!empty($this->_modulexml->dependencies->vtiger_version))) {
					$languagefile_found = true;
					break;
				}

				// Do we need to check the zip further?
				if($this->isLanguageType()) {
					$languagefile_found = true; // No need to search for module language file.
					break;
				} else {
					continue;
				}
			}
			// Check for language file.
			preg_match("/modules\/([^\/]+)\/language\/en_us.lang.php/", $filename, $matches);
			if(count($matches)) { $language_modulename = $matches[1]; continue; }
		}

		// Verify module language file.
		if(!empty($language_modulename) && $language_modulename == $modulename) {
			$languagefile_found = true;
		}

		if(!empty($this->_modulexml) &&
			!empty($this->_modulexml->dependencies) &&
			!empty($this->_modulexml->dependencies->vtiger_version)) {
				$vtigerversion_found = true;
		}

		$validzip = false;
		if($manifestxml_found && $languagefile_found && $vtigerversion_found)
			$validzip = true;

		if($validzip) {
			if(!empty($this->_modulexml->license)) {
				if(!empty($this->_modulexml->license->inline)) {
					$this->_licensetext = $this->_modulexml->license->inline;
				} else if(!empty($this->_modulexml->license->file)) {
					$licensefile = $this->_modulexml->license->file;
					$licensefile = "$licensefile";
					if(!empty($filelist[$licensefile])) {
						$this->_licensetext = $unzip->unzip($licensefile);
					} else {
						$this->_licensetext = "Missing $licensefile!";
					}
				}
			}
		}

		if($unzip) $unzip->close();

		return $validzip;
	}

	/**
	 * Get module name packaged in the zip file
	 * @access private
	 */
	function getModuleNameFromZip($zipfile) {
		if(!$this->checkZip($zipfile)) return null;

		return (string)$this->_modulexml->name;
	}

	/**
	 * Get short description packaged in the zip file
	 * @access private
	 */
	function getShortDescriptionFromZip($zipfile) {
		if(!$this->checkZip($zipfile)) return null;

		return (string)$this->_modulexml->short_description;
	}
	

	/**
	 * Get short description packaged in the zip file
	 * @access private
	 */
	function getShortDescription() {
		return (string)$this->_modulexml->short_description;
	}

	/**
	 * returns the name of the module.
	 * @return String - name of the module as given in manifest file.
	 */
	function getModuleName() {
		return (string)$this->_modulexml->name;
	}

	/**
	 * Cache the field instance for re-use
	 * @access private
	 */
	function __AddModuleFieldToCache($moduleInstance, $fieldname, $fieldInstance) {
		$this->_modulefields_cache["$moduleInstance->name"]["$fieldname"] = $fieldInstance;
	}

	/**
	 * Get field instance from cache
	 * @access private
	 */
	function __GetModuleFieldFromCache($moduleInstance, $fieldname) {
		return $this->_modulefields_cache["$moduleInstance->name"]["$fieldname"];
	}

	/**
	 * Initialize Import
	 * @access private
	 */
	function initImport($zipfile, $overwrite) {
		$module = $this->getModuleNameFromZip($zipfile);
		if($module != null) {

			$unzip = new Vtiger_Unzip($zipfile);

			// Unzip selectively
			$unzip->unzipAllEx( ".",
				Array(
					// Include only file/folders that need to be extracted
					'include' => Array('templates', "modules/$module", 'cron','manifest.xml'),
					//'exclude' => Array('manifest.xml')
					// NOTE: If excludes is not given then by those not mentioned in include are ignored.
				),
				// What files needs to be renamed?
				Array(
					// Templates folder
					'templates' => "Smarty/templates/modules/$module",
					// Cron folder
					'cron' => "cron/modules/$module"
				)
			);

			@rename('manifest.xml',"modules/$module/manifest.xml");
			// eliminate temporary directories
			@rmdir("modules/$module/modules/$module");
			@rmdir("modules/$module/modules/");
			@rmdir("Smarty/templates/modules/$module/templates");
			@rmdir("cron/modules/$module/cron");
			$dirs = glob("modules/$module/*",GLOB_ONLYDIR);
			foreach ($dirs as $path) {
				$dir = basename($path);
				@rmdir("modules/$module/$dir/modules/$module/$dir");
				@rmdir("modules/$module/$dir/modules/$module");
				@rmdir("modules/$module/$dir/modules/");
			}
			if($unzip) $unzip->close();
		}
		return $module;
	}

	function getTemporaryFilePath($filepath=false) {
		return 'cache/'. $filepath;
	}
	/**
	 * Get dependent version
	 * @access private
	 */
	function getDependentVtigerVersion() {
		return $this->_modulexml->dependencies->vtiger_version;
	}

	/**
	 * Get dependent Maximum version
	 * @access private
	 */
	function getDependentMaxVtigerVersion() {
		return $this->_modulexml->dependencies->vtiger_max_version;
	}

	/**
	 * Get package version
	 * @access private
	 */
	function getVersion() {
		return $this->_modulexml->version;
	}

	/**
	 * Import Module from zip file
	 * @param String Zip file name
	 * @param Boolean True for overwriting existing module
	 *
	 * @todo overwrite feature is not functionally currently.
	 */
	function import($zipfile, $overwrite=false) {
		$module = $this->getModuleNameFromZip($zipfile);
		if($module != null) {
			// If data is not yet available
			if(empty($this->_modulexml)) {
				$this->__parseManifestFile($unzip);
			}

			$buildModuleArray = array();
			$installSequenceArray = array();
			$moduleBundle = (boolean)$this->_modulexml->modulebundle;
			if($moduleBundle == true) {
				$moduleList = (Array)$this->_modulexml->modulelist;
				foreach($moduleList as $moduleInfos) {
					foreach($moduleInfos as $moduleInfo) {
						$moduleInfo = (Array)$moduleInfo;
						$buildModuleArray[] = $moduleInfo;
						$installSequenceArray[] = $moduleInfo['install_sequence'];
					}
				}
				sort($installSequenceArray);
				$unzip = new Vtiger_Unzip($zipfile);
				$unzip->unzipAllEx($this->getTemporaryFilePath());
				foreach ($installSequenceArray as $sequence) {
					foreach ($buildModuleArray as $moduleInfo) {
						if($moduleInfo['install_sequence'] == $sequence) {
							$this->import($this->getTemporaryFilePath($moduleInfo['filepath']), $overwrite);
						}
					}
				}
			} else {
				$module = $this->initImport($zipfile, $overwrite);
				// Call module import function
				$this->import_Module();
			}
		}
	}

	/**
	 * Import Module from manifest.xml file. Other files should already be in place
	 * @param String manifest.xml file path
	 */
	function importManifest($manifestfile) {
		global $adb,$log;
		if (!$this->loadManifestFromFile($manifestfile))
			return false;
		$module = (string) $this->_modulexml->name;
		if($module != null) {
			if ($this->isLanguageType()) {
				require_once('vtlib/Vtiger/Language.php');
				$languagePack = new Vtiger_Language();
				@$languagePack->register((string) $this->_modulexml->prefix,(string) $this->_modulexml->label,$module);
			} else {
				$sql = "select tabid from vtiger_tab where name=?";
				$result = $adb->pquery($sql, array($module));
				if ($result and $adb->num_rows($result) > 0) {
					return false; // module already installed
				}
				$this->import_Module();
			}
		} else {
			return false;
		}
		return true;
	}

	/**
	 * Load manifest.xml from specified file.
	 * @param String manifest.xml file path
	 */
	function loadManifestFromFile($manifestfile) {
		global $adb,$log;
		if (!is_file($manifestfile))
			$manifestfile .= '/manifest.xml';  // in case they just give us the path
		if (!is_file($manifestfile))
			return false;
		$this->_modulexml = simplexml_load_file($manifestfile);
		return true;
	}

	/**
	 * Import Module
	 * @access private
	 */
	function import_Module() {
		$tabname = (string)$this->_modulexml->name;
		$tablabel= (string)$this->_modulexml->label;
		$parenttab=(string)$this->_modulexml->parent;
		$tabversion=(string)$this->_modulexml->version;

		$isextension= false;
		if(!empty($this->_modulexml->type)) {
			$type = strtolower($this->_modulexml->type);
			if($type == 'extension' || $type == 'language')
				$isextension = true;
		}

		$vtigerMinVersion = (string)$this->_modulexml->dependencies->vtiger_version;
		$vtigerMaxVersion = (string)$this->_modulexml->dependencies->vtiger_max_version;

		$moduleInstance = new Vtiger_Module();
		$moduleInstance->name = $tabname;
		$moduleInstance->label= $tablabel;
		if ($menuInstance = Vtiger_Menu::getInstance($parenttab)){
			$moduleInstance->parent=$parenttab;
		} else {
			$moduleInstance->parent="Tools";
		}
		$moduleInstance->isentitytype = ($isextension != true);
		$moduleInstance->version = (!$tabversion)? 0 : $tabversion;
		$moduleInstance->minversion = (!$vtigerMinVersion)? false : $vtigerMinVersion;
		$moduleInstance->maxversion = (!$vtigerMaxVersion)?  false : $vtigerMaxVersion;
		$moduleInstance->save();

		if(!empty($parenttab)) {
			$menuInstance = Vtiger_Menu::getInstance($parenttab);
			if ($menuInstance == NULL) {
				$menuInstance = Vtiger_Menu::getInstance("Tools");
				self::log("Module attached to Tools because $parenttab does not exist");
			}
			$menuInstance->addModule($moduleInstance);
		}

		$this->import_Tables($this->_modulexml);
		$this->import_Blocks($this->_modulexml, $moduleInstance);
		$this->import_CustomViews($this->_modulexml, $moduleInstance);
		$this->import_SharingAccess($this->_modulexml, $moduleInstance);
		$this->import_Events($this->_modulexml, $moduleInstance);
		$this->import_Actions($this->_modulexml, $moduleInstance);
		$this->import_RelatedLists($this->_modulexml, $moduleInstance);
		$this->import_CustomLinks($this->_modulexml, $moduleInstance);
		$this->import_CronTasks($this->_modulexml);
		$moduleInstance->initWebservice();
		Vtiger_Module::fireEvent($moduleInstance->name, Vtiger_Module::EVENT_MODULE_POSTINSTALL);
	}

	/**
	 * Import Tables of the module
	 * @access private
	 */
	function import_Tables($modulenode) {
		if(empty($modulenode->tables) || empty($modulenode->tables->table)) return;

		/**
		 * Record the changes in schema file
		 */
		$schemafile = @fopen("modules/$modulenode->name/schema.xml", 'w');
		if($schemafile) {
			fwrite($schemafile, "<?xml version='1.0'?>\n");
			fwrite($schemafile, "<schema>\n");
			fwrite($schemafile, "\t<tables>\n");
		}

		// Import the table via queries
		foreach($modulenode->tables->table as $tablenode) {
			$tablename = $tablenode->name;
			$tablesql  = "$tablenode->sql"; // Convert to string format

			// Save the information in the schema file.
			if($schemafile) {
				fwrite($schemafile, "\t\t<table>\n");
				fwrite($schemafile, "\t\t\t<name>$tablename</name>\n");
				fwrite($schemafile, "\t\t\t<sql><![CDATA[$tablesql]]></sql>\n");
				fwrite($schemafile, "\t\t</table>\n");
			}

			// Avoid executing SQL that will DELETE or DROP table data
			if(Vtiger_Utils::IsCreateSql($tablesql)) {
				if(!Vtiger_Utils::checkTable($tablename)) {
					self::log("SQL: $tablesql ... ", false);
					Vtiger_Utils::ExecuteQuery($tablesql);
					self::log("DONE");
				}
			} else {
				if(Vtiger_Utils::IsDestructiveSql($tablesql)) {
					self::log("SQL: $tablesql ... SKIPPED");
				} else {
					self::log("SQL: $tablesql ... ", false);
					Vtiger_Utils::ExecuteQuery($tablesql);
					self::log("DONE");
				}
			}
		}
		if($schemafile) {
			fwrite($schemafile, "\t</tables>\n");
			fwrite($schemafile, "</schema>\n");
			fclose($schemafile);
		}
	}

	/**
	 * Import Blocks of the module
	 * @access private
	 */
	function import_Blocks($modulenode, $moduleInstance) {
		if(empty($modulenode->blocks) || empty($modulenode->blocks->block)) return;
		foreach($modulenode->blocks->block as $blocknode) {
			$blockInstance = $this->import_Block($modulenode, $moduleInstance, $blocknode);
			$this->import_Fields($blocknode, $blockInstance, $moduleInstance);
		}
	}

	/**
	 * Import Block of the module
	 * @access private
	 */
	function import_Block($modulenode, $moduleInstance, $blocknode) {
		$blocklabel = $blocknode->label;

		$blockInstance = new Vtiger_Block();
		$blockInstance->label = $blocklabel;
		$moduleInstance->addBlock($blockInstance);
		return $blockInstance;
	}

	/**
	 * Import Fields of the module
	 * @access private
	 */
	function import_Fields($blocknode, $blockInstance, $moduleInstance) {
		if(empty($blocknode->fields) || empty($blocknode->fields->field)) return;

		foreach($blocknode->fields->field as $fieldnode) {
			$fieldInstance = $this->import_Field($blocknode, $blockInstance, $moduleInstance, $fieldnode);
		}
	}

	/**
	 * Import Field of the module
	 * @access private
	 */
	function import_Field($blocknode, $blockInstance, $moduleInstance, $fieldnode) {
		$fieldInstance = new Vtiger_Field();
		$fieldInstance->name         = $fieldnode->fieldname;
		$fieldInstance->label        = $fieldnode->fieldlabel;
		$fieldInstance->table        = $fieldnode->tablename;
		$fieldInstance->column       = $fieldnode->columnname;
		$fieldInstance->uitype       = $fieldnode->uitype;
		$fieldInstance->generatedtype= $fieldnode->generatedtype;
		$fieldInstance->readonly     = $fieldnode->readonly;
		$fieldInstance->presence     = $fieldnode->presence;
		$fieldInstance->defaultvalue = $fieldnode->defaultvalue;
		$fieldInstance->maximumlength= (empty($fieldnode->maximumlength) ? 100 : $fieldnode->maximumlength);
		$fieldInstance->sequence     = $fieldnode->sequence;
		$fieldInstance->quicksequence= $fieldnode->quickcreatesequence;
		$fieldInstance->typeofdata   = $fieldnode->typeofdata;
		$fieldInstance->displaytype  = $fieldnode->displaytype;
		$fieldInstance->info_type    = $fieldnode->info_type;
		if(!empty($fieldnode->quickcreate))
			$fieldInstance->quickcreate = $fieldnode->quickcreate;

		if(!empty($fieldnode->helpinfo))
			$fieldInstance->helpinfo = $fieldnode->helpinfo;

		if(isset($fieldnode->masseditable))
			$fieldInstance->masseditable = $fieldnode->masseditable;

		if(isset($fieldnode->columntype) && !empty($fieldnode->columntype))
			$fieldInstance->columntype = $fieldnode->columntype;

		$blockInstance->addField($fieldInstance);

		// Set the field as entity identifier if marked.
		if(!empty($fieldnode->entityidentifier)) {
			$moduleInstance->entityidfield = (string)$fieldnode->entityidentifier->entityidfield;
			$moduleInstance->entityidcolumn= (string)$fieldnode->entityidentifier->entityidcolumn;
			$moduleInstance->setEntityIdentifier($fieldInstance);
		}

		// Check picklist values associated with field if any.
		if(!empty($fieldnode->picklistvalues) && !empty($fieldnode->picklistvalues->picklistvalue)) {
			$picklistvalues = Array();
			foreach($fieldnode->picklistvalues->picklistvalue as $picklistvaluenode) {
				$picklistvalues[] = $picklistvaluenode;
			}
			$fieldInstance->setPicklistValues( $picklistvalues );
		}

		// Check related modules associated with this field
		if(!empty($fieldnode->relatedmodules) && !empty($fieldnode->relatedmodules->relatedmodule)) {
			$relatedmodules = Array();
			foreach($fieldnode->relatedmodules->relatedmodule as $relatedmodulenode) {
				$relatedmodules[] = (string)$relatedmodulenode;
			}
			$fieldInstance->setRelatedModules($relatedmodules);
		}

		$this->__AddModuleFieldToCache($moduleInstance, $fieldnode->fieldname, $fieldInstance);
		return $fieldInstance;
	}

	/**
	 * Import Custom views of the module
	 * @access private
	 */
	function import_CustomViews($modulenode, $moduleInstance) {
		if(empty($modulenode->customviews) || empty($modulenode->customviews->customview)) return;
		foreach($modulenode->customviews->customview as $customviewnode) {
			$filterInstance = $this->import_CustomView($modulenode, $moduleInstance, $customviewnode);

		}
	}

	/**
	 * Import Custom View of the module
	 * @access private
	 */
	function import_CustomView($modulenode, $moduleInstance, $customviewnode) {
		$viewname = $customviewnode->viewname;
		$setdefault=$customviewnode->setdefault;
		$setmetrics=$customviewnode->setmetrics;

		$filterInstance = new Vtiger_Filter();
		$filterInstance->name = $viewname;
		$filterInstance->isdefault = $setdefault;
		$filterInstance->inmetrics = $setmetrics;

		$moduleInstance->addFilter($filterInstance);

		foreach($customviewnode->fields->field as $fieldnode) {
			$fieldInstance = $this->__GetModuleFieldFromCache($moduleInstance, $fieldnode->fieldname);
			$filterInstance->addField($fieldInstance, $fieldnode->columnindex);
		}
		if(!empty($customviewnode->rules->rule)) {
			foreach($customviewnode->rules->rule as $rulenode) {
				$fieldInstance = $this->__GetModuleFieldFromCache($moduleInstance, $rulenode->fieldname);
				$filterInstance->addRule($fieldInstance, $rulenode->comparator, $rulenode->value, $rulenode->columnindex, $rulenode->groupid, $rulenode->column_condition);
			}
		}
		if(!empty($customviewnode->groups->group)) {
			foreach($customviewnode->groups->group as $groupnode) {
				$filterInstance->addGroup($groupnode->groupid, $groupnode->group_condition, $groupnode->condition_expression);
			}
		}
	}

	/**
	 * Import Sharing Access of the module
	 * @access private
	 */
	function import_SharingAccess($modulenode, $moduleInstance) {
		if(empty($modulenode->sharingaccess)) return;

		if(!empty($modulenode->sharingaccess->default)) {
			foreach($modulenode->sharingaccess->default as $defaultnode) {
				$moduleInstance->setDefaultSharing($defaultnode);
			}
		}
	}

	/**
	 * Import Events of the module
	 * @access private
	 */
	function import_Events($modulenode, $moduleInstance) {
		if(empty($modulenode->events) || empty($modulenode->events->event))	return;

		if(Vtiger_Event::hasSupport()) {
			foreach($modulenode->events->event as $eventnode) {
				$this->import_Event($modulenode, $moduleInstance, $eventnode);
			}
		}
	}

	/**
	 * Import Event of the module
	 * @access private
	 */
	function import_Event($modulenode, $moduleInstance, $eventnode) {
		$event_condition = '';
		$event_dependent = '[]';
		if(!empty($eventnode->condition)) $event_condition = "$eventnode->condition";
		if(!empty($eventnode->dependent)) $event_dependent = "$eventnode->dependent";
		Vtiger_Event::register($moduleInstance,
			(string)$eventnode->eventname, (string)$eventnode->classname,
			(string)$eventnode->filename, (string)$event_condition, (string)$event_dependent
		);
	}

	/**
	 * Import actions of the module
	 * @access private
	 */
	function import_Actions($modulenode, $moduleInstance) {
		if(empty($modulenode->actions) || empty($modulenode->actions->action)) return;
		foreach($modulenode->actions->action as $actionnode) {
			$this->import_Action($modulenode, $moduleInstance, $actionnode);
		}
	}

	/**
	 * Import action of the module
	 * @access private
	 */
	function import_Action($modulenode, $moduleInstance, $actionnode) {
		$actionstatus = $actionnode->status;
		if($actionstatus == 'enabled')
			$moduleInstance->enableTools($actionnode->name);
		else
			$moduleInstance->disableTools($actionnode->name);
	}

	/**
	 * Import related lists of the module
	 * @access private
	 */
	function import_RelatedLists($modulenode, $moduleInstance) {
		if(empty($modulenode->relatedlists) || empty($modulenode->relatedlists->relatedlist)) return;
		foreach($modulenode->relatedlists->relatedlist as $relatedlistnode) {
			$relModuleInstance = $this->import_Relatedlist($modulenode, $moduleInstance, $relatedlistnode);
		}
	}

	/**
	 * Import related list of the module.
	 * @access private
	 */
	function import_Relatedlist($modulenode, $moduleInstance, $relatedlistnode) {
		$relModuleInstance = Vtiger_Module::getInstance($relatedlistnode->relatedmodule);
		$label = $relatedlistnode->label;
		$actions = false;
		if(!empty($relatedlistnode->actions) && !empty($relatedlistnode->actions->action)) {
			$actions = Array();
			foreach($relatedlistnode->actions->action as $actionnode) {
				$actions[] = "$actionnode";
			}
		}
		if($relModuleInstance) {
			$moduleInstance->setRelatedList($relModuleInstance, "$label", $actions, "$relatedlistnode->function");
		}
		return $relModuleInstance;
	}

	/**
	 * Import custom links of the module.
	 * @access private
	 */
	function import_CustomLinks($modulenode, $moduleInstance) {
		if(empty($modulenode->customlinks) || empty($modulenode->customlinks->customlink)) return;

		foreach($modulenode->customlinks->customlink as $customlinknode) {
			$handlerInfo = null;
			if(!empty($customlinknode->handler_path)) {
				$handlerInfo = array();
				$handlerInfo = array("$customlinknode->handler_path",
										"$customlinknode->handler_class",
										"$customlinknode->handler");
			}
			if (isset($customlinknode->onlyonmodule)) {
				$onlyonmodule = empty($customlinknode->onlyonmodule);
			} else {
				$onlyonmodule = false;
			}
			$moduleInstance->addLink(
				"$customlinknode->linktype",
				"$customlinknode->linklabel",
				"$customlinknode->linkurl",
				"$customlinknode->linkicon",
				"$customlinknode->sequence",
				$handlerInfo,
				$onlyonmodule
			);
		}
	}

	/**
	 * Import cron jobs of the module.
	 * @access private
	 */
	function import_CronTasks($modulenode){
		if(empty($modulenode->crons) || empty($modulenode->crons->cron)) return;
		foreach ($modulenode->crons->cron as $cronTask){
			if(empty($cronTask->status)){
				$cronTask->status = Vtiger_Cron::$STATUS_DISABLED;
			} else {
				$cronTask->status = Vtiger_Cron::$STATUS_ENABLED;
			}
			if((empty($cronTask->sequence))){
				$cronTask->sequence=Vtiger_Cron::nextSequence();
			}
			Vtiger_Cron::register("$cronTask->name","$cronTask->handler", "$cronTask->frequency", "$modulenode->name","$cronTask->status","$cronTask->sequence","$cronTask->description");
		}
	}
}
?>
