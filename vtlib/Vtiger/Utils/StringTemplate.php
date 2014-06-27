<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Template class will enable you to replace a merge fields defined in the String
 * with values set dynamically.
 *
 * @author Prasad
 * @package vtlib
 */
class Vtiger_StringTemplate {
	// Template variables set dynamically
	var $tplvars = Array();

	/**
	 * Identify variable with the following pattern
	 * $VARIABLE_KEY$
	 */
	var $_lookfor = '/\$([^\$]+)\$/';

	/**
	 * Constructor
	 */
	function __construct() {
	}

	/**
	 * Assign replacement value for the variable.
	 */
	function assign($key, $value) {
		$this->tplvars[$key] = $value;
	}	

	/**
	 * Get replacement value for the variable.
	 */
	function get($key) {
		$value = false;
		if(isset($this->tplvars[$key])) {
			$value = $this->tplvars[$key];
		}
		return $value;
	}

	/**
	 * Clear all the assigned variable values.
	 * (except the once in the given list)
	 */
	function clear($exceptvars=false) {
		$restorevars = Array();
		if($exceptvars) {
			foreach($exceptvars as $varkey) {
				$restorevars[$varkey] = $this->get($varkey);
			}
		}		
		unset($this->tplvars);

		$this->tplvars = Array();
		foreach($restorevars as $key=>$val) $this->assign($key, $val);
	}

	/**
	 * Merge the given file with variable values assigned.
	 * @param $instring input string template
	 * @param $avoidLookup should be true if only verbatim file copy needs to be done
	 * @returns merged contents
	 */
	function merge($instring, $avoidLookup=false) {
		if(empty($instring)) return $instring;

		if(!$avoidLookup) {

			/** Look for variables */
			$matches = Array();
			preg_match_all($this->_lookfor, $instring, $matches);

			/** Replace variables found with value assigned. */
			$matchcount = count($matches[1]);
			for($index = 0; $index < $matchcount; ++$index) {
				$matchstr = $matches[0][$index];
				$matchkey = $matches[1][$index];

				$matchstr_regex = $this->__formatAsRegex($matchstr);

				$replacewith = $this->get($matchkey);
				if($replacewith) {
					$instring = preg_replace(
						"/$matchstr_regex/", $replacewith, $instring);
				}
			}
		}
		return $instring;
	}

	/**
	 * Clean up the input to be used as a regex
	 * @access private
	 */
	function __formatAsRegex($value) {
		// If / is not already escaped as \/ do it now
		$value = preg_replace('/\//', '\\/', $value);
		// If $ is not already escaped as \$ do it now
		$value = preg_replace('/(?<!\\\)\$/', '\\\\$', $value);
		return $value;
	}

}
?>
