<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
********************************************************************************/

require_once('include/pclzip.lib.php');


//functions for odt mailmerge

function GetFileExtension($filename) 
{
    $pathinfo = pathinfo($filename);
    return $pathinfo['extension'];
}

function crmmerge($csvheader,$content,$index_in_csvdata,$function_name='')
{
    global $csvdata;
    $f = $function_name;
    if (!($function_name) || !is_callable($function_name)) {
        $f = create_function('$a','return $a;');
    }
    $Header = explode (",",$csvheader);
    $Temp = explode ("###",$csvdata);
    $Values = explode (",",$Temp[$index_in_csvdata]);
    $numfields = count($Values);
    for($i=0;$i<$numfields;$i++)
    {
        $content = str_replace($Header[$i], call_user_func($f,$Values[$i]), $content);
    }
    return $content;
}


function entpack($filename,$wordtemplatedownloadpath,$filecontent)
{
    //global $filename, $wordtemplatedownloadpath;
    $temp_dir = time();
    mkdir($root_directory .$wordtemplatedownloadpath .$temp_dir,0777);
    $handle = fopen($wordtemplatedownloadpath.'/'.$filename,"wb");
    $filecontent = base64_decode($filecontent);
    fwrite($handle,$filecontent);
    fclose($handle);
    
    $archive = new PclZip($wordtemplatedownloadpath.'/'.$filename);
    //unzip all files 
    if ($archive->extract(PCLZIP_OPT_PATH,$wordtemplatedownloadpath.'/'.$temp_dir) == 0)
    {
        die("Error s: ".$archive->errorInfo(true));
    }
    //delete the template
    //unlink($wordtemplatedownloadpath.'/'.$filename);
    return $temp_dir;
}

function packen($filename,$wordtemplatedownloadpath,$temp_dir, $concontent,$stylecontent)
{
    //global $filename, $wordtemplatedownloadpath;
    //write a new content.xml
    $handle=fopen($wordtemplatedownloadpath.'/'.$temp_dir.'/content.xml',"w");
    fwrite($handle,$concontent);
    fclose($handle);

    //write a new styles.xml
    $handle2=fopen($wordtemplatedownloadpath.'/'.$temp_dir.'/styles.xml',"w");
    fwrite($handle2,$stylecontent);
    fclose($handle2);

    $archive = new PclZip($wordtemplatedownloadpath.'/'.$filename);
    //make a new archive (or .odt file)
    $v_list = $archive->add($wordtemplatedownloadpath.'/'.$temp_dir,PCLZIP_OPT_REMOVE_PATH, $wordtemplatedownloadpath.'/'.$temp_dir);
    if ($v_list == 0) 
    {
        die("Error : ".$archive->errorInfo(true));
    }
}

function remove_dir($dir)
{
    $handle = opendir($dir);
    while (false!==($item = readdir($handle)))
    {
        if($item != '.' && $item != '..')
        {
            if(is_dir($dir.'/'.$item)) 
            {
                remove_dir($dir.'/'.$item);
            }
            else
            {
                unlink($dir.'/'.$item);
            }
        }
    }
    closedir($handle);
    if(rmdir($dir))
    {
        $success = true;
    }
    return $success;
}


/**
* @see http://sourceforge.net/projects/phprtf
*/
function utf8Unicode($str) {
        return unicodeToEntitiesPreservingAscii(utf8ToUnicode($str));
}


/**
* @see http://sourceforge.net/projects/phprtf
*/
function unicodeToEntitiesPreservingAscii($unicode) {
    $entities = '';
    foreach( $unicode as $value ) {
                if ($value != 65279) {
                $entities .= ( $value > 127 ) ? '\uc0\u' . $value . ' ' : chr( $value );
            }
    }
    return $entities;
}
/**
* @see http://sourceforge.net/projects/phprtf
* @see http://www.randomchaos.com/documents/?source=php_and_unicode
*/
function utf8ToUnicode($str) {
    $unicode = array();
    $values = array();
    $lookingFor = 1;

    for ($i = 0; $i < strlen($str); $i++ ) {
        $thisValue = ord($str[$i]);

            if ($thisValue < 128) {
                        $unicode[] = $thisValue;
        } else {
            if ( count( $values ) == 0 ) {
                                $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
                        }

            $values[] = $thisValue;

            if ( count( $values ) == $lookingFor ) {
                $number = ( $lookingFor == 3 ) ?
                    ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                        ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
                $unicode[] = $number;
                $values = array();
                $lookingFor = 1;
            }
        }
    }
    return $unicode;
}


?>
