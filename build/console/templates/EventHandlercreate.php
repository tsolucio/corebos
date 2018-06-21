<?php
global $adb;
require 'include/events/include.inc';
$em = new VTEventsManager($adb);
$em->registerHandler("NAME", "PATH", "CLASSNAME");