<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * Zip creation class
 * @package vtlib
 */
class Vtiger_Zip {
	public $filename;
	private $zipa;

	public function __construct($filename) {
		$this->filename  = $filename;
		$this->zipa = new ZipArchive();
		if ($this->zipa->open($filename, ZipArchive::CREATE)!==true) {
			throw new Exception("cannot open <$filename>");
		}
	}

	public function addDir($dirname) {
		$dirname = rtrim($dirname, '/');
		$this->zipa->addEmptyDir($dirname);
	}

	public function addFile($filename, $cfilename, $fileComments = '', $data = false) {
		// $filename can be a local file OR the data which will be compressed
		if (substr($cfilename, -1)=='/') {
			$data = '';
			$this->zipa->addFromString($cfilename, $data);
		} elseif (file_exists($filename)) {
			$data = file_get_contents($filename);
			$this->zipa->addFile($filename, $cfilename);
		} elseif ($filename) {
			throw new Exception("Cannot add $filename. File not found");
			return false;
		} else {
			// DATA is given
			$this->zipa->addFromString($cfilename, $data);
		}
	}

	public function save($zipComments = '') {
		if ($zipComments!='') {
			$this->zipa->setArchiveComment($zipComments);
		}
		$this->zipa->close();
	}

	/**
	 * Push out the file content for download.
	 */
	public function forceDownload($zipfileName) {
		if (!$this->isInsideApplication($zipfileName)) {
			return false; // if the file is not inside the application tree we do not send it
		}
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=".basename($zipfileName).";");
		//header("Content-Transfer-Encoding: binary");
		$disk_file_size = filesize($zipfileName);
		header("Content-Length: ".$disk_file_size);
		readfile($zipfileName);
	}

	/**
	 * Get relative path (w.r.t base)
	 */
	public function __getRelativePath($basepath, $srcpath) {
		$base_realpath = $this->__normalizePath(realpath($basepath));
		$src_realpath  = $this->__normalizePath(realpath($srcpath));
		$search_index  = strpos($src_realpath, $base_realpath);
		if ($search_index === 0) {
			$startindex = strlen($base_realpath)+1;
			// On windows $base_realpath ends with / and On Linux it will not have / at end!
			if (strrpos($base_realpath, '/') == strlen($base_realpath)-1) {
				$startindex -= 1;
			}
			$relpath = substr($src_realpath, $startindex);
		}
		return $relpath;
	}

	/**
	 * Check and add '/' directory separator
	 */
	public function __fixDirSeparator($path) {
		if ($path != '' && (strrpos($path, '/') != strlen($path)-1)) {
			$path .= '/';
		}
		return $path;
	}

	/**
	 * Normalize the directory path separators.
	 */
	public function __normalizePath($path) {
		if ($path && strpos($path, '\\')!== false) {
			$path = preg_replace("/\\\\/", "/", $path);
		}
		return $path;
	}

	/**
	 * Copy the directory on the disk into zip file.
	 */
	public function copyDirectoryFromDisk($dirname, $zipdirname = null, $excludeList = null, $basedirname = null) {
		if (!$this->isInsideApplication($dirname)) {
			return false;
		}
		if (!is_dir($dirname)) {
			return false;
		}
		$dir = opendir($dirname);
		if ($dir===false) {
			return false;
		}
		if (strrpos($dirname, '/') != strlen($dirname)-1) {
			$dirname .= '/';
		}

		if ($basedirname == null) {
			$basedirname = realpath($dirname);
		}

		while (false !== ($file = readdir($dir))) {
			if ($file != '.' && $file != '..' &&
				$file != '.svn' && $file != 'CVS') {
					// Exclude the file/directory
				if (!empty($excludeList) && in_array("$dirname$file", $excludeList)) {
					continue;
				}

				if (is_dir("$dirname$file")) {
					$this->copyDirectoryFromDisk("$dirname$file", $zipdirname, $excludeList, $basedirname);
				} else {
					$zippath = $dirname;
					if ($zipdirname != null && $zipdirname != '') {
						$zipdirname = $this->__fixDirSeparator($zipdirname);
						$zippath = $zipdirname.$this->__getRelativePath($basedirname, $dirname);
					}
					$this->copyFileFromDisk($dirname, $zippath, $file);
				}
			}
		}
		closedir($dir);
	}

	/**
	 * Copy the directory on the disk into zip file with no offset
	 */
	public function copyDirectoryFromDiskNoOffset($dirname, $zipdirname = null, $excludeList = null, $basedirname = null) {
		if (!$this->isInsideApplication($dirname)) {
			return false;
		}
		$dir = opendir($dirname);
		if (strrpos($dirname, '/') != strlen($dirname)-1) {
			$dirname .= '/';
		}

		if ($basedirname == null) {
			$basedirname = realpath($dirname);
		}

		while (false !== ($file = readdir($dir))) {
			if ($file != '.' && $file != '..' &&
				$file != '.svn' && $file != 'CVS') {
					// Exclude the file/directory
				if (!empty($excludeList) && in_array("$dirname$file", $excludeList)) {
					continue;
				}

				if (is_dir("$dirname$file")) {
					$this->copyDirectoryFromDisk("$dirname$file", $file, $excludeList, $dirname.$file);
				} else {
					$zippath = '';
					if ($zipdirname != null && $zipdirname != '') {
						$zipdirname = $this->__fixDirSeparator($zipdirname);
						$zippath = $zipdirname.$this->__getRelativePath($basedirname, $dirname);
					}
					$this->copyFileFromDisk($dirname, $zippath, $file);
				}
			}
		}
		closedir($dir);
	}

	/**
	 * Copy the disk file into the zip.
	 */
	public function copyFileFromDisk($path, $zippath, $file) {
		$path = $this->__fixDirSeparator($path);
		$zippath = $this->__fixDirSeparator($zippath);
		$this->addFile("$path$file", "$zippath$file");
	}

	/**
	 * path is inside the application tree
	 */
	public function isInsideApplication($path2check) {
		global $root_directory;
		$rp = realpath($path2check);
		return (strpos($rp, $root_directory)===0);
	}
}
?>
