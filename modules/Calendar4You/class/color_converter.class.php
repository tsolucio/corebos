<?php
/*
this class is useful to convert HEX, RGB and HSL color codes, one to anothers
author: usman didi khamdani
author's email: usmankhamdani@gmail.com
author's phone: +6287883919293
last updated: Sept 18, 2010
*/

class colorConverter {

	// convert dec to hex
	public function Hexa($dec) {
		if ($dec<0 || $dec>255) {
			return false;
		} else {
			$p = floor($dec/16);
			$s = $dec%16;
			return dechex($p).dechex($s);
		}
	}

	// convert hex to dec
	public function Deci($hex) {
		$HEXpar = preg_split('//', '0123456789abcdef', -1, PREG_SPLIT_NO_EMPTY);

		if (strlen($hex)==1) {
			if (in_array($hex, $HEXpar)) {
				$dec = hexdec($hex);
				return ($dec * 16) + $dec;
			} else {
				return false;
			}
		} elseif (strlen($hex)==2) {
			$hex1 = substr($hex, 0, 1);
			$hex2 = substr($hex, 1, 1);

			if (in_array($hex1, $HEXpar) && in_array($hex2, $HEXpar)) {
				$dec1 = hexdec($hex1);
				$dec2 = hexdec($hex2);
				return ($dec1 * 16) + $dec2;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	// check validity of RGB color code
	public function isRGB($R, $G, $B) {
		if ($this->Hexa($R)==false || $this->Hexa($G)==false || $this->Hexa($B)==false) {
			$this->RGBError = 1;

			if ($this->Hexa($R)==false) {
				$Re = 'Minimal value of Red color is 0 and maximal value is 255<br />';
			} else {
				$Re = '';
			}

			if ($this->Hexa($G)==false) {
				$Ge = 'Minimal value of Green color is 0 and maximal value is 255<br />';
			} else {
				$Ge = '';
			}

			if ($this->Hexa($B)==false) {
				$Be = 'Minimal value of Blue color is 0 and maximal value is 255<br />';
			} else {
				$Be = '';
			}

			$this->RGBErrorMessage = '<div><b>Error RGB Color Value!</b><br />'.$Re.$Ge.$Be.'</div>';
		} else {
			$this->RGBError = 0;
		}
	}

	// check validity of HEX color code
	public function isHEX($HEX) {
		$HEX = strtolower(str_replace('#', '', $HEX));
		$HEX = preg_split('//', $HEX, -1, PREG_SPLIT_NO_EMPTY);
		$HEXpar = preg_split('//', '0123456789abcdef', -1, PREG_SPLIT_NO_EMPTY);
		$HEXlen = count($HEX);

		/*
		// for css rule only
		if($HEXlen==3 || $HEXlen==6) {
			if($HEXlen==3) {
				if(in_array($HEX[0],$HEXpar) && in_array($HEX[1],$HEXpar) && in_array($HEX[2],$HEXpar)) {
					$this->HEXError = 0;
				} else {

					$this->HEXError = 1;
					$this->HEXErrorMessage = '<div><b>Error HEX Color Value!</b><br />Minimal value of each HEX color is 0 and maximal value is f</div>';

				}
			} else {
				if(in_array($HEX[0],$HEXpar) && in_array($HEX[1],$HEXpar) && in_array($HEX[2],$HEXpar) && in_array($HEX[3],$HEXpar) && in_array($HEX[4],$HEXpar) && in_array($HEX[5],$HEXpar)) {
					$this->HEXError = 0;
				} else {
					$this->HEXError = 1;
					$this->HEXErrorMessage = '<div><b>Error HEX Color Value!</b><br />Minimal value of each HEX color is 0 and maximal value is f</div>';
				}
			}
		///////////////////////////////////
		*/
		// for all rule, include css
		if ($HEXlen==6) {
			if (in_array($HEX[0], $HEXpar) && in_array($HEX[1], $HEXpar) && in_array($HEX[2], $HEXpar) && in_array($HEX[3], $HEXpar) && in_array($HEX[4], $HEXpar) && in_array($HEX[5], $HEXpar)) {
				$this->HEXError = 0;
			} else {
				$this->HEXError = 1;
				$this->HEXErrorMessage = '<div><b>Error HEX Color Value!</b><br />Minimal value of each HEX color is 0 and maximal value is f</div>';
			}
		///////////////////////////////////
		} else {
			$this->HEXError = 1;
			$this->HEXErrorMessage = '<div><b>Error HEX Color Value!</b><br />Lenght of Hex color must be 3 or 6 characters</div>';
		}
	}

	// check validity of HSL color code
	public function isHSL($H, $S, $L) {
		if (($H<0 || $S<0 || $L<0) || ($H>360 || $S>100 || $L>100)) {
			$this->HSLError = 1;

			if ($H<0 || $H>360) {
				$He = 'Minimal value of Hue color is 0 and maximal value is 360<br />';
			} else {
				$He = '';
			}
			if ($S<0 || $S>100) {
				$Se = 'Minimal value of Saturation color is 0 and maximal value is 100<br />';
			} else {
				$Se = '';
			}
			if ($L<0 || $L>100) {
				$Le = 'Minimal value of Lightness color is 0 and maximal value is 100';
			} else {
				$Le = '';
			}

			$this->HSLErrorMessage = '<div><b>Error HSL Color Value!</b><br />'.$He.$Se.$Le.'</div>';
		} else {
			$this->HSLError = 0;
		}
	}

	// convert RGB to HEX color
	public function RGB2HEX($R, $G, $B) {
		$this->isRGB($R, $G, $B);

		if ($this->RGBError==0) {
			$Rh = $this->Hexa($R);
			$Gh = $this->Hexa($G);
			$Bh = $this->Hexa($B);

			$Rh1 = substr($Rh, 0, 1);
			$Rh2 = substr($Rh, 1, 1);

			$Gh1 = substr($Gh, 0, 1);
			$Gh2 = substr($Gh, 1, 1);

			$Bh1 = substr($Bh, 0, 1);
			$Bh2 = substr($Bh, 1, 1);

			/*
			// for css rule only
			if(($Rh1==$Rh2) && ($Gh1==$Gh2) && ($Bh1==$Bh2)) {
				return '#'.$Rh1.$Gh1.$Bh1;
			} else {
				return '#'.$Rh.$Gh.$Bh;
			}
			*/
			return '#'.$Rh.$Gh.$Bh; // for all rule, include css
		}
	}

	// convert HEX to RGB color
	public function HEX2RGB($HEX) {
		$this->isHEX($HEX);

		if ($this->HEXError==0) {
			$HEX = str_replace('#', '', $HEX);

			if (strlen($HEX)==3) {
				$R = $this->Deci(substr($HEX, 0, 1));
				$G = $this->Deci(substr($HEX, 1, 1));
				$B = $this->Deci(substr($HEX, 2, 1));
			} else {
				$R = $this->Deci(substr($HEX, 0, 2));
				$G = $this->Deci(substr($HEX, 2, 2));
				$B = $this->Deci(substr($HEX, 4, 2));
			}

			return array($R,$G,$B);
		}
	}

	// convert RGB to HSL color; adapted from www.easyrgb.com
	public function RGB2HSL($R, $G, $B) {
		$this->isRGB($R, $G, $B);

		if ($this->RGBError==0) {
			$R = $R/255;
			$G = $G/255;
			$B = $B/255;

			$RGB = array($R,$G,$B);
			sort($RGB);

			$min = $RGB[0];
			$max = $RGB[2];
			$delta = $max-$min;

			$L = ($max+$min)/2;

			if ($delta==0) {
				$H = 0;
				$S = 0;
			} else {
				if ($L<0.5) {
					$S = $delta/($max+$min);
				} else {
					$S = $delta/(2-$max-$min);
				}

				$Rn = ((($max-$R)/6)+($delta/2))/$delta;
				$Gn = ((($max-$G)/6)+($delta/2))/$delta;
				$Bn = ((($max-$B)/6)+($delta/2))/$delta;

				if ($R==$max) {
					$H = $Bn-$Gn;
				}
				if ($G==$max) {
					$H = (1/3)+$Rn-$Bn;
				}
				if ($B==$max) {
					$H = (2/3)+$Gn-$Rn;
				}

				if ($H<0) {
					$H = $H+1;
				}
				if ($H>1) {
					$H = $H-1;
				}
			}

			$H = $H*360;
			$S = $S*100;
			$L = $L*100;

			return array($H,$S,$L);
		}
	}

	// convert HSL to RGB color; adapted from www.easyrgb.com
	public function HSL2RGB($H, $S, $L) {
		$this->isHSL($H, $S, $L);

		if ($this->HSLError==0) {
			$H = $H/360;
			$S = $S/100;
			$L = $L/100;

			if ($S==0) {
				$R = $L*255;
				$G = $L*255;
				$B = $L*255;
			} else {
				if ($L<0.5) {
					$temp2 = $L*(1+$S);
				} else {
					$temp2 = ($L+$S)-($S*$L);
				}

				$temp1 = (2*$L)-$temp2;

				$Rtemp3 = $H+(1/3);
				$Gtemp3 = $H;
				$Btemp3 = $H-(1/3);

				$R = 255*$this->Hue2RGB($temp1, $temp2, $Rtemp3);
				$G = 255*$this->Hue2RGB($temp1, $temp2, $Gtemp3);
				$B = 255*$this->Hue2RGB($temp1, $temp2, $Btemp3);
			}

			return array($R,$G,$B);
		}
	}

	// part of HSL2RGB function
	public function Hue2RGB($temp1, $temp2, $temp3) {
		if ($temp3<0) {
			$temp3 = $temp3+1;
		}
		if ($temp3>1) {
			$temp3 = $temp3-1;
		}

		if ((6*$temp3)<1) {
			return $temp1+($temp2-$temp1)*6*$temp3;
		} elseif ((2*$temp3)<1) {
			return $temp2;
		} elseif ((3*$temp3)<2) {
			return $temp1+($temp2-$temp1)*((2/3)-$temp3)*6;
		} else {
			return $temp1;
		}
	}

	// convert HEX to HSL color
	public function HEX2HSL($HEX) {
		$this->isHEX($HEX);
		if ($this->HEXError==0) {
			$temp = $this->HEX2RGB($HEX);
			return $this->RGB2HSL($temp[0], $temp[1], $temp[2]);
		}
	}

	// convert HSL to HEX color
	public function HSL2HEX($H, $S, $L) {
		$this->isHSL($H, $S, $L);
		if ($this->HSLError==0) {
			$temp = $this->HSL2RGB($H, $S, $L);
			return $this->RGB2HEX($temp[0], $temp[1], $temp[2]);
		}
	}
}
?>