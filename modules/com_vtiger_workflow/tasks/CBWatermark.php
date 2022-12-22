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

	public function getFieldNames() {
		return array('imagesvalue', 'imagesx', 'imagesy', 'exptype');
	}

	public function doTask(&$entity) {
		global $current_user, $logbg, $from_wf, $currentModule, $root_directory;
		$from_wf = true;
		$logbg->debug('> CBWatermark');
		$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
		$_REQUEST['ajxaction'] = 'Workflow';
		if (!empty($this->imagesvalue)) {
			$watermark = '';
			if ($this->exptype == 'rawtext') {
				$watermark = $this->imagesvalue;
			} elseif ($this->exptype == 'fieldname') {
				$util = new VTWorkflowUtils();
				$adminUser = $util->adminUser();
				$entityCache = new VTEntityCache($adminUser);
				$fn = new VTSimpleTemplate($this->imagesvalue);
				$watermark = $fn->render($entityCache, $entity->getId(), [], $entity->WorkflowContext);
			} else {
				$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($this->imagesvalue)));
				$expression = $parser->expression();
				$exprEvaluater = new VTFieldExpressionEvaluater($expression);
				$watermark = $exprEvaluater->evaluate($entity);
			}
			$waterMarkUrl = $watermark;
			$waterMarkArr = explode('/', $waterMarkUrl);
			$waterMarkDotArr = explode('.', $waterMarkUrl);
			$waterMarkName = $waterMarkArr[count($waterMarkArr) - 1];
			$waterMarkType = $waterMarkDotArr[count($waterMarkDotArr) - 1];
			$waterMarkSavedPath = $root_directory . 'storage/';
			$data = $entity->getData();
			$mainImageFileName = $data['filename'];
			$mainImageFileType = $data['filetype'];
			$mainImageFileSize = $data['filesize'];
			$mainImageDownloadUrl = $data['_downloadurl'];
			$mainImagePath = substr($root_directory, 0, strlen($root_directory) - 9) . parse_url($mainImageDownloadUrl)['path'];

			// create image objects
			$waterMarkImg = null;
			$mainImage = null;
			if ($waterMarkType == 'png') {
				$waterMarkImg = imagecreatefrompng($waterMarkUrl);
			} elseif ($mainImageFileType == 'jpg' || $mainImageFileType == 'jpeg') {
				$waterMarkImg = imagecreatefrompng($waterMarkUrl);
			}
			if ($mainImageFileType == 'image/png') {
				$mainImage = imagecreatefrompng($mainImagePath);
			} elseif ($mainImageFileType == 'image/jpg' || $mainImageFileType == 'image/jpeg') {
				$mainImage = imagecreatefromjpeg($mainImagePath);
			}

			// Get the height/width of the watermark and main image
			$wmsx = imagesx($waterMarkImg);
			$wmsy = imagesy($waterMarkImg);
			$misx = imagesx($mainImage);
			$misy = imagesy($mainImage);
			// set the size of the watermark image
			$wmImageAspectRatio = $wmsx / $wmsy;
			$mainImageAspectRatio = $misx / $misy;
			$mainImageAspectRatioType = $wmImageAspectRatio < $mainImageAspectRatio ? 'vertical' : 'horizontal';

			// water mark image options
			$wmSize = (float)$this->imagesx * 0.01;
			$position = $this->imagesy;

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
					//do nothing
					break;
			}

			// resize the watermark image
			$waterMarkImgAfterResize = imagecreatetruecolor($wmnsx, $wmnsy);
			imagesavealpha($waterMarkImgAfterResize, true);
			$color = imagecolorallocatealpha($waterMarkImgAfterResize, 0, 0, 0, 127);
			imagefill($waterMarkImgAfterResize, 0, 0, $color);
			imagecopyresampled($waterMarkImgAfterResize, $waterMarkImg, 0, 0, 0, 0, $wmnsx, $wmnsy, $wmsx, $wmsy);

			// add the watermark to the image
			imagecopy($mainImage, $waterMarkImgAfterResize, $wmpx, $wmpy, 0, 0, $wmnsx, $wmnsy);

			// Save image and free memory
			if ($mainImageFileType == 'image/png') {
				imagepng($mainImage, $mainImagePath);
			} elseif ($mainImageFileType == 'image/jpg' || $mainImageFileType == 'image/jpeg') {
				imagejpeg($mainImage, $mainImagePath);
			}

			// free the images
			imagedestroy($waterMarkImgAfterResize);
			imagedestroy($waterMarkImg);
			imagedestroy($mainImage);
		}
		$_REQUEST['ajxaction'] = $hold_ajxaction;
		$from_wf = false;
		$logbg->debug('< CBWatermark');
	}
}
?>