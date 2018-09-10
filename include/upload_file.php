<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'config.php';

class UploadFile {
	public $field_name;
	public $stored_file_name;

	public function __construct($field_name) {
		global $log;
		$this->field_name = $field_name;
		$log->debug('Entering/Exiting UploadFile ('.$field_name.') method ...');
	}

	/** Function to get the url of the attachment
	 * @param $stored_file_name -- stored_file_name:: Type string
	 * @param $bean_id -- bean_id:: Type integer
	 * @returns urlstring -- urlstring:: Type string
	*/
	public function get_url($stored_file_name, $bean_id) {
		global $log, $site_URL, $upload_dir;
		$log->debug('Entering/Exiting get_url('.$stored_file_name.','.$bean_id.') method ...');
		//echo $site_URL.'/'.$upload_dir.$bean_id.$stored_file_name;
		//echo $_ENV['HOSTNAME'] .':' .$_SERVER['SERVER_PORT'].'/'.$upload_dir.$bean_id.$stored_file_name;
		return 'http://'.$_ENV['HOSTNAME'] .':' .$_SERVER['SERVER_PORT'].'/'.$upload_dir.$bean_id.$stored_file_name;
		//return $site_URL.'/'.$upload_dir.$bean_id.$stored_file_name;
	}

	/** Function to duplicate and copy a file to another location
	 * @param $old_id -- old_id:: Type integer
	 * @param $new_id -- new_id:: Type integer
	 * @param $file_name -- filename:: Type string
	*/
	public function duplicate_file($old_id, $new_id, $file_name) {
		global $log, $root_directory, $upload_dir;
		$log->debug('Entering duplicate_file('.$old_id.', '.$new_id.', '.$file_name.') method ...');
		$source = $root_directory.'/'.$upload_dir.$old_id.$file_name;
		$destination = $root_directory.'/'.$upload_dir.$new_id.$file_name;
		copy($source, $destination);
		$log->debug('Exiting duplicate_file method ...');
	}

	/** Function to get the status of the file upload
	 * @returns boolean
	 */
	public function confirm_upload() {
		global $log, $root_directory, $upload_dir, $upload_badext, $currentModule;
		$log->debug('Entering confirm_upload() method ...');
		$upload_maxsize = GlobalVariable::getVariable('Application_Upload_MaxSize', 3000000, $currentModule);

		if (!is_uploaded_file($_FILES[$this->field_name]['tmp_name'])) {
			$log->debug('Exiting confirm_upload method ...');
			return false;
		} elseif ($_FILES[$this->field_name]['size'] > $upload_maxsize) {
			die("ERROR: uploaded file was too big: max filesize:$upload_maxsize");
		}

		if (!is_writable($root_directory.'/'.$upload_dir)) {
			die("ERROR: cannot write to directory: $root_directory/$upload_dir for uploads");
		}

		require_once 'include/utils/utils.php';
		$this->stored_file_name = sanitizeUploadFileName($_FILES[$this->field_name]['name'], $upload_badext);
		$log->debug('Exiting confirm_upload method ...');
		return true;
	}

	/** Function to get the stored file name */
	public function get_stored_file_name() {
		global $log;
		$log->debug('Entering/Exiting get_stored_file_name() method ...');
		return $this->stored_file_name;
	}

	/** Function is to move a file and store it in given location
	 * @param $bean_id -- $bean_id:: Type integer
	 * @returns boolean
	*/
	public function final_move($bean_id) {
		global $log, $root_directory, $upload_dir;
		$log->debug('Entering final_move('.$bean_id.') method ...');

		$file_name = $bean_id.$this->stored_file_name;
		$destination = $root_directory.'/'.$upload_dir.$file_name;

		if (!move_uploaded_file($_FILES[$this->field_name]['tmp_name'], $destination)) {
			die('ERROR: cannot move_uploaded_file to destination');
		}
		$log->debug('Exiting final_move method ...');
		return true;
	}

	/** Function deletes a file for a given file name
	 * @param $bean_id -- bean_id:: Type integer
	 * @param $file_name -- file name:: Type string
	 * @returns boolean
	*/
	public function unlink_file($bean_id, $file_name) {
		global $log, $root_directory, $upload_dir;
		$log->debug('Entering/Exiting unlink_file('.$bean_id.','.$file_name.') method ...');
		return unlink($root_directory.'/'.$upload_dir.$bean_id.$file_name);
	}
}
?>
