<?php
/**
 * Include this file if you want to use the __autoload feature rather than including
 * all of the files manually. It will automatically register its autoload function
 * with spl's autoload mechanism.
 */

require_once 'qCal/Loader.php';
function qCal_Autoloader($name) {

	qCal_Loader::loadClass($name);

}

spl_autoload_register("qCal_Autoloader");