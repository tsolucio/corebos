<?php
/**
 * Función que dado un número nos lo devuelve escrito en letras.
 * Basado en Artículo PCWorld y rutina en ExcelBasic.
 * @param $x numero a traducir
 * @devuelve cadena que representa número dado escrito
 * @creado Joe     Fecha:  4/2/01
 *******************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of CobroPago vtiger CRM Extension.
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
function EnLetras($x) {
 $x = str_replace(',','',$x);
 $parte1 = floor($x / 1000000);
 $parte2 = floor(($x - $parte1 * 1000000) / 1000);
 $parte3 = $x - $parte1 * 1000000 - $parte2 * 1000;
 If ($parte1 == 0) $millon = "";
 If ($parte1 == 1) $millon = "UN MILLON ";
 If ($parte1 > 1) $millon = NOMBRE($parte1)." MILLONES ";
 If (($parte1 - 100 * floor($parte1 / 100) > 11) && ($parte1 - 10 * floor($parte1 / 10) == 1)):
     $millon = substr(NOMBRE($parte1), 1, strlen(NOMBRE($parte1)) - 1)." MILLONES ";
 endif;
 If ($parte2 == 0) $millar = "";
 If ($parte2 == 1) $millar = " MIL ";
 If ($parte2 > 1) $millar = NOMBRE($parte2)." MIL ";
 If (($parte2 - 100 * floor($parte2 / 100) > 11) && (($parte2 - 10 * floor($parte2 / 10)) == 1)):
     $millar = substr(NOMBRE($parte2), 1, strlen(NOMBRE($parte2)) - 1)." MIL ";
 endif;
 $ENLETRAS = $millon.$millar.NOMBRE($parte3);
 if (floor($x)<>$x): /* no es entero, solo tratamos dos decimales */
   $dec=($x-floor($x))*10;
   if (floor($dec)*10<>floor($dec*10)) /* hay dos decimales */ $dec=$dec*10;
   $dec=floor($dec);
   $declen=strlen($dec);
   $ENLETRAS = $ENLETRAS." coma ".EnLetras($dec);
 endif;
 If (floor($x) == 0) $ENLETRAS = "CERO";
 return $ENLETRAS;
}

function NOMBRE($x) {
 $uni = array("", "UNO", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE", "DIEZ", "ONCE", "DOCE", "TRECE",
     "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE", "VEINTE","VEINTIUNO", "VEINTIDOS", "VEINTITRES",
     "VEINTICUATRO", "VEINTICINCO", "VEINTISEIS", "VEINTISIETE", "VEINTIOCHO", "VEINTINUEVE");
 $dec = array("", "", "", "TREINTA", "CUARENTA", "CINCUENTA", "SESENTA", "SETENTA", "OCHENTA", "NOVENTA");
 $cent = array("", "CIENTO", "DOSCIENTOS", "TRESCIENTOS", "CUATROCIENTOS", "QUINIENTOS", "SEISCIENTOS", "SETECIENTOS",
     "OCHOCIENTOS", "NOVECIENTOS");
 $xcent = floor($x / 100);
 $xdec = floor($x / 10) - 10 * $xcent;
 $xuni = $x - 100 * $xcent - 10 * $xdec;
 If ($xdec > 2):
     $NOM = $dec[$xdec];
     If ($xuni > 0):
         $NOM = $NOM." Y ".$uni[$xuni];
     EndIf;
 Else:
     $NOM = $uni[$xdec * 10 + $xuni];
 EndIf;
 If ($xcent > 0) $NOM = $cent[$xcent]." ".$NOM;
 If ($x == 100) $NOM = "CIEN";
 return $NOM;
}

//print EnLetras($num)."<br>";
//print EnLetras("2,349.368");
//print EnLetras("312.68");
?>