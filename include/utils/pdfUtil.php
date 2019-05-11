<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'include/utils/pdfConcat.php';

class pdfutil {

	/**
	 * Get an instance of the PDF Concatenation class
	 * @param string $filename full path to the inital PDF file
	 * @return concat_pdf
	 */
	public static function getPDFConcat($filename) {
		return new concat_pdf($filename);
	}

	/**
	 * Password protect a given PDF
	 * @param string $input full path to the PDF file to protect
	 * @param string $password password to use to protect the input PDF
	 * @param string $output full path to where the protected PDF should be saved (will be overwritten if exists)
	 * @return boolean if successful or not
	 */
	public static function PDFProtect($input, $password, $output) {
	}

	/**
	 * Password unprotect a given PDF
	 * @param string $input full path to the PDF file to unprotect
	 * @param string $password password to use to unprotect the input PDF
	 * @param string $output full path to where the unprotected PDF should be saved (will be overwritten if exists)
	 * @return boolean if successful or not
	 */
	public static function PDFUnProtect($input, $password, $output) {
	}

	/**
	 * Search the given PDF for a NIF value (custom PDF layout), if found, search the indicated module and field for that NIF value
	 *  and return the CRMID of the record found. In case no record is found return -1
	 * @param string $filename full path to the inital PDF file
	 * @return concat_pdf
	 */
	public static function PDFIdentifyByNIF($filename, $module, $fieldname) {
	}
}
?>
