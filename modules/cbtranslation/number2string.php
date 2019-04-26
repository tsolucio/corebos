<?php
/******************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of coreBOS
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
 ******************************************************************************************************/

class number2string {

	private static $lang2use;

	/* Función que dado un número nos lo devuelve escrito en letras.
	 * Basado en Artículo PCWorld y rutina en ExcelBasic.
	 * @param $x numero a traducir en formato EN, 123456.78
	 * @devuelve cadena que representa número dado escrito
	 * @creado Joe     Fecha:  2001/2/4, 2017/12/20
	*/
	public static function convert($x, $lang = '') {
		if ($lang == '') {
			global $default_language;
			$lang = substr($default_language, 0, 2);
		}
		self::$lang2use = $lang;
		$lng = self::getLanguage($lang);
		$parte1 = floor($x / 1000000);
		$parte2 = floor(($x - $parte1 * 1000000) / 1000);
		$parte3 = $x - $parte1 * 1000000 - $parte2 * 1000;
		if ($parte1 == 0) {
			$millon = '';
		}
		if ($parte1 == 1) {
			$millon = $lng['one'].' '.$lng['million'].' ';
		}
		if ($parte1 > 1) {
			$millon = self::aCadena($parte1).' '.$lng['millions'].' ';
		}
		if (($parte1 - 100 * floor($parte1 / 100) > 11) && ($parte1 - 10 * floor($parte1 / 10) == 1)) {
			$millon = self::aCadena($parte1).' '.$lng['millions']. ' ';
		}
		if ($parte2 == 0) {
			$millar = '';
		}
		if ($parte2 == 1) {
			$millar = ' '.$lng['thousand'].' ';
		}
		if ($parte2 > 1) {
			$millar = ' '.self::aCadena($parte2).' '.$lng['thousand'].' ';
		}
		if (($parte2 - 100 * floor($parte2 / 100) > 11) && (($parte2 - 10 * floor($parte2 / 10)) == 1)) {
			$millar = self::aCadena($parte2).' '.$lng['thousand'].' ';
		}
		$ENLETRAS = $millon.$millar.self::aCadena($parte3);
		if (strpos($x, '.')>0) {
			$dec = substr($x, strpos($x, '.')+1);
			$ENLETRAS = $ENLETRAS.' '.$lng['coma'].' '.self::convert($dec, $lang);
		}
		if (floor($x) == 0) {
			$ENLETRAS = $lng['zero'];
		}
		return trim(str_replace('  ', ' ', $ENLETRAS));
	}

	private static function aCadena($x) {
		$lng = self::getLanguage(self::$lang2use);
		$uni = $lng['uni'];
		$dec = $lng['dec'];
		$cent = $lng['cent'];
		$xcent = floor($x / 100);
		$xdec = floor($x / 10) - 10 * $xcent;
		$xuni = $x - 100 * $xcent - 10 * $xdec;
		if ($xdec > 2) {
			$NOM = $dec[$xdec];
			if ($xuni > 0) {
				$NOM = $NOM.' '.$lng['and'].' '.$uni[$xuni];
			}
		} else {
			$NOM = $uni[$xdec * 10 + $xuni];
		}
		if ($xcent > 0) {
			$NOM = $cent[$xcent]." ".$NOM;
		}
		if ($x == 100) {
			$NOM = $lng['hundred'];
		}
		return $NOM;
	}

	private static function getLanguage($lang) {
		$languages = array(
			'es' => array(
				'coma' => 'coma',
				'and' => 'Y',
				'one' => 'UN',
				'uni' => array(
					'',
					'UNO', 'DOS', 'TRES', 'CUATRO',
					'CINCO', 'SEIS', 'SIETE', 'OCHO',
					'NUEVE', 'DIEZ', 'ONCE', 'DOCE',
					'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS',
					'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE', 'VEINTE',
					'VEINTIUNO', 'VEINTIDOS', 'VEINTITRES', 'VEINTICUATRO',
					'VEINTICINCO', 'VEINTISEIS', 'VEINTISIETE', 'VEINTIOCHO', 'VEINTINUEVE'
				),
				'dec' => array(
					'', '', '',
					'TREINTA', 'CUARENTA', 'CINCUENTA',
					'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'
				),
				'cent' => array(
					'',
					'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS',
					'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS',
					'OCHOCIENTOS', 'NOVECIENTOS'
				),
				'zero' => 'CERO',
				'hundred' => 'CIEN',
				'thousand' => 'MIL',
				'million' => 'MILLON',
				'millions' => 'MILLONES',
			),
			'en' => array(
				'coma' => 'point',
				'and' => '',
				'one' => 'ONE',
				'uni' => array(
					'',
					'ONE', 'TWO', 'THREE', 'FOUR',
					'FIVE', 'SIX', 'SEVEN', 'EIGHT',
					'NINE', 'TEN', 'ELEVEN', 'TWELVE',
					'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN',
					'SEVENTEEN', 'EIGHTTEEN', 'NINETEEN', 'TWENTY',
					'TWENTY-ONE', 'TWENTY-TWO', 'TWENTY-THREE', 'TWENTY-FOUR',
					'TWENTY-FIVE', 'TWENTY-SIX', 'TWENTY-SEVEN', 'TWENTY-EIGHT', 'TWENTY-NINE'
				),
				'dec' => array(
					'', '', '',
					'THIRTY', 'FOURTY', 'FIFTY',
					'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'
				),
				'cent' => array(
					'',
					'HUNDRED', 'TWO HUNDRED', 'THREE HUNDRED',
					'FOUR HUNDRED', 'FIVE HUNDRED', 'SIX HUNDRED', 'SEVEN HUNDRED',
					'EIGHT HUNDRED', 'NINE HUNDRED'
				),
				'zero' => 'ZERO',
				'hundred' => 'HUNDRED',
				'thousand' => 'THOUSAND',
				'million' => 'MILLION',
				'millions' => 'MILLIONS',
			),
		);
		return (isset($languages[$lang]) ? $languages[$lang] : $languages['en']);
	}
}
?>
