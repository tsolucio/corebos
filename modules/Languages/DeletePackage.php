<?php
/*********************************************************************************
 * $Header$
 * Description: Language Pack Wizard
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): Gaëtan KRONEISEN technique@expert-web.fr
 * 				   Pius Tschümperlin ep-t.ch
 ********************************************************************************/
require_once('modules/Languages/Config.inc.php');
$languageid = $_REQUEST['languageid'];

    function full_rmdir( $dir )
    {
        if ( !is_writable( $dir ) )
        {
            if ( !@chmod( $dir, 0777 ) )
            {
                return FALSE;
            }
        }
       
        $d = dir( $dir );
        while ( FALSE !== ( $entry = $d->read() ) )
        {
            if ( $entry == '.' || $entry == '..' )
            {
                continue;
            }
            $entry = $dir . '/' . $entry;
            if ( is_dir( $entry ) )
            {
                if ( !$this->full_rmdir( $entry ) )
                {
                    return FALSE;
                }
                continue;
            }
            if ( !@unlink( $entry ) )
            {
                $d->close();
                return FALSE;
            }
        }
       
        $d->close();
       
        rmdir( $dir );
       
        return TRUE;
    }

	//Get prefix of selected languages
	$sql = "SELECT prefix FROM vtiger_languages WHERE languageid =".$languageid;
	$result = $adb->query($sql);
	$row = $adb->fetch_array($result);

	//Delete all modules languages files
	if ($dh = opendir($modulesDirectory)) {
		while (($folder = readdir($dh)) !== false) { 
			if(is_dir($modulesDirectory.'/'.$folder)&&$folder!='..'&&$folder!='.'&&file_exists($modulesDirectory.'/'.$folder.'/language/'.$row['prefix'].'.lang.php')) {
				unlink($modulesDirectory.'/'.$folder.'/language/'.$row['prefix'].'.lang.php');
				if(file_exists($modulesDirectory.'/'.$folder.'/language/'.$row['prefix'].'.lang.php.bak')){
					unlink($modulesDirectory.'/'.$folder.'/language/'.$row['prefix'].'.lang.php.bak');
				}
			}
		} 
		closedir($dh);
	}

	//Delete (if exist) All the informations to generate the zip package
	if(is_dir($tmp_dir.$row['prefix'])){
		full_rmdir($tmp_dir.$row['prefix']);
	}

	//Delete the generic language file
	if(file_exists('include/language/'.$row['prefix'].'.lang.php')){
		unlink('include/language/'.$row['prefix'].'.lang.php');
	}
	if(file_exists('include/language/'.$row['prefix'].'.lang.php.bak')){
		unlink('include/language/'.$row['prefix'].'.lang.php.bak');
	}
	
	//Delete the JavaScript language file
	if(file_exists('include/js/'.$row['prefix'].'.lang.js')){
		unlink('include/js/'.$row['prefix'].'.lang.js');
	}
	if(file_exists('include/language/'.$row['prefix'].'.lang.php.bak')){
		unlink('include/language/'.$row['prefix'].'.lang.php.bak');
	}
	
	//Remove from DB
	$sql = "delete from vtiger_languages where languageid =".$languageid;
	$adb->query($sql);
	
	// remove prefix from config.inc.php
	$filename='config.inc.php';
	if(file_exists($filename) && is_writable($filename)){
		$ConfigfileContent = file_get_contents($filename,FILE_TEXT);
		
		if (preg_match("/'".$row['prefix']."'\s*=>/",$ConfigfileContent)) {
			$ConfigfileContent = preg_replace('/(\''.$row['prefix'].'\'\s*=>.*\',?)/i', '', $ConfigfileContent);
		
			if ($make_backups == true) {
				@unlink($filename.'.bak');
				@copy($filename, $filename.'.bak');
			}
			$fd = fopen($filename, 'w');
			fwrite($fd, $ConfigfileContent);
			fclose($fd);
		}
		header("Location:index.php?module=Languages&action=ListPackages&parenttab=Settings");
		
	}
	else header("Location:index.php?module=Languages&action=ListPackages&parenttab=Settings&error=ERROR_CONFIG_INC");





?>


