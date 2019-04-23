<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

function validaCPF($field, $cpf = null) {
	// Verifica se um número foi informado
	if (empty($cpf)) {
		return false;
	}

	// Elimina possivel mascara
	$cpf = preg_replace('/[^0-9]/', '', $cpf);
	$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

	// Verifica se o numero de digitos informados é igual a 11
	if (strlen($cpf) != 11) {
		return false;
	} elseif ($cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' ||
			$cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
		// Verifica se nenhuma das sequências invalidas abaixo
		// foi digitada. Caso afirmativo, retorna falso
		return false;
		// Calcula os digitos verificadores para verificar se o
		// CPF é válido
	} else {
		for ($t = 9; $t < 11; $t ++) {
			for ($d = 0, $c = 0; $c < $t; $c ++) {
				$d += $cpf {$c} * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($cpf {$c} != $d) {
				return false;
			}
		}
		return true;
	}
}

function validaCNPJ($field, $cnpj) {
	$cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
	// Valida tamanho
	if (strlen($cnpj) != 14) {
		// CNPJ incorrect length
		return false;
	}
	// Valida primeiro dígito verificador
	for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
		$soma += $cnpj{$i} * $j;
		$j = ($j == 2) ? 9 : $j - 1;
	}
	$resto = $soma % 11;
	if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto)) {
		// CNPJ incorrect char 12
		return false;
	}
	// Valida segundo dígito verificador
	for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
		$soma += $cnpj{$i} * $j;
		$j = ($j == 2) ? 9 : $j - 1;
	}
	$resto = $soma % 11;
	return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
}

function validaCNPJ2($field, $cnpj) {
	$c = preg_replace('/\D/', '', $cnpj);
	if (strlen($c) != 14 || preg_match("/^{$c[0]}{14}$/", $c)) {
		// CNPJ2 incorrect length
		return false;
	}
	$b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
	for ($i = 0, $n = 0; $i < 12;
		$n += $c[$i] * $b[++$i]) {
	}
	if ($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
		// CNPJ2 incorrect char 12
		return false;
	}
	for ($i = 0, $n = 0; $i <= 12;
		$n += $c[$i] * $b[$i++]) {
	}
	if ($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
		// CNPJ2 incorrect char 13
		return false;
	}
	return true;
}
