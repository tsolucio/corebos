<?php
//============================================================+
// File name   : c128aobject.php
// Begin       : 2002-07-31
// Last Update : 2004-12-29
// Author      : Karim Mribti [barcode@mribti.com]
// Version     : 0.0.8a  2001-04-01 (original code)
// License     : GNU LGPL (Lesser General Public License) 2.1
//               http://www.gnu.org/copyleft/lesser.txt
// Source Code : http://www.mribti.com/barcode/
//
// Description : Code 128-A Barcode Render Class for PHP using
//               the GD graphics library.
//               Code 128-A is a continuous, multilevel and
//               include all upper case alphanumeric characters
//               and ASCII control characters.
//
// NOTE:
// This version contains changes by Nicola Asuni:
//  - porting to PHP5
//  - code style and formatting
//  - automatic php documentation in PhpDocumentor Style
//    (www.phpdoc.org)
//  - minor bug fixing
//============================================================+

/**
 * Code 128-A Barcode Render Class for PHP using the GD graphics library.<br>
 * Code 128-A is a continuous, multilevel and include all upper case alphanumeric characters and ASCII control characters.
 * @author Karim Mribti, Nicola Asuni
 * @name BarcodeObject
 * @package com.tecnick.tcpdf
 * @version 0.0.8a  2001-04-01 (original code)
 * @since 2001-03-25
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 */

/**
 * Code 128-A Barcode Render Class for PHP using the GD graphics library.<br>
 * Code 128-A is a continuous, multilevel and include all upper case alphanumeric characters and ASCII control characters.
 * @author Karim Mribti, Nicola Asuni
 * @name BarcodeObject
 * @package com.tecnick.tcpdf
 * @version 0.0.8a  2001-04-01 (original code)
 * @since 2001-03-25
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 */
class C128AObject extends BarcodeObject {
	
	/**
	 * Class Constructor.
	 * @param int $Width Image width in pixels.
	 * @param int $Height Image height in pixels. 
	 * @param int $Style Barcode style.
	 * @param int $Value value to print on barcode.
	 */
	public function __construct($Width, $Height, $Style, $Value) {
		parent::__construct($Width, $Height, $Style);
		$this->mValue   = $Value;
		$this->mChars   = " !\"#$%&'()*+�-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_";
		$this->mCharSet = array (
		"212222",   /*   00 */
		"222122",   /*   01 */
		"222221",   /*   02 */
		"121223",   /*   03 */
		"121322",   /*   04 */
		"131222",   /*   05 */
		"122213",   /*   06 */
		"122312",   /*   07 */
		"132212",   /*   08 */
		"221213",   /*   09 */
		"221312",   /*   10 */
		"231212",   /*   11 */
		"112232",   /*   12 */
		"122132",   /*   13 */
		"122231",   /*   14 */
		"113222",   /*   15 */
		"123122",   /*   16 */
		"123221",   /*   17 */
		"223211",   /*   18 */
		"221132",   /*   19 */
		"221231",   /*   20 */
		"213212",   /*   21 */
		"223112",   /*   22 */
		"312131",   /*   23 */
		"311222",   /*   24 */
		"321122",   /*   25 */
		"321221",   /*   26 */
		"312212",   /*   27 */
		"322112",   /*   28 */
		"322211",   /*   29 */
		"212123",   /*   30 */
		"212321",   /*   31 */
		"232121",   /*   32 */
		"111323",   /*   33 */
		"131123",   /*   34 */
		"131321",   /*   35 */
		"112313",   /*   36 */
		"132113",   /*   37 */
		"132311",   /*   38 */
		"211313",   /*   39 */
		"231113",   /*   40 */
		"231311",   /*   41 */
		"112133",   /*   42 */
		"112331",   /*   43 */
		"132131",   /*   44 */
		"113123",   /*   45 */
		"113321",   /*   46 */
		"133121",   /*   47 */
		"313121",   /*   48 */
		"211331",   /*   49 */
		"231131",   /*   50 */
		"213113",   /*   51 */
		"213311",   /*   52 */
		"213131",   /*   53 */
		"311123",   /*   54 */
		"311321",   /*   55 */
		"331121",   /*   56 */
		"312113",   /*   57 */
		"312311",   /*   58 */
		"332111",   /*   59 */
		"314111",   /*   60 */
		"221411",   /*   61 */
		"431111",   /*   62 */
		"111224",   /*   63 */
		"111422",   /*   64 */
		"121124",   /*   65 */
		"121421",   /*   66 */
		"141122",   /*   67 */
		"141221",   /*   68 */
		"112214",   /*   69 */
		"112412",   /*   70 */
		"122114",   /*   71 */
		"122411",   /*   72 */
		"142112",   /*   73 */
		"142211",   /*   74 */
		"241211",   /*   75 */
		"221114",   /*   76 */
		"413111",   /*   77 */
		"241112",   /*   78 */
		"134111",   /*   79 */
		"111242",   /*   80 */
		"121142",   /*   81 */
		"121241",   /*   82 */
		"114212",   /*   83 */
		"124112",   /*   84 */
		"124211",   /*   85 */
		"411212",   /*   86 */
		"421112",   /*   87 */
		"421211",   /*   88 */
		"212141",   /*   89 */
		"214121",   /*   90 */
		"412121",   /*   91 */
		"111143",   /*   92 */
		"111341",   /*   93 */
		"131141",   /*   94 */
		"114113",   /*   95 */
		"114311",   /*   96 */
		"411113",   /*   97 */
		"411311",   /*   98 */
		"113141",   /*   99 */
		"114131",   /*  100 */
		"311141",   /*  101 */
		"411131"    /*  102 */
		);
	}

	/**
	 * Returns the character index.
	 * @param char $char character.
	 * @return int character index or -1 in case of error.
	 * @access private
	 */
	private function GetCharIndex($char) {
		for ($i=0;$i<64;$i++) {
			if ($this->mChars[$i] == $char) {
				return $i;
			}
		}
		return -1;
	}
	
	/**
	 * Returns the bar size.
	 * @param int $xres Horizontal resolution.
	 * @param char $char Character.
	 * @return int barcode size.
	 * @access private
	 */
	private function GetBarSize($xres, $char) {
		switch ($char) {
			case '1': {
				$cVal = BCD_C128_BAR_1;
				break;
			}
			case '2': {
				$cVal = BCD_C128_BAR_2;
				break;
			}
			case '3': {
				$cVal = BCD_C128_BAR_3;
				break;
			}
			case '4': {
				$cVal = BCD_C128_BAR_4;
				break;
			}
			default: {
				$cVal = 0;
			}
		}
		return  $cVal * $xres;
	}
	
	/**
	 * Returns barcode size.
	 * @param int $xres Horizontal resolution.
	 * @return barcode size.
	 * @access private
	 */
	private function GetSize($xres) {
		$len = strlen($this->mValue);

		if ($len == 0)  {
			$this->mError = "Null value";
			return false;
		}
		$ret = 0;
		for ($i=0;$i<$len;$i++) {
			if (($id = $this->GetCharIndex($this->mValue[$i])) == -1) {
				$this->mError = "C128A not include the char '".$this->mValue[$i]."'";
				return false;
			} else {
				$cset = $this->mCharSet[$id];
				$ret += $this->GetBarSize($xres, $cset[0]);
				$ret += $this->GetBarSize($xres, $cset[1]);
				$ret += $this->GetBarSize($xres, $cset[2]);
				$ret += $this->GetBarSize($xres, $cset[3]);
				$ret += $this->GetBarSize($xres, $cset[4]);
				$ret += $this->GetBarSize($xres, $cset[5]);
			}
		}

		/* length of Check character */
		$cset = $this->GetCheckCharValue();
		$CheckSize = 0;
		for ($i=0;$i<6;$i++) {
			$CheckSize += $this->GetBarSize($cset[$i], $xres);
		}
		$StartSize = 2*BCD_C128_BAR_2*$xres + 3*BCD_C128_BAR_1*$xres + BCD_C128_BAR_4*$xres;
		$StopSize  = 2*BCD_C128_BAR_2*$xres + 3*BCD_C128_BAR_1*$xres + 2*BCD_C128_BAR_3*$xres;
		return $StartSize + $ret + $CheckSize + $StopSize;
	}
	
	/**
	 * Returns the check-char value.
	 * @return string.
	 * @access private
	 */
	private function GetCheckCharValue() {
		$len = strlen($this->mValue);
		$sum = 103; // 'A' type;
		for ($i=0;$i<$len;$i++) {
			$sum +=  $this->GetCharIndex($this->mValue[$i]) * ($i+1);
		}
		$check  = $sum % 103;
		return $this->mCharSet[$check];
	}

	/**
	 * Draws the start code.
	 * @param int $DrawPos Drawing position.
	 * @param int $yPos Vertical position.
	 * @param int $ySize Vertical size.
	 * @param int $xres Horizontal resolution.
	 * @return int drawing position.
	 * @access private
	 */
	private function DrawStart($DrawPos, $yPos, $ySize, $xres) {
		/* Start code is '211412' */
		$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize('2', $xres) , $ySize);
		$DrawPos += $this->GetBarSize('2', $xres);
		$DrawPos += $this->GetBarSize('1', $xres);
		$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize('1', $xres) , $ySize);
		$DrawPos += $this->GetBarSize('1', $xres);
		$DrawPos += $this->GetBarSize('4', $xres);
		$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize('1', $xres) , $ySize);
		$DrawPos += $this->GetBarSize('1', $xres);
		$DrawPos += $this->GetBarSize('2', $xres);
		return $DrawPos;
	}
	
	/**
	 * Draws the stop code.
	 * @param int $DrawPos Drawing position.
	 * @param int $yPos Vertical position.
	 * @param int $ySize Vertical size.
	 * @param int $xres Horizontal resolution.
	 * @return int drawing position.
	 * @access private
	 */
	private function DrawStop($DrawPos, $yPos, $ySize, $xres) {
		/* Stop code is '2331112' */
		$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize('2', $xres) , $ySize);
		$DrawPos += $this->GetBarSize('2', $xres);
		$DrawPos += $this->GetBarSize('3', $xres);
		$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize('3', $xres) , $ySize);
		$DrawPos += $this->GetBarSize('3', $xres);
		$DrawPos += $this->GetBarSize('1', $xres);
		$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize('1', $xres) , $ySize);
		$DrawPos += $this->GetBarSize('1', $xres);
		$DrawPos += $this->GetBarSize('1', $xres);
		$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize('2', $xres) , $ySize);
		$DrawPos += $this->GetBarSize('2', $xres);
		return $DrawPos;
	}
	
	/**
	 * Draws the check-char code.
	 * @param int $DrawPos Drawing position.
	 * @param int $yPos Vertical position.
	 * @param int $ySize Vertical size.
	 * @param int $xres Horizontal resolution.
	 * @return int drawing position.
	 * @access private
	 */
	private function DrawCheckChar($DrawPos, $yPos, $ySize, $xres) {
		$cset = $this->GetCheckCharValue();
		$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize($cset[0], $xres) , $ySize);
		$DrawPos += $this->GetBarSize($cset[0], $xres);
		$DrawPos += $this->GetBarSize($cset[1], $xres);
		$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize($cset[2], $xres) , $ySize);
		$DrawPos += $this->GetBarSize($cset[2], $xres);
		$DrawPos += $this->GetBarSize($cset[3], $xres);
		$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize($cset[4], $xres) , $ySize);
		$DrawPos += $this->GetBarSize($cset[4], $xres);
		$DrawPos += $this->GetBarSize($cset[5], $xres);
		return $DrawPos;
	}

	/**
	 * Draws the barcode object.
	 * @param int $xres Horizontal resolution.
	 * @return bool true in case of success.
	 */
	public function DrawObject($xres) {
		$len = strlen($this->mValue);
		if (($size = $this->GetSize($xres))==0) {
			return false;
		}

		if ($this->mStyle & BCS_ALIGN_CENTER) $sPos = (integer)(($this->mWidth - $size ) / 2);
		else if ($this->mStyle & BCS_ALIGN_RIGHT) $sPos = $this->mWidth - $size;
		else $sPos = 0;

		/* Total height of bar code -Bars only- */
		if ($this->mStyle & BCS_DRAW_TEXT) $ysize = $this->mHeight - BCD_DEFAULT_MAR_Y1 - BCD_DEFAULT_MAR_Y2 - $this->GetFontHeight($this->mFont);
		else $ysize = $this->mHeight - BCD_DEFAULT_MAR_Y1 - BCD_DEFAULT_MAR_Y2;

		/* Draw text */
		if ($this->mStyle & BCS_DRAW_TEXT) {
			if ($this->mStyle & BCS_STRETCH_TEXT) {
				for ($i=0;$i<$len;$i++) {
					$this->DrawChar($this->mFont, $sPos+(2*BCD_C128_BAR_2*$xres + 3*BCD_C128_BAR_1*$xres + BCD_C128_BAR_4*$xres)+($size/$len)*$i,
					$ysize + BCD_DEFAULT_MAR_Y1 + BCD_DEFAULT_TEXT_OFFSET, $this->mValue[$i]);
				}
			} else {/* Center */
			$text_width = $this->GetFontWidth($this->mFont) * strlen($this->mValue);
			$this->DrawText($this->mFont, $sPos+(($size-$text_width)/2)+(2*BCD_C128_BAR_2*$xres + 3*BCD_C128_BAR_1*$xres + BCD_C128_BAR_4*$xres),
			$ysize + BCD_DEFAULT_MAR_Y1 + BCD_DEFAULT_TEXT_OFFSET, $this->mValue);
			}
		}

		$cPos = 0;
		$DrawPos = $this->DrawStart($sPos, BCD_DEFAULT_MAR_Y1 , $ysize, $xres);
		do {
			$c     = $this->GetCharIndex($this->mValue[$cPos]);
			$cset  = $this->mCharSet[$c];
			$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize($cset[0], $xres) , $ysize);
			$DrawPos += $this->GetBarSize($cset[0], $xres);
			$DrawPos += $this->GetBarSize($cset[1], $xres);
			$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize($cset[2], $xres) , $ysize);
			$DrawPos += $this->GetBarSize($cset[2], $xres);
			$DrawPos += $this->GetBarSize($cset[3], $xres);
			$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, $this->GetBarSize($cset[4], $xres) , $ysize);
			$DrawPos += $this->GetBarSize($cset[4], $xres);
			$DrawPos += $this->GetBarSize($cset[5], $xres);
			$cPos++;
		} while ($cPos<$len);
		$DrawPos = $this->DrawCheckChar($DrawPos, BCD_DEFAULT_MAR_Y1 , $ysize, $xres);
		$DrawPos =  $this->DrawStop($DrawPos, BCD_DEFAULT_MAR_Y1 , $ysize, $xres);
		return true;
	}
}

//============================================================+
// END OF FILE
//============================================================+
?>