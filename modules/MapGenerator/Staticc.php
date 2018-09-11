<?php
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
