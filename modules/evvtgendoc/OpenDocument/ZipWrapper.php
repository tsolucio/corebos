<?php
/*************************************************************************************************
 * Copyright 2009 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 * Author: Joe Bordes
 *************************************************************************************************/

class ZipWrapper {
	public static function read($archive, $filename) {
		$zip = new ZipArchive;
		if (file_exists($archive) && $zip->open(realpath($archive)) && $zip->locateName($filename) !== false) {
			return $zip->getFromName($filename);
		}
		return false;
	}

	public static function write($archive, $filename, $content) {
		$zip = new ZipArchive;
		if (file_exists($archive)) {
			$zip->open(realpath($archive));
		} else {
			$zip->open(getcwd() . '/' . $archive, ZipArchive::CREATE);
		}

		if ($zip->locateName($filename) !== false) {
			$zip->deleteName($filename);
		}

		return $zip->addFromString($filename, $content);
	}

	public static function copyPictures($origin, $destination, $changedImages = array(), $newImages = array()) {
		$ziporg = new ZipArchive;
		$zipdst = new ZipArchive;
		if (file_exists($origin) && file_exists($destination) && $ziporg->open(realpath($origin)) && $zipdst->open(realpath($destination))) {
			$tempdir= 'cache/'.uniqid('gendoc');
			mkdir($tempdir);
			$ziporg->extractTo($tempdir);
			$ziporg->close();
			if (is_dir("$tempdir/Pictures")) { // Tenemos imagenes para copiar y ficheros
				foreach (new DirectoryIterator("$tempdir/Pictures") as $pictures) {
					if ($pictures->isDot()) {
						continue;
					}
					$fname=$pictures->getFilename();
					if (array_key_exists('Pictures/'.$fname, $changedImages)) {
						$filein=$changedImages['Pictures/'.$fname];
					} else {
						$filein="$tempdir/Pictures/".$fname;
					}
					$zipdst->addFile($filein, 'Pictures/'.$fname);
				}
			}
			if (count($newImages)>0) { // Imagenes nuevas
				// Manifest ya esta actualizado, solo hay que añadir estas imagenes
				foreach ($newImages as $newImage) {
					$zipdst->addFile(realpath($newImage), 'Pictures/'.basename($newImage));
				}
			}
			// Now we look for any Object Drawings/Graphs
			foreach (glob("$tempdir/Object*", GLOB_ONLYDIR) as $ocode) {
				$dname = str_replace_once(dirname($ocode).'/', '', $ocode);
				foreach (new DirectoryIterator($ocode) as $pictures) {
					if ($pictures->isDot()) {
						continue;
					}
					$fname=$pictures->getFilename();
					$filein=$ocode.'/'.$fname;
					$zipdst->addFile($filein, $dname.'/'.$fname);
				}
			}
			$zipdst->close();
			ZipWrapper::unlinkRecursive($tempdir, true); // Elimino directorio temporal
		}
	}

	/**
	 * Recursively delete a directory
	 *
	 * @param string $dir Directory name
	 * @param boolean $deleteRootToo Delete specified top-level directory as well
	 */
	public static function unlinkRecursive($dir, $deleteRootToo) {
		if (!$dh = @opendir($dir)) {
			return;
		}
		while (false !== ($obj = readdir($dh))) {
			if ($obj == '.' || $obj == '..') {
				continue;
			}

			if (!@unlink($dir . '/' . $obj)) {
				ZipWrapper::unlinkRecursive($dir.'/'.$obj, true);
			}
		}

			closedir($dh);

		if ($deleteRootToo) {
			@rmdir($dir);
		}
	}
}
?>
