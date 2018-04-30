<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Validations.
 * The MIT License (MIT)
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT
 * NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **
 * Created by: Sergio Zambrano Delfa <sergio.zambrano@gmail.com>
 * Date: 10/11/16
 * Time: 9:38
 *************************************************************************************************/

class validatorESIDNumber {
	const DOCUMENT_LENGTH_WITH_CODE = 9;
	const DOCUMENT_LENGTH_WITHOUT_CODE = 8;
	private $functions = array(
		'DNI' => 'getDNICode',
		'NIE' => 'getNIECode',
		'NIF' => 'getNIFCode',
		'CIFN' => 'getCIFCode',
		'CIFL' => 'getCIFCode',
	);
	private static $patterns = array(
		'DNI'  => "~^\\d{8,8}[TRWAGMYFPDXBNJZSQVHLCKE]?$~",
		'NIE'  => "~^[XYZ]\\d{7,7}[TRWAGMYFPDXBNJZSQVHLCKE]?$~",
		'NIF'  => "~^[KLM]\\d{7,7}[ABCDEFGHIJ]?$~",
		'CIFN' => "~^[ABCDEFGHJUV]\\d{7,7}\\d?$~",
		'CIFL' => "~^[NPQRSW]\\d{7,7}[ABCDEFGHIJ]?$~",
	);

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function isDNIFormat($documentId) {
		return 1 === preg_match(self::$patterns['DNI'], $documentId);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function isNIEFormat($documentId) {
		 return 1 === preg_match(self::$patterns['NIE'], $documentId);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function isNIFFormat($documentId) {
		return 1 === preg_match(self::$patterns['NIF'], $documentId);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	private function isCIFL($documentId) {
		 return 1 === preg_match(self::$patterns['CIFL'], $documentId);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	private function isCIFN($documentId) {
		 return 1 === preg_match(self::$patterns['CIFN'], $documentId);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function isPersonalFormat($documentId) {
		 return $this->isDNIFormat($documentId) || $this->isNIEFormat($documentId) || $this->isNIFFormat($documentId);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function isCIFFormat($documentId) {
		return $this->isCIFL($documentId) || $this->isCIFN($documentId);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function isValidFormat($documentId) {
		return $this->isPersonalFormat($documentId) || $this->isCIFFormat($documentId);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function isValidDNI($documentId) {
		if (!is_string($documentId) ||
			strlen($documentId) !== static::DOCUMENT_LENGTH_WITH_CODE ||
			!$this->isDNIFormat($documentId)
		) {
			return false;
		}
		$documentFirstEightChars = substr($documentId, 0, static::DOCUMENT_LENGTH_WITHOUT_CODE);
		$code = $this->getDNICode($documentFirstEightChars);
		$lastChar = $this->getLastCharOfString($documentId);
		return ($code === $lastChar || strtolower($code) === $lastChar);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function isValidNIE($documentId) {
		if (!is_string($documentId) || strlen($documentId)!== static::DOCUMENT_LENGTH_WITH_CODE || !$this->isNIEFormat($documentId)) {
			return false;
		}
		$documentFirstEightChars = substr($documentId, 0, static::DOCUMENT_LENGTH_WITHOUT_CODE);
		$code = $this->getNIECode($documentFirstEightChars);

		$lastChar = $this->getLastCharOfString($documentId);
		return ($code === $lastChar || strtolower($code) === $lastChar);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function isValidNIF($documentId) {
		if (!is_string($documentId) ||
			strlen($documentId) !== static::DOCUMENT_LENGTH_WITH_CODE ||
			!$this->isNIFFormat($documentId)
		) {
			return false;
		}
		$documentFirstEightChars = substr($documentId, 0, static::DOCUMENT_LENGTH_WITHOUT_CODE);
		$code = $this->getNIFCode($documentFirstEightChars);
		$lastChar = $this->getLastCharOfString($documentId);
		return ($code === $lastChar || strtolower($code) === $lastChar);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function isValidCIF($documentId) {
		if (!is_string($documentId) ||
			strlen($documentId) !== static::DOCUMENT_LENGTH_WITH_CODE
			|| !$this->isCIFFormat($documentId)
		) {
			return false;
		}
		$documentFirstEightChars = substr($documentId, 0, static::DOCUMENT_LENGTH_WITHOUT_CODE);
		$code = $this->getCIFCode($documentFirstEightChars);
		$lastChar = $this->getLastCharOfString($documentId);
		return ($code === $lastChar || strtolower($code) === $lastChar);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function isValidPersonalESID($documentId) {
		 return $this->isValidDNI($documentId) || $this->isValidNIE($documentId) || $this->isValidNIF($documentId);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function isValidESID($documentId) {
		return $this->isValidPersonalESID($documentId) || $this->isValidCIF($documentId);
	}

	/**
	 * @param string $documentId
	 * @return bool
	 */
	public function validate($documentId) {
		$documentFirstEightChars = substr($documentId, 0, static::DOCUMENT_LENGTH_WITHOUT_CODE);
		$controlCode = $this->getDocumentCode($documentFirstEightChars);
		$lastChar = $this->getLastCharOfString($documentId);
		return strlen($documentId) === static::DOCUMENT_LENGTH_WITH_CODE && ($controlCode === $lastChar || strtolower($controlCode) === $lastChar);
	}

	/**
	 * @param string $documentId
	 * @return string
	 */
	private function getLastCharOfString($documentId) {
		return substr($documentId, -1, 1);
	}

	/**
	 * @param string $documentId
	 * @return string
	 */
	public function getDNICode($documentId) {
		if (!is_string($documentId) || strlen($documentId) !== static::DOCUMENT_LENGTH_WITHOUT_CODE) {
			return '';
		}
		$modulo = (int)$documentId % 23;
		return substr('TRWAGMYFPDXBNJZSQVHLCKE', $modulo, 1);
	}

	/**
	 * @param string $documentId
	 * @return string
	 */
	public function getNIECode($documentId) {
		if (!is_string($documentId) || strlen($documentId) !== static::DOCUMENT_LENGTH_WITHOUT_CODE) {
			return '';
		}
		$documentId = str_replace(array('X', 'Y', 'Z'), array('', '1', '2'), $documentId);
		$modulo = (int)$documentId % 23;
		return substr('TRWAGMYFPDXBNJZSQVHLCKE', $modulo, 1);
	}

	/**
	 * @param string $documentId
	 * @return mixed
	 */
	public function getNIFCode($documentId) {
		if (!is_string($documentId) || strlen($documentId) !== static::DOCUMENT_LENGTH_WITHOUT_CODE) {
			return '';
		}
		$modulo = $this->calculateModule($documentId);
		return $modulo[1];
	}

	/**
	 * @param string $documentId
	 * @return string
	 */
	public function getCIFCode($documentId) {
		if (!is_string($documentId) || strlen($documentId) !== static::DOCUMENT_LENGTH_WITHOUT_CODE) {
			return '';
		}
		$modulo = $this->calculateModule($documentId);
		$code = $modulo[0];
		if (1 === preg_match(self::$patterns['CIFL'], $documentId)) {
			$code = $modulo[1];
		}
		return $code;
	}

	private function calculateModule($documentId) {
		$controlCodes = 'JABCDEFGHI';
		$even = 0;
		for ($i=2; $i<=6; $i+=2) {
			$even += (int)substr($documentId, $i, 1);
		}
		$odd = 0;
		for ($i=1; $i<=7; $i+=2) {
			$partial = 2*(int)substr($documentId, $i, 1);
			if ($partial>9) {
				$odd += 1 + ($partial - 10);
			} else {
				$odd += $partial;
			}
		}
		$modulo = ($even + $odd) % 10;
		if ($modulo!=0) {
			$modulo = 10 - $modulo;
		}
		return array($modulo, substr($controlCodes, $modulo, 1));
	}

	/**
	 * @param string $documentId
	 * @return mixed
	 */
	public function getDocumentCode($documentId) {
		foreach ($this->functions as $key => $method) {
			if (1 === preg_match(self::$patterns[$key], $documentId)) {
				return $this->$method($documentId);
			}
		}
		return '';
	}
}

function isValidDNI($field, $fieldval, $params, $fields) {
	$v = new validatorESIDNumber();
	return $v->isValidDNI($fieldval);
}
function isValidNIE($field, $fieldval, $params, $fields) {
	$v = new validatorESIDNumber();
	return $v->isValidNIE($fieldval);
}
function isValidNIF($field, $fieldval, $params, $fields) {
	$v = new validatorESIDNumber();
	return $v->isValidNIF($fieldval);
}
function isValidCIF($field, $fieldval, $params, $fields) {
	$v = new validatorESIDNumber();
	return $v->isValidCIF($fieldval);
}
function isValidPersonalESID($field, $fieldval, $params, $fields) {
	$v = new validatorESIDNumber();
	return $v->isValidPersonalESID($fieldval);
}
function isValidESID($field, $fieldval, $params, $fields) {
	$v = new validatorESIDNumber();
	return $v->isValidESID($fieldval);
}
