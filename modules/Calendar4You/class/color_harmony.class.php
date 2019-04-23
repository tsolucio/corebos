<?php

// last updated: Sept 18, 2010
class colorHarmony extends colorConverter {

	// Monochromatic
	public function Monochromatic($HEX) {
		$color1 = strtolower($HEX); // base color

		$RGB = $this->HEX2RGB($color1);
		$R = $RGB[0];
		$G = $RGB[1];
		$B = $RGB[2];

		$R2 = $this->MonoC($R, 2);
		$G2 = $this->MonoC($G, 2);
		$B2 = $this->MonoC($B, 2);

		$color2 = $this->RGB2HEX($R2, $G2, $B2);

		$R3 = $this->MonoC($R, 3);
		$G3 = $this->MonoC($G, 3);
		$B3 = $this->MonoC($B, 3);

		$color3 = $this->RGB2HEX($R3, $G3, $B3);

		$R4 = $this->MonoC($R, 4);
		$G4 = $this->MonoC($G, 4);
		$B4 = $this->MonoC($B, 4);

		$color4 = $this->RGB2HEX($R4, $G4, $B4);

		$color5 = $this->SecondC($color1);
		$color6 = $this->SecondC($color2);
		$color7 = $this->SecondC($color3);
		$color8 = $this->SecondC($color4);

		return array($color1,$color2,$color3,$color4,$color5,$color6,$color7,$color8);
	}

	// Analogous
	public function Analogous($HEX) {
		$color1 = strtolower($HEX); // base color

		$temp = $this->HEX2HSL($color1);

		$H = $temp[0];
		$S = $temp[1];
		$L = $temp[2];

		$H1 = $this->FixHue($H+30);
		$H2 = $this->FixHue($H-30);

		$color2 = $this->HSL2HEX($H1, $S, $L);
		/*
		$color3 = '#fff'; // for css rule only
		*/
		$color3 = '#ffffff'; // for all rule, include css
		$color4 = $this->HSL2HEX($H2, $S, $L);

		$color5 = $this->SecondC($color1);
		$color6 = $this->SecondC($color2);
		$color7 = $this->SecondC($color3);
		$color8 = $this->SecondC($color4);

		return array($color1,$color2,$color3,$color4,$color5,$color6,$color7,$color8);
	}

	// Complementary
	public function Complementary($HEX) {
		$color1 = strtolower($HEX); // base color

		$RGB = $this->HEX2RGB($color1);
		$R = $RGB[0];
		$G = $RGB[1];
		$B = $RGB[2];

		$temp = $this->HEX2HSL($HEX);

		$H = $temp[0];
		$H = $this->FixHue($H+180);
		$S = $temp[1];
		$L = $temp[2];

		$color2 = $this->HSL2HEX($H, $S, $L);

		$RGB2 = $this->HEX2RGB($color2);
		$R2 = $RGB2[0];
		$G2 = $RGB2[1];
		$B2 = $RGB2[2];

		$R = $this->MonoC($R, 2);
		$G = $this->MonoC($G, 2);
		$B = $this->MonoC($B, 2);

		$color3 = $this->RGB2HEX($R, $G, $B);

		$R2 = $this->MonoC($R2, 2);
		$G2 = $this->MonoC($G2, 2);
		$B2 = $this->MonoC($B2, 2);

		$color4 = $this->RGB2HEX($R2, $G2, $B2);

		$color5 = $this->SecondC($color1);
		$color6 = $this->SecondC($color2);
		$color7 = $this->SecondC($color3);
		$color8 = $this->SecondC($color4);

		return array($color1,$color2,$color3,$color4,$color5,$color6,$color7,$color8);
	}

	// Triads
	public function Triads($HEX) {
		$color1 = strtolower($HEX); // base color

		$temp = $this->HEX2HSL($color1);

		$H = $temp[0];
		$S = $temp[1];
		$L = $temp[2];

		$H1 = $this->FixHue($H+120);
		$H2 = $this->FixHue($H-120);

		$color2 = $this->HSL2HEX($H1, $S, $L);
		/*
		$color3 = '#fff'; // for css rule only
		*/
		$color3 = '#ffffff'; // for all rule, include css
		$color4 = $this->HSL2HEX($H2, $S, $L);

		$color5 = $this->SecondC($color1);
		$color6 = $this->SecondC($color2);
		$color7 = $this->SecondC($color3);
		$color8 = $this->SecondC($color4);

		return array($color1,$color2,$color3,$color4,$color5,$color6,$color7,$color8);
	}

	public function FixHue($Hue) {
		if ($Hue<0) {
			return $Hue+360;
		} elseif ($Hue>360) {
			return $Hue-360;
		} else {
			return $Hue;
		}
	}

	public function SecondC($c) {
		$RGB = $this->HEX2RGB($c);
		$R = $RGB[0];
		$G = $RGB[1];
		$B = $RGB[2];

		$par = 0.75;

		$R2 = floor($par*$R);
		$G2 = floor($par*$G);
		$B2 = floor($par*$B);

		return $this->RGB2HEX($R2, $G2, $B2);
	}

	public function MonoC($c, $n) {
		$par1 = 128;
		$par2 = 192;
		$par3 = 64;
		$par4 = 223;

		$diffC = $c-$par1; // $c = color
		$diff = abs($diffC);

		if ($n==2) {
			if ($diffC>=1) {
				return $par2+floor(0.5*$diff);
			} elseif ($diffC<0) {
				return $par2-floor(0.5*$diff);
			} else {
				return $par2;
			}
		}

		if ($n==3) {
			if ($diffC>=1) {
				return $par3+floor(0.5*$diff);
			} elseif ($diffC<0) {
				return $par3-floor(0.5*$diff);
			} else {
				return $par3;
			}
		}

		if ($n==4) {
			if ($diffC>=1) {
				return $par4+floor(0.25*$diff);
			} elseif ($diffC<0) {
				return $par4-floor(0.25*$diff);
			} else {
				return $par4;
			}
		}
	}
}
?>