<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class CurrencyField {

	private $CURRENCY_PATTERN_PLAIN = '123456789';
	private $CURRENCY_PATTERN_SINGLE_GROUPING = '123456,789';
	private $CURRENCY_PATTERN_THOUSAND_GROUPING = '123,456,789';
	private $CURRENCY_PATTERN_MIXED_GROUPING = '12,34,56,789';

	/**
	 * Currency Format(3,3,3) or (2,2,3)
	 * @var String
	 */
	public $currencyFormat = '123,456,789';

	/**
	 * Currency Separator for example (comma, dot, hash)
	 * @var String
	 */
	public $currencySeparator = ',';

	/**
	 * Decimal Separator for example (dot, comma, space)
	 * @var <type>
	 */
	public $decimalSeparator = '.';

	/**
	 * Number of Decimal Numbers
	 * @var Integer
	 */
	public $numberOfDecimal = 3;

	/**
	 * Currency Id
	 * @var Integer
	 */
	public $currencyId = 1;

	/**
	 * Currency Symbol
	 * @var String
	 */
	public $currencySymbol;

	/**
	 * Currency Symbol Placement
	 */
	public $currencySymbolPlacement;

	/**
	 * Currency Conversion Rate
	 * @var Number
	 */
	public $conversionRate = 1;

	/**
	 * Value to be converted
	 * @param Number $value
	 */
	public $value = null;

	/**
	 * Maximum Number Of Currency Decimals
	 * @var Number
	 */
	public static $maxNumberOfDecimals = 6;

	/**
	 * Constructor
	 * @param Number $value
	 */
	public function __construct($value) {
		$this->value = $value;
	}

	/**
	 * Initializes the User's Currency Details
	 * @global Users $current_user
	 * @param Users $user
	 */
	public function initialize($user = null) {
		global $current_user,$default_charset;
		if (empty($user)) {
			$user = $current_user;
		}

		if (!empty($user->currency_grouping_pattern)) {
			$this->currencyFormat = html_entity_decode($user->currency_grouping_pattern, ENT_QUOTES, $default_charset);
			$this->currencySeparator = html_entity_decode($user->currency_grouping_separator, ENT_QUOTES, $default_charset);
			$this->decimalSeparator = html_entity_decode($user->currency_decimal_separator, ENT_QUOTES, $default_charset);
		}

		if (!empty($user->currency_id)) {
			$this->currencyId = $user->currency_id;
		} else {
			$this->currencyId = self::getDBCurrencyId();
		}
		$currencyRateAndSymbol = getCurrencySymbolandCRate($this->currencyId);
		$this->currencySymbol = $currencyRateAndSymbol['symbol'];
		$this->conversionRate = $currencyRateAndSymbol['rate'];
		$this->currencySymbolPlacement = (empty($currencyRateAndSymbol['position']) ? $user->currency_symbol_placement : $currencyRateAndSymbol['position']);
		$this->numberOfDecimal = self::getCurrencyDecimalPlaces($user);
	}

	public function getCurrencySymbol() {
		return $this->currencySymbol;
	}

	public function setNumberofDecimals($numberOfDecimals) {
		$this->numberOfDecimal = $numberOfDecimals;
	}

	//Get the User selected NumberOfCurrencyDecimals
	public static function getCurrencyDecimalPlaces($user = null) {
		global $current_user;
		if (empty($user)) {
			$user = $current_user;
		}
		if (isset($user->no_of_currency_decimals)) {
			return $user->no_of_currency_decimals;
		} else {
			return 2;
		}
	}

	/**
	 * Returns the Formatted Currency value for the User
	 * @global Users $current_user
	 * @param Users $user
	 * @param Boolean $skipConversion for multicurrency support
	 * @return String - Formatted Currency
	 */
	public static function convertToUserFormat($value, $user = null, $skipConversion = false) {
		$self = new self($value);
		return $self->getDisplayValue($user, $skipConversion);
	}

	/**
	 * Function that converts the Number into Users Currency
	 * @param Users $user
	 * @param Boolean $skipConversion for multicurrency support
	 * @return Formatted Currency
	 */
	public function getDisplayValue($user = null, $skipConversion = false, $noInit = false) {
		global $current_user;
		if (empty($user)) {
			$user = $current_user;
		}
		if (!$noInit) {
			$this->initialize($user);
		}
		$value = $this->value;
		if ($skipConversion == false) {
			$value = self::convertFromDollar($value, $this->conversionRate);
		}
		return $this->formatCurrencyValue($value);
	}

	/**
	 * Function that converts the Number into Users Currency along with currency symbol
	 * @param Users $user
	 * @param Boolean $skipConversion
	 * @return Formatted Currency
	 */
	public function getDisplayValueWithSymbol($user = null, $skipConversion = false) {
		$formattedValue = $this->getDisplayValue($user, $skipConversion);
		return self::appendCurrencySymbol($formattedValue, $this->currencySymbol, $this->currencySymbolPlacement);
	}

	/**
	 * Static Function that appends the currency symbol to a given currency value, based on the preferred symbol placement
	 * @param Number $currencyValue
	 * @param String $currencySymbol
	 * @param String $currencySymbolPlacement
	 * @return Currency value appended with the currency symbol
	 */
	public static function appendCurrencySymbol($currencyValue, $currencySymbol, $currencySymbolPlacement = '') {
		global $current_user;
		if (empty($currencySymbolPlacement)) {
			$currencySymbolPlacement = $current_user->currency_symbol_placement;
		}

		switch ($currencySymbolPlacement) {
			case '1.0$':
				$returnValue = $currencyValue . $currencySymbol;
				break;
			case '$1.0':
			default:
				$returnValue = $currencySymbol . $currencyValue;
		}
		return $returnValue;
	}

	/**
	 * Function that formats the Number based on the User configured Pattern, Currency separator and Decimal separator
	 * @param Number $value
	 * @return Formatted Currency
	 */
	private function formatCurrencyValue($value) {
		if (is_string($value)) {
			$value = (float)$value;
		}
		if ($value == 0) {
			return '0';
		}
		$currencyPattern = $this->currencyFormat;
		$currencySeparator = $this->currencySeparator;
		$decimalSeparator = $this->decimalSeparator;
		$currencyDecimalPlaces = $this->numberOfDecimal;
		$value = number_format($value, $currencyDecimalPlaces, '.', '');
		if (empty($currencySeparator)) {
			$currencySeparator = ' ';
		}
		if (empty($decimalSeparator)) {
			$decimalSeparator = ' ';
		}

		if ($currencyPattern == $this->CURRENCY_PATTERN_PLAIN) {
			// Replace '.' with Decimal Separator
			$number = str_replace('.', $decimalSeparator, $value);
			return $number;
		}
		$negativeNumber=($value<0);
		// Separate the numeric and decimal parts
		$numericParts = explode('.', $value);
		$wholeNumber = abs($numericParts[0]);
		if ($currencyPattern == $this->CURRENCY_PATTERN_SINGLE_GROUPING) {
			// First part of the number which remains intact
			if (strlen($wholeNumber) > 3) {
				$wholeNumberFirstPart = substr($wholeNumber, 0, strlen($wholeNumber)-3);
			}
			// Second Part of the number (last 3 digits) which should be separated from the First part using Currency Separator
			$wholeNumberLastPart = substr($wholeNumber, -3);
			// Re-create the whole number with user's configured currency separator
			if (!empty($wholeNumberFirstPart)) {
				$numericParts[0] = $wholeNumberFirstPart.$currencySeparator.$wholeNumberLastPart;
			} else {
				$numericParts[0] = $wholeNumberLastPart;
			}
			// Re-create the currency value combining the whole number and the decimal part using Decimal separator
			$number = implode($decimalSeparator, $numericParts);
			if ($negativeNumber) {
				$number='-'.$number;
			}
			return $number;
		}
		if ($currencyPattern == $this->CURRENCY_PATTERN_THOUSAND_GROUPING) {
			// Pad the rest of the length in the number string with Leading 0, to get it to the multiples of 3
			$numberLength = strlen($wholeNumber);
			// First grouping digits length
			$OddGroupLength = $numberLength%3;
			$gapsToBeFilled = 0;
			if ($OddGroupLength > 0) {
				$gapsToBeFilled = 3 - $OddGroupLength;
			}
			$wholeNumber = str_pad($wholeNumber, $numberLength+$gapsToBeFilled, '0', STR_PAD_LEFT);
			// Split the whole number into chunks of 3 digits
			$wholeNumberParts = str_split($wholeNumber, 3);
			// Re-create the whole number with user's configured currency separator
			$numericParts[0] = $wholeNumber = implode($currencySeparator, $wholeNumberParts);
			if ($wholeNumber != 0) {
				$numericParts[0] = ltrim($wholeNumber, '0');
			} else {
				$numericParts[0] = 0;
			}
			// Re-create the currency value combining the whole number and the decimal part using Decimal separator
			$number = implode($decimalSeparator, $numericParts);
			if ($negativeNumber) {
				$number='-'.$number;
			}
			return $number;
		}
		if ($currencyPattern == $this->CURRENCY_PATTERN_MIXED_GROUPING) {
			// First part of the number which needs separate division
			if (strlen($wholeNumber) > 3) {
				$wholeNumberFirstPart = substr($wholeNumber, 0, strlen($wholeNumber)-3);
			}
			// Second Part of the number (last 3 digits) which should be separated from the First part using Currency Separator
			$wholeNumberLastPart = substr($wholeNumber, -3);
			if (!empty($wholeNumberFirstPart)) {
				// Pad the rest of the length in the number string with Leading 0, to get it to the multiples of 2
				$numberLength = strlen($wholeNumberFirstPart);
				// First grouping digits length
				$OddGroupLength = $numberLength%2;
				$gapsToBeFilled = 0;
				if ($OddGroupLength > 0) {
					$gapsToBeFilled = 2 - $OddGroupLength;
				}
				$wholeNumberFirstPart = str_pad($wholeNumberFirstPart, $numberLength+$gapsToBeFilled, '0', STR_PAD_LEFT);
				// Split the first part of tne number into chunks of 2 digits
				$wholeNumberFirstPartElements = str_split($wholeNumberFirstPart, 2);
				$wholeNumberFirstPart = ltrim(implode($currencySeparator, $wholeNumberFirstPartElements), '0');
				$wholeNumberFirstPart = implode($currencySeparator, $wholeNumberFirstPartElements);
				if ($wholeNumberFirstPart != 0) {
					$wholeNumberFirstPart = ltrim($wholeNumberFirstPart, '0');
				} else {
					$wholeNumberFirstPart = 0;
				}
				// Re-create the whole number with user's configured currency separator
				$numericParts[0] = $wholeNumberFirstPart.$currencySeparator.$wholeNumberLastPart;
			} else {
				$numericParts[0] = $wholeNumberLastPart;
			}
			// Re-create the currency value combining the whole number and the decimal part using Decimal separator
			$number = implode($decimalSeparator, $numericParts);
			if ($negativeNumber) {
				$number='-'.$number;
			}
			return $number;
		}
		return $number;
	}

	/**
	 * Returns the Currency value without formatting for DB Operations
	 * @global Users $current_user
	 * @param Users $user
	 * @param Boolean $skipConversion
	 * @return Number
	 */
	public function getDBInsertedValue($user = null, $skipConversion = false) {
		global $current_user;
		if (empty($user)) {
			$user = $current_user;
		}

		$this->initialize($user);

		$value = $this->value;

		$currencySeparator = $this->currencySeparator;
		$decimalSeparator  = $this->decimalSeparator;
		if (empty($currencySeparator)) {
			$currencySeparator = ' ';
		}
		if (empty($decimalSeparator)) {
			$decimalSeparator = ' ';
		}
		$value = str_replace("$currencySeparator", "", $value);
		$value = str_replace("$decimalSeparator", ".", $value);

		if ($skipConversion == false) {
			$value = self::convertToDollar($value, $this->conversionRate);
		}
		//$value = round($value, $this->numberOfDecimal);

		return $value;
	}

	/**
	 * Returns the Currency value without formatting for DB Operations
	 * @param Number $value
	 * @param Users $user
	 * @param Boolean $skipConversion
	 * @return Number
	 */
	public static function convertToDBFormat($value, $user = null, $skipConversion = false) {
		$self = new self($value);
		return $self->getDBInsertedValue($user, $skipConversion);
	}

	/**
	 * Function to get the default CRM currency
	 * @return Integer Default system currency id
	 */
	public static function getDBCurrencyId() {
		$adb = PearDatabase::getInstance();
		$result = $adb->pquery('SELECT id FROM vtiger_currency_info WHERE defaultid < 0', array());
		$noOfRows = $adb->num_rows($result);
		if ($noOfRows > 0) {
			return $adb->query_result($result, 0, 'id');
		}
		return null;
	}

	public static function convertToDollar($amount, $conversionRate) {
		if ($conversionRate == 0) {
			return 0;
		}
		return (float)$amount / $conversionRate;
	}

	public static function convertFromDollar($amount, $conversionRate) {
		return round((float)$amount * (float)$conversionRate, self::$maxNumberOfDecimals);
	}

	/** This function returns the amount converted from master currency.
	 * param $amount - amount to be converted.
	 * param $crate - conversion rate.
	 */
	public static function convertFromMasterCurrency($amount, $conversionRate) {
		return $amount * $conversionRate;
	}

	/** For modules with multi currency functionality this function returns all the information of the currency being used on one record
	 * For this to work, the module MUST have two fields on their MAIN table:
	 *  currency_id: integer uitype 117
	 *  conversion_rate: decimal(10,3) uitype 1 displaytype 3
	 * param $module module name to get the currrency from
	 * param $crmid record to get the currrency from
	 * returns array with all the currency information
	 */
	public static function getMultiCurrencyInfoFrom($module, $crmid) {
		global $log, $adb;
		$log->debug("Entering into function getMultiCurrencyInfoFrom($module, $crmid).");
		$m = CRMEntity::getInstance($module);
		$inventory_table = $m->table_name;
		$inventory_id = $m->table_index;
		$res = $adb->pquery(
			"select currency_id, $inventory_table.conversion_rate as conv_rate, vtiger_currency_info.*
				from $inventory_table
				inner join vtiger_currency_info on $inventory_table.currency_id = vtiger_currency_info.id
				where $inventory_id=?",
			array($crmid)
		);
		if (!$res || $adb->num_rows($res)==0) {
			// if there is no conversion information we suppose the currency field is in the default currency
			$res = $adb->pquery('SELECT id as currency_id, 1 as conv_rate, currency_name, currency_code, currency_symbol, currency_position
					FROM vtiger_currency_info WHERE defaultid < 0', array());
		}

		$currency_info = array();
		$currency_info['currency_id'] = $adb->query_result($res, 0, 'currency_id');
		$currency_info['conversion_rate'] = $adb->query_result($res, 0, 'conv_rate');
		$currency_info['currency_name'] = $adb->query_result($res, 0, 'currency_name');
		$currency_info['currency_code'] = $adb->query_result($res, 0, 'currency_code');
		$currency_info['currency_symbol'] = $adb->query_result($res, 0, 'currency_symbol');
		$currency_info['currency_position'] = $adb->query_result($res, 0, 'currency_position');

		$log->debug("Exit from function getMultiCurrencyInfoFrom.");
		return $currency_info;
	}

	public static function getDecimalsFromTypeOfData($typeofdata) {
		global $current_user;
		$typeinfo = explode('~', $typeofdata);
		if ($typeinfo[0]!='N' && $typeinfo[0]!='NN') {
			return 0;
		}
		if (isset($typeinfo[2])) {
			if (strpos($typeinfo[2], ',')) {
				$decimals = explode(',', $typeinfo[2]);
				$decimals = ((isset($decimals[1]) && is_numeric($decimals[1])) ? $decimals[1] : self::getCurrencyDecimalPlaces($current_user));
			} elseif (isset($typeinfo[3]) && is_numeric($typeinfo[3])) {
				$decimals = $typeinfo[3];
			} else {
				$decimals = self::getCurrencyDecimalPlaces($current_user);
			}
		} else {
			$decimals = self::getCurrencyDecimalPlaces($current_user);
		}
		return $decimals;
	}
}
?>