<?php
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');

global $adb;
$adb->query("
    CREATE TABLE IF NOT EXISTS `vtiger_parametrize` (
   `param_id` varchar(100) DEFAULT NULL,
  `logo_login` varchar(60) NOT NULL,
  `logo_top` varchar(150) DEFAULT NULL,

  PRIMARY KEY (`param_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO  `vtiger_settings_field` (
`fieldid` ,
`blockid` ,
`name` ,
`iconpath` ,
`description` ,
`linkto` ,
`sequence` ,
`active`
)
VALUES (
'38',  '4',  'LBL_PARAMETRIZE', 'personalize.gif' ,  'LBL_PARAMETRIZE_DESCRIPTION',  'index.php?module=Settings&action=Parametrize&parenttab=Settings', NULL ,  '0'
);

INSERT INTO  `vtiger_parametrize` (
`param_id` ,
`logo_login` ,
`logo_top`
)
VALUES (
'1',  'logo.png',  'app-logo.png'
);

");
?>