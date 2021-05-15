<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class FileField {

	public static function getFileSizeDisplayValue($filesize) {
		$format = '%0.2f ';
		if ($filesize < 1024) {
			$filesize = sprintf($format, round($filesize, 2)).'b';
		} elseif ($filesize > 1024 && $filesize < 1048576) {
			$filesize = sprintf($format, round($filesize/1024, 2)).'kB';
		} elseif ($filesize > 1048576) {
			$filesize = sprintf($format, round($filesize/(1024*1024), 2)).'MB';
		}
		return $filesize;
	}

	public static function getFileIcon($value, $downloadtype, $module) {
		global $theme;
		$value = trim($value);
		$fileicon = '';
		if ($value != '') {
			$img = "<img src='";
			$style = 'style="border:0;padding:0 1px;"';
			if ($downloadtype == 'I') {
				$ext = strtolower(substr($value, strrpos($value, '.') + 1));
				if ($ext == 'bin' || $ext == 'exe' || $ext == 'rpm') {
					$i18nicon = getTranslatedString('Execute', 'cbupdater');
					$fileicon = $img . vtiger_imageurl('fExeBin.gif', $theme)."' alt='$i18nicon' title='$i18nicon' $style>";
				} elseif ($ext == 'jpg' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png') {
					$i18nicon = getTranslatedString('Image', $module);
					$fileicon = $img.vtiger_imageurl('fbImageFile.gif', $theme)."' alt='$i18nicon' title='$i18nicon' $style>";
				} elseif ($ext == 'txt' || $ext == 'doc' || $ext == 'xls') {
					$i18nicon = getTranslatedString('Document', $module);
					$fileicon = $img.vtiger_imageurl('fbTextFile.gif', $theme)."' alt='$i18nicon' title='$i18nicon' $style>";
				} elseif ($ext == 'zip' || $ext == 'gz' || $ext == 'rar') {
					$i18nicon = getTranslatedString('Compressed', $module);
					$fileicon = $img.vtiger_imageurl('fbZipFile.gif', $theme)."'  alt='$i18nicon' title='$i18nicon'$style>";
				} else {
					$i18nicon = getTranslatedString('Unknown', $module);
					$fileicon = $img.vtiger_imageurl('fbUnknownFile.gif', $theme)."' alt='$i18nicon' title='$i18nicon' $style>";
				}
			} elseif ($downloadtype == 'E') {
				$i18nicon = getTranslatedString('LBL_EXTERNAL_LNK', $module);
				$fileicon = $img . vtiger_imageurl('fbLink.gif', $theme)."' alt='$i18nicon' title='$i18nicon' $style>";
			}
		}
		return $fileicon;
	}
}