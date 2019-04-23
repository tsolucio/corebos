<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once __DIR__ . '/../../../include/tcpdf/config/lang/eng.php';
require_once __DIR__ . '/../../../include/tcpdf/tcpdf.php';

class Vtiger_PDF_TCPDF extends TCPDF {

	protected $FontFamily;

	public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false) {
		parent::__construct($orientation, $unit, $format, $unicode, $encoding);
		$this->SetFont('', '', 10);
		$this->setFontFamily('times');
	}

	public function getFontSize() {
		return $this->FontSizePt;
	}

	public function setFontFamily($family) {
		$this->FontFamily = $family;
	}

	public function GetStringHeight($sa, $w, $reseth = false, $autopadding = true, $cellpadding = '', $border = 0) {
		if (empty($sa)) {
			return 0;
		}

		$sa = str_replace("\r", '', $sa);
		// remove the last newline
		if (substr($sa, -1) == "\n") {
			$sa = substr($sa, 0, -1);
		}

		$blocks = explode("\n", $sa);
		$cMargin = $this->getCellPaddings();
		$wmax = $w - (2 * $cMargin['R']);

		$lines = 0;
		$spacesize = $this->GetCharWidth(32);
		foreach ($blocks as $block) {
			if (!empty($block)) {
				$words = explode(" ", $block);

				$cw = 0;
				for ($i = 0; $i < count($words); $i++) {
					if ($i != 0) {
						$cw += $spacesize;
					}

					$wordwidth = $this->GetStringWidth($words[$i]);
					$cw += $wordwidth;

					if ($cw > $wmax) { // linebreak
						$cw = $wordwidth;
						$lines++;
					}
				}
			}
			$lines++;
		}
		return ($lines * ($this->FontSize * $this->cell_height_ratio)) + 2;
	}

	public function SetFont($family, $style = '', $size = null, $fontfile = '', $subset = 'default', $out = true) {
		if ($family == '') {
			$family = $this->FontFamily;
		}
		//Select a font; size given in points
		if ($size == 0) {
			$size = $this->FontSizePt;
		}
		// try to add font (if not already added)
		$fontdata =  $this->AddFont($family, $style);
		$this->FontFamily = $fontdata['family'];
		$this->FontStyle = $fontdata['style'];
		$this->CurrentFont = &$this->fonts[$fontdata['fontkey']];
		$this->SetFontSize($size);
	}
}
?>