#!/usr/bin/php
<?php
if ($argc!=1 || !is_readable('config.inc.php') || !is_readable('include/utils/CommonUtils.php')) {
	echo "Regenerates tabdata in the current directory.\n";
	echo "USAGE: ".basename($argv[0])."\n";
	exit;
}
require('config.inc.php');
require('include/utils/CommonUtils.php');
create_tab_data_file();
create_parenttab_data_file();
?>
