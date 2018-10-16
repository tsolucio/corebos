<?php

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
 *************************************************************************************************/
/*
 * @Author: Edmond Kacaj
 * @Date: 2018-09-11 11:54:44
 * @Last Modified by: edmondikacaj@gmail.com
 * @Last Modified time: 2018-09-11 14:53:48
 */

/**
 * this is the type of errors or debugs
 */
abstract class TypeOFErrors
{
    const ERRORLG = "----Map Generator--- ERROR !!!";
    const INFOLG = "----Map Generator----- INFO !!!";
    const WARNINGLG = "---Map Generator---- WARNING !!!";
    const SUCCESLG = "----Map Generator----- SUCCESS !!!";
    /**
     * @param      <String>  constant for tabele of history
     */
    const Tabele_name = "mapgeneration_queryhistory";

    const HttpresponseTypeTable = "mapgeneration_httpresponsetype";

}
