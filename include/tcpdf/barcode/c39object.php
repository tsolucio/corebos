<?php
//============================================================+
// File name   : c39object.php
// Begin       : 2002-07-31
// Last Update : 2004-12-29
// Author      : Karim Mribti [barcode@mribti.com]
//             : Nicola Asuni [info@tecnick.com]
// Version     : 0.0.8a  2001-04-01 (original code)
// License     : GNU LGPL (Lesser General Public License) 2.1
//               http://www.gnu.org/copyleft/lesser.txt
// Source Code : http://www.mribti.com/barcode/
//
// Description : Code 39 Barcode Render Class for PHP using
//               the GD graphics library.
//               Code 39 is an alphanumeric bar code that can
//               encode decimal number, case alphabet and some
//               special symbols.
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
 * Code 39 Barcode Render Class.<br>
 * Code 39 is an alphanumeric bar code that can encode decimal number, case alphabet and some special symbols.
 * @author Karim Mribti, Nicola Asuni
 * @name BarcodeObject
 * @package com.tecnick.tcpdf
 * @version 0.0.8a  2001-04-01 (original code)
 * @since 2001-03-25
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 */

/**
 * Code 39 Barcode Render Class.<br>
 * Code 39 is an alphanumeric bar code that can encode decimal number, case alphabet and some special symbols.
 * @author Karim Mribti, Nicola Asuni
 * @name BarcodeObject
 * @package com.tecnick.tcpdf
 * @version 0.0.8a  2001-04-01 (original code)
 * @since 2001-03-25
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 */
class C39Object extends BarcodeObject {
	
	/**
	 * Class Constructor.
	 * @param int $Width Image width in pixels.
	 * @param int $Height Image height in pixels. 
	 * @param int $Style Barcode style.
	 * @param int $Value value to print on barcode.
	 */
	public function __construct($Width, $Height, $Style, $Value) {
		parent::__construct($Width, $Height, $Style);
		$this->mValue = $Value;
		$this->mChars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-. *$/+%";
		$this->mCharSet = array (
		/* 0  */ "000110100",
		/* 1  */ "100100001",
		/* 2  */ "001100001",
		/* 3  */ "101100000",
		/* 4  */ "000110001",
		/* 5  */ "100110000",
		/* 6  */ "001110000",
		/* 7  */ "000100101",
		/* 8  */ "100100100",
		/* 9  */ "001100100",
		/* A  */ "100001001",
		/* B  */ "001001001",
		/* C  */ "101001000",
		/* D  */ "000011001",
		/* E  */ "100011000",
		/* F  */ "001011000",
		/* G  */ "000001101",
		/* H  */ "100001100",
		/* I  */ "001001100",
		/* J  */ "000011100",
		/* K  */ "100000011",
		/* L  */ "001000011",
		/* M  */ "101000010",
		/* N  */ "000010011",
		/* O  */ "100010010",
		/* P  */ "001010010",
		/* Q  */ "000000111",
		/* R  */ "100000110",
		/* S  */ "001000110",
		/* T  */ "000010110",
		/* U  */ "110000001",
		/* V  */ "011000001",
		/* W  */ "111000000",
		/* X  */ "010010001",
		/* Y  */ "110010000",
		/* Z  */ "011010000",
		/* -  */ "010000101",
		/* .  */ "110000100",
		/* SP */ "011000100",
		/* *  */ "010010100",
		/* $  */ "010101000",
		/* /  */ "010100010",
		/* +  */ "010001010",
		/* %  */ "000101010"
		);
	}

	/**
	 * Returns the character index.
	 * @param char $char character.
	 * @return int character index or -1 in case of error.
	 * @access private
	 */
	private function GetCharIndex($char) {
		for ($i=0;$i<44;$i++) {
			if ($this->mChars[$i] == $char) {
				return $i;
			}
		}
		return -1;
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

		for ($i=0;$i<$len;$i++) {
			if ($this->GetCharIndex($this->mValue[$i]) == -1 || $this->mValue[$i] == '*') {
				/* The asterisk is only used as a start and stop code */
				$this->mError = "C39 not include the char '".$this->mValue[$i]."'";
				return false;
			}
		}

		/* Start, Stop is 010010100 == '*'  */
		$StartSize = BCD_C39_NARROW_BAR * $xres * 6 + BCD_C39_WIDE_BAR * $xres * 3;
		$StopSize  = BCD_C39_NARROW_BAR * $xres * 6 + BCD_C39_WIDE_BAR * $xres * 3;
		$CharSize  = BCD_C39_NARROW_BAR * $xres * 6 + BCD_C39_WIDE_BAR * $xres * 3; /* Same for all chars */

		return $CharSize * $len + $StartSize + $StopSize + /* Space between chars */ BCD_C39_NARROW_BAR * $xres * ($len-1);
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
		/* Start code is '*' */
		$narrow = BCD_C39_NARROW_BAR * $xres;
		$wide   = BCD_C39_WIDE_BAR * $xres;
		$this->DrawSingleBar($DrawPos, $yPos, $narrow , $ySize);
		$DrawPos += $narrow;
		$DrawPos += $wide;
		$this->DrawSingleBar($DrawPos, $yPos, $narrow , $ySize);
		$DrawPos += $narrow;
		$DrawPos += $narrow;
		$this->DrawSingleBar($DrawPos, $yPos, $wide , $ySize);
		$DrawPos += $wide;
		$DrawPos += $narrow;
		$this->DrawSingleBar($DrawPos, $yPos, $wide , $ySize);
		$DrawPos += $wide;
		$DrawPos += $narrow;
		$this->DrawSingleBar($DrawPos, $yPos, $narrow, $ySize);
		$DrawPos += $narrow;
		$DrawPos += $narrow; /* Space between chars */
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
		/* Stop code is '*' */
		$narrow = BCD_C39_NARROW_BAR * $xres;
		$wide   = BCD_C39_WIDE_BAR * $xres;
		$this->DrawSingleBar($DrawPos, $yPos, $narrow , $ySize);
		$DrawPos += $narrow;
		$DrawPos += $wide;
		$this->DrawSingleBar($DrawPos, $yPos, $narrow , $ySize);
		$DrawPos += $narrow;
		$DrawPos += $narrow;
		$this->DrawSingleBar($DrawPos, $yPos, $wide , $ySize);
		$DrawPos += $wide;
		$DrawPos += $narrow;
		$this->DrawSingleBar($DrawPos, $yPos, $wide , $ySize);
		$DrawPos += $wide;
		$DrawPos += $narrow;
		$this->DrawSingleBar($DrawPos, $yPos, $narrow, $ySize);
		$DrawPos += $narrow;
		return $DrawPos;
	}
	
	/**
	 * Draws the barcode object.
	 * @param int $xres Horizontal resolution.
	 * @return bool true in case of success.
	 */
	public function DrawObject($xres) {
		$len = strlen($this->mValue);

		$narrow = BCD_C39_NARROW_BAR * $xres;
		$wide   = BCD_C39_WIDE_BAR * $xres;

		if (($size = $this->GetSize($xres))==0) {
			return false;
		}

		$cPos = 0;
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
					$this->DrawChar($this->mFont, $sPos+($narrow*6+$wide*3)+($size/$len)*$i,
					$ysize + BCD_DEFAULT_MAR_Y1 + BCD_DEFAULT_TEXT_OFFSET, $this->mValue[$i]);
				}
			} else {/* Center */
			$text_width = $this->GetFontWidth($this->mFont) * strlen($this->mValue);
			$this->DrawText($this->mFont, $sPos+(($size-$text_width)/2)+($narrow*6+$wide*3),
			$ysize + BCD_DEFAULT_MAR_Y1 + BCD_DEFAULT_TEXT_OFFSET, $this->mValue);
			}
		}

		$DrawPos = $this->DrawStart($sPos, BCD_DEFAULT_MAR_Y1 , $ysize, $xres);
		do {
			$c     = $this->GetCharIndex($this->mValue[$cPos]);
			$cset  = $this->mCharSet[$c];
			$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, ($cset[0] == '0') ? $narrow : $wide , $ysize);
			$DrawPos += ($cset[0] == '0') ? $narrow : $wide;
			$DrawPos += ($cset[1] == '0') ? $narrow : $wide;
			$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, ($cset[2] == '0') ? $narrow : $wide , $ysize);
			$DrawPos += ($cset[2] == '0') ? $narrow : $wide;
			$DrawPos += ($cset[3] == '0') ? $narrow : $wide;
			$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, ($cset[4] == '0') ? $narrow : $wide , $ysize);
			$DrawPos += ($cset[4] == '0') ? $narrow : $wide;
			$DrawPos += ($cset[5] == '0') ? $narrow : $wide;
			$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, ($cset[6] == '0') ? $narrow : $wide , $ysize);
			$DrawPos += ($cset[6] == '0') ? $narrow : $wide;
			$DrawPos += ($cset[7] == '0') ? $narrow : $wide;
			$this->DrawSingleBar($DrawPos, BCD_DEFAULT_MAR_Y1, ($cset[8] == '0') ? $narrow : $wide , $ysize);
			$DrawPos += ($cset[8] == '0') ? $narrow : $wide;
			$DrawPos += $narrow; /* Space between chars */
			$cPos++;
		} while ($cPos<$len);
		$DrawPos =  $this->DrawStop($DrawPos, BCD_DEFAULT_MAR_Y1 , $ysize, $xres);
		return true;
	}
}

//============================================================+
// END OF FILE
//============================================================+
?>