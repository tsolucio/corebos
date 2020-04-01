<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2014-07-27
 */

echo 'starting example' . PHP_EOL;

require_once 'include/MemoryLimitManager/MemoryLimitManager.php';
$manager = new MemoryLimitManager();

$currentMemoryUsageInBytes = memory_get_usage(true);

$manager->setBufferInMegaBytes(0);
$manager->setLimitInBytes($currentMemoryUsageInBytes);

$data = array();

for ($iterator = 0; $iterator < 10; ++$iterator) {
	$data[$iterator] = true;
	if ($manager->isLimitReached()) {
		echo 'error - memory limit of ' . $manager->getLimitInBytes() . ' bytes reached (current usage: ' . $manager->getCurrentUsageInBytes() . ' bytes)' . PHP_EOL;
		exit(1);
	}
}

echo 'finished example with memory usage of ' . $manager->getCurrentUsageInMegaBytes() . ' mb' . PHP_EOL;