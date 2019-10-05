/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Module       : Password Management
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S.L.
 *               : random generator based on code from ataxx@visto.com
 *               : http://www.javascriptsource.com/passwords/random-password-generator.html
 *************************************************************************************************/

var corebos_Password = {

	getRandomNum: function (lbound, ubound) {
		return (Math.floor(Math.random() * (ubound - lbound)) + lbound);
	},

	getRandomChar: function (number, lower, upper, other, extra) {
		var numberChars = '0123456789';
		var lowerChars = 'abcdefghijklmnopqrstuvwxyz';
		var upperChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		var otherChars = "`~!@#$%^&*()-_=+[{]}\\|;:'\",<.>/? ";
		var charSet = extra;
		if (number == true) {
			charSet += numberChars;
		}
		if (lower == true) {
			charSet += lowerChars;
		}
		if (upper == true) {
			charSet += upperChars;
		}
		if (other == true) {
			charSet += otherChars;
		}
		return charSet.charAt(this.getRandomNum(0, charSet.length));
	},

	getPassword: function (length, extraChars, firstNumber, firstLower, firstUpper, firstOther, latterNumber, latterLower, latterUpper, latterOther) {
		var rc = '';
		if (length > 0) {
			rc = rc + this.getRandomChar(firstNumber, firstLower, firstUpper, firstOther, extraChars);
		}
		for (var idx = 1; idx < length; ++idx) {
			rc = rc + this.getRandomChar(latterNumber, latterLower, latterUpper, latterOther, extraChars);
		}
		return rc;
	},

	//Check for special character
	checkSpecialChar: function (passwordValue) {
		var i=0;
		var character='';
		while (i <= passwordValue.length) {
			character = passwordValue.charAt(i);
			if ((character == '.')||(character =='!')||(character =='?')||(character ==',')||(character ==';')||(character =='-')||(character =='@')||(character =='#')) {
				return true;
			}
			i++;
		}
		return false;
	},

	//check for number
	checkNumber: function (passwordValue) {
		var i=0;
		while (i < passwordValue.length) {
			var character = passwordValue.charAt(i);
			if (!isNaN(character)) {
				return true;
			}
			i++;
		}
		return false;
	},

	//Check for lowercase character
	checkLower: function (passwordValue) {
		var i=0;
		while (i < passwordValue.length) {
			var character = passwordValue.charAt(i);
			if (character == character.toLowerCase()) {
				return true;
			}
			i++;
		}
		return false;
	},

	//Check for capital
	checkCapital: function (passwordValue) {
		var i=0;
		while (i < passwordValue.length) {
			var character = passwordValue.charAt(i);
			if (character == character.toUpperCase()) {
				return true;
			}
			i++;
		}
		return false;
	},

	passwordChecker: function (passwordValue) {
		passwordValue = trim(passwordValue);
		//Length Password
		var passwordLength = (passwordValue.length);

		//Capital?
		var containsCapital = this.checkCapital(passwordValue);

		//Lower?
		var containsLower = this.checkLower(passwordValue);

		//Number?
		var containsNumber = this.checkNumber(passwordValue);

		//Special Char?
		var containsSpecialChar = this.checkSpecialChar(passwordValue);

		//COMPLEX PASSWORD: Minimum 8 characters, and three of the four conditions needs to be ok --> Capital, Lowercase, Special Character, Number
		if (passwordLength < 8) {
			return false;
		} else {
			//Combination Match All
			if ((containsNumber == true)&&(containsCapital == true)&&(containsLower == true)&&(containsSpecialChar == true)) {
				return true;
			} else {
				//Combination 1
				if ((containsNumber == true)&&(containsCapital == true)&&(containsLower == true)) {
					return true;
				} else {
					//Combination 2
					if ((containsCapital == true)&&(containsLower == true)&&(containsSpecialChar == true)) {
						return true;
					} else {
						//Combination 3
						if ((containsLower == true)&&(containsSpecialChar == true)&&(containsNumber == true)) {
							return true;
						} else {
							//Combination 4
							if ((containsNumber == true)&&(containsCapital == true)&&(containsSpecialChar == true)) {
								return true;
							} else {
								return false;
							}
						}
					}
				}
			}
		}
	}
};