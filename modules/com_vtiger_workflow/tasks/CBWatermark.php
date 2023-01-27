<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'include/events/include.inc';

class CBWatermark extends VTTask {
	public $executeImmediately = true;
	public $queable = true;
	public $wmImageValue;
	public $imagefieldName;
	public $wmSize;
	public $wmPosition;

	public function getFieldNames() {
		return array('wmImageValue', 'imagefieldName', 'wmSize', 'wmPosition');
	}

	public function getContextVariables() {
		return array(
			'WaterMark_Image_Value' => array(
				'type' => '1',
				'values' => 'varchar',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_WaterMark_Image_Value',
			),
			'WaterMark_Image_Field' => array(
				'type' => '1',
				'values' => 'varchar',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_WaterMark_Image_Field',
			),
			'WaterMark_Size' => array(
				'type' => '7',
				'values' => 'integer',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_WaterMark_Size',
			),
			'WaterMark_Position' => array(
				'type' => '7',
				'values' => 'integer',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_WaterMark_Position',
			),
		);
	}

	public function doTask(&$entity) {
		global $logbg, $from_wf, $currentModule, $root_directory;
		$logbg->debug('> Watermark');

		// setting workflow variables
		$wmImageValue = null;
		$imagefieldName = null;
		$wmsize = null;
		$wmPosition = null;
		if (empty($entity->WorkflowContext['WaterMark_Image_Value'])) {
			$wmImageValue = $this->wmImageValue;
		} else {
			$wmImageValue = $entity->WorkflowContext['WaterMark_Image_Value'];
		}

		if (empty($entity->WorkflowContext['WaterMark_Image_Field'])) {
			$imagefieldName = $this->imagefieldName;
		} else {
			$imagefieldName = $entity->WorkflowContext['WaterMark_Image_Field'];
		}

		if (empty($entity->WorkflowContext['WaterMark_Size'])) {
			$wmsize = $this->wmSize;
		} else {
			$wmsize = $entity->WorkflowContext['WaterMark_Size'];
		}

		if (empty($entity->WorkflowContext['WaterMark_Position'])) {
			$wmPosition = $this->wmPosition;
		} else {
			$wmPosition = $entity->WorkflowContext['WaterMark_Position'];
		}
		$from_wf = true;
		$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
		$_REQUEST['ajxaction'] = 'Workflow';
		if (!empty($wmImageValue)) {
			$watermark = '';
			$watermark = $wmImageValue;
			$waterMarkUrl = $watermark;
			$waterMarkArr = explode('/', $waterMarkUrl);
			$waterMarkDotArr = explode('.', $waterMarkUrl);
			$waterMarkName = $waterMarkArr[count($waterMarkArr) - 1];
			$waterMarkType = $waterMarkDotArr[count($waterMarkDotArr) - 1];
			$waterMarkSavedPath = $root_directory . GlobalVariable::getVariable('Application_Storage_Directory', 'storage/');
			$data = $entity->getData();

			$mainImageFileName = '';
			$mainImageFileType = '';
			$mainImageFileSize = '';
			$mainImagePath = '';
			// $watermarkPrefix = 'WATERMARKED_';
			if ($currentModule == 'Documents') {
				$mainImageFileName = $data['filename'];
				// $mainImageFileName = $watermarkPrefix . $data['filename'];
				$mainImageFileType = $data['filetype'];
				$mainImageFileSize = $data['filesize'];
				$mainImageDownloadUrl = $data['_downloadurl'];
				$mainImagePath = $root_directory . substr(parse_url($mainImageDownloadUrl)['path'], strlen(explode('/', parse_url($mainImageDownloadUrl)['path'])[1])+1) ;
			} else {
				$imageKey = $imagefieldName . 'imageinfo';
				$mainImageFileName = $data[$imageKey]['name'];
				// $mainImageFileName = $watermarkPrefix . $data[$imageKey]['name'];
				$mainImageFileType = $data[$imageKey]['type'];
				$mainImageFileRecordId = $data[$imageKey]['id'];
				$mainImageFolderPath = $data[$imageKey]['path'];
				$mainImageUrl = $data[$imageKey]['fullpath'];
				$mainImageUniqueFileName = explode('/', $mainImageUrl)[count(explode('/', $mainImageUrl))-1];
				$mainImagePath = $root_directory . $mainImageFolderPath . $mainImageUniqueFileName;
			}
			$mainImageFileType = strpos($mainImageFileType, '/') !== false ? explode('/', $mainImageFileType)[1] : $mainImageFileType;

			// create image objects
			$waterMarkImg = null;
			$mainImage = null;
			$mainImageOriginal = null;
			$invalidImage = false;
			if ($waterMarkType == 'png') {
				$waterMarkImg = imagecreatefrompng($waterMarkUrl);
				if (!$waterMarkImg) {
					$logbg->debug('(Watermark) The watermark image was not created');
					$invalidImage = true;
				}
			} elseif ($waterMarkType == 'jpg' || $waterMarkType == 'jpeg') {
				$waterMarkImg = imagecreatefromjpeg($waterMarkUrl);
				if (!$waterMarkImg) {
					$logbg->debug('(Watermark) The watermark image was not created ');
					$invalidImage = true;
				}
			} else {
				$logbg->debug('(Watermark) cannot create the watermark image because extension is not supported');
				$invalidImage = true;
			}
			if ($mainImageFileType == 'png') {
				$mainImage = imagecreatefrompng($mainImagePath);
				if (!$mainImage) {
					$logbg->debug('(Watermark) The main image was not created');
					$invalidImage = true;
				}
				$mainImageOriginal = imagecreatefrompng($mainImagePath);
			} elseif ($mainImageFileType == 'jpg' || $mainImageFileType == 'jpeg') {
				$mainImage = imagecreatefromjpeg($mainImagePath);
				if (!$mainImage) {
					$logbg->debug('(Watermark) The main image was not created');
					$invalidImage = true;
				}
				$mainImageOriginal = imagecreatefromjpeg($mainImagePath);
			} else {
				$logbg->debug('(Watermark) cannot create the main image because extension is not supported');
				$invalidImage = true;
			}

			if (!$invalidImage) {
				// Get the height/width of the watermark and main image
				$wmsx = imagesx($waterMarkImg);
				$wmsy = imagesy($waterMarkImg);
				$misx = imagesx($mainImage);
				$misy = imagesy($mainImage);

				// set the size of the watermark image
				$wmImageAspectRatio = $wmsx / $wmsy;
				$mainImageAspectRatio = $misx / $misy;
				$mainImageAspectRatioType = $wmImageAspectRatio <= $mainImageAspectRatio ? 'vertical' : 'horizontal';

				// water mark image options
				$wmSize = (float)$wmsize * 0.01;
				$position = $wmPosition;

				$wmnsx = null;
				$wmnsy = null;
				switch ($mainImageAspectRatioType) {
					case 'horizontal':
						$wmnsx = $misx * $wmSize;
						$wmnsy = ($wmnsx / $wmImageAspectRatio);
						break;
					case 'vertical':
						$wmnsy = $misy * $wmSize;
						$wmnsx = ($wmnsy * $wmImageAspectRatio);
						break;
				}

				$wmpx = null;
				$wmpy = null;
				switch ($position) {
					case 'center':
						$wmpx = $misx / 2 - ($wmnsx / 2);
						$wmpy = $misy / 2 - ($wmnsy / 2);
						break;
					case 'top':
						$wmpx = $misx / 2 - ($wmnsx / 2);
						$wmpy = 0;
						break;
					case 'bottom':
						$wmpx = $misx / 2 - ($wmnsx / 2);
						$wmpy = $misy - $wmnsy;
						break;
					case 'right':
						$wmpx = $misx - $wmnsx;
						$wmpy = $misy / 2 - ($wmnsy / 2);
						break;
					case 'left':
						$wmpx = 0;
						$wmpy = $misy / 2 - ($wmnsy / 2);
						break;
					case 'topright':
						$wmpx = $misx - $wmnsx;
						$wmpy = 0;
						break;
					case 'topleft':
						$wmpx = 0;
						$wmpy = 0;
						break;
					case 'bottomleft':
						$wmpx = 0;
						$wmpy = $misy - $wmnsy;
						break;
					case 'bottomright':
						$wmpx = $misx - $wmnsx;
						$wmpy = $misy - $wmnsy;
						break;
					default:
						$logbg->debug('(Watermark) the watermark position you specified is not supported. we use bottom right');
						$wmpx = $misx - $wmnsx;
						$wmpy = $misy - $wmnsy;
						break;
				}

				// resize the watermark image
				$logbg->debug('(Watermark) adding the watermark to the image');
				$waterMarkImgAfterResize = imagecreatetruecolor($wmnsx, $wmnsy);
				imagesavealpha($waterMarkImgAfterResize, true);
				$color = imagecolorallocatealpha($waterMarkImgAfterResize, 0, 0, 0, 127);
				imagefill($waterMarkImgAfterResize, 0, 0, $color);
				imagecopyresampled($waterMarkImgAfterResize, $waterMarkImg, 0, 0, 0, 0, $wmnsx, $wmnsy, $wmsx, $wmsy);

				// add the watermark to the image
				$res = imagecopy($mainImage, $waterMarkImgAfterResize, $wmpx, $wmpy, 0, 0, $wmnsx, $wmnsy);
				if (!$res) {
					$logbg->debug('(Watermark) could not add the watermark on the image');
				}

				// Save image
				$mainOriginalImagePath = $mainImagePath . '_ORIGINAL';
				if ($mainImageFileType == 'png') {
					imagepng($mainImage, $mainImagePath);
					imagepng($mainImageOriginal, $mainOriginalImagePath);
				} elseif ($mainImageFileType == 'jpg' || $mainImageFileType == 'jpeg') {
					imagejpeg($mainImage, $mainImagePath);
					imagejpeg($mainImageOriginal, $mainOriginalImagePath);
				} else {
					$logbg->debug('(Watermark) cannot save the main image because the extension is not supported');
				}

				// free the images
				imagedestroy($waterMarkImgAfterResize);
				imagedestroy($waterMarkImg);
				imagedestroy($mainImageOriginal);
				imagedestroy($mainImage);
			}
		}
		$_REQUEST['ajxaction'] = $hold_ajxaction;
		$from_wf = false;
		$logbg->debug('< Watermark');
	}
}
?>