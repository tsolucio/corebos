<?php
/**
 * @author: stev leibelt <artodeto@bazzline.net>
 * @since: 2014-07-27
 */

/**
 * Class MemoryLimitManager
 * @package Net\Bazzline\Component\MemoryLimitManager
 */
class MemoryLimitManager {

	/**
	 * @var int
	 */
	private $bufferInBytes;

	/**
	 * @var int
	 */
	private $limitFromIniInBytes;

	/**
	 * @var int
	 */
	private $limitInBytes;

	public function __construct() {
		$currentMemoryLimitFromIni = trim(ini_get('memory_limit'));

		if (((int) $currentMemoryLimitFromIni) > 0) {
			$unitIdentifier = strtolower($currentMemoryLimitFromIni[strlen($currentMemoryLimitFromIni)-1]);
			$value = (int) substr($currentMemoryLimitFromIni, 0, -1);

			switch ($unitIdentifier) {
				case 'g':
					$this->limitFromIniInBytes = ($this->gigaBytesInBytes($value));
					break;
				case 'm':
					$this->limitFromIniInBytes = ($this->megaBytesInBytes($value));
					break;
				case 'k':
					$this->limitFromIniInBytes = ($this->kiloBytesInBytes($value));
					break;
				default:
					$this->limitFromIniInBytes = $value;
			}
		} else {
			$this->limitFromIniInBytes = 0;
		}

		$this->limitInBytes = $this->limitFromIniInBytes;
	}

	/**
	 * @return int
	 */
	public function getPHPLimitInBytes() {
		return $this->limitFromIniInBytes;
	}

	/**
	 * @return int
	 */
	public function getPHPLimitInKiloBytes() {
		return $this->bytesInKiloBytes($this->limitFromIniInBytes);
	}

	/**
	 * @return int
	 */
	public function getPHPLimitInMegaBytes() {
		return $this->bytesInMegaBytes($this->limitFromIniInBytes);
	}

	/**
	 * @return int
	 */
	public function getPHPLimitInGigaBytes() {
		return $this->bytesInGigaBytes($this->limitFromIniInBytes);
	}

	/**
	 * @param int $bytes
	 */
	public function setBufferInBytes($bytes) {
		$this->bufferInBytes = (int) $bytes;
	}

	/**
	 * @param int $kiloBytes
	 */
	public function setBufferInKiloBytes($kiloBytes) {
		$this->setBufferInBytes($this->kiloBytesInBytes($kiloBytes));
	}

	/**
	 * @param int $megaBytes
	 */
	public function setBufferInMegaBytes($megaBytes) {
		$this->setBufferInBytes($this->megaBytesInBytes($megaBytes));
	}

	/**
	 * @param int $gigaBytes
	 */
	public function setBufferInGigaBytes($gigaBytes) {
		$this->setBufferInBytes($this->gigaBytesInBytes($gigaBytes));
	}

	/**
	 * @return int
	 */
	public function getBufferInBytes() {
		return $this->bufferInBytes;
	}

	/**
	 * @return int
	 */
	public function getBufferInKiloBytes() {
		return $this->bytesInKiloBytes($this->bufferInBytes);
	}

	/**
	 * @return int
	 */
	public function getBufferInMegaBytes() {
		return $this->bytesInMegaBytes($this->bufferInBytes);
	}

	/**
	 * @return int
	 */
	public function getBufferInGigaBytes() {
		return $this->bytesInGigaBytes($this->bufferInBytes);
	}

	/**
	 * @param int $bytes
	 * @throws InvalidArgumentException
	 */
	public function setLimitInBytes($bytes) {
		if ($this->limitFromIniInBytes > 0 && $bytes > $this->limitFromIniInBytes) {
			throw new InvalidArgumentException('provided limit ('.$bytes.') is above ini limit ('.$this->limitFromIniInBytes.')', 1);
		}
		$this->limitInBytes = (int) $bytes;
	}

	/**
	 * @param int $kiloBytes
	 * @throws InvalidArgumentException
	 */
	public function setLimitInKiloBytes($kiloBytes) {
		$this->setLimitInBytes($this->kiloBytesInBytes($kiloBytes));
	}

	/**
	 * @param int $megaBytes
	 * @throws InvalidArgumentException
	 */
	public function setLimitInMegaBytes($megaBytes) {
		$this->setLimitInBytes($this->megaBytesInBytes($megaBytes));
	}

	/**
	 * @param int $gigaBytes
	 * @throws InvalidArgumentException
	 */
	public function setLimitInGigaBytes($gigaBytes) {
		$this->setLimitInBytes($this->gigaBytesInBytes($gigaBytes));
	}

	/**
	 * @return int
	 */
	public function getLimitInBytes() {
		return $this->limitInBytes;
	}

	/**
	 * @return int
	 */
	public function getLimitInKiloBytes() {
		return $this->bytesInKiloBytes($this->limitInBytes);
	}

	/**
	 * @return int
	 */
	public function getLimitInMegaBytes() {
		return $this->bytesInMegaBytes($this->limitInBytes);
	}

	/**
	 * @return int
	 */
	public function getLimitInGigaBytes() {
		return $this->bytesInGigaBytes($this->limitInBytes);
	}

	/**
	 * @param array $processIds
	 * @return int
	 */
	public function getCurrentUsageInBytes(array $processIds = array()) {
		$currentUsageInBytes = memory_get_usage(true);

		foreach ($processIds as $processId) {
			$return = 0;
			exec('ps -p ' . $processId . ' --no-headers -o rss', $return);

			if (isset($return[0])) {
				//non-swapped physical memory in kilo bytes
				$usageInBytes = (int) $return[0];
				$currentUsageInBytes += ($usageInBytes * 1024);
			}
		}

		return $currentUsageInBytes;
	}

	/**
	 * @param array $processIds
	 * @return int
	 */
	public function getCurrentUsageInKiloBytes(array $processIds = array()) {
		return $this->bytesInKiloBytes($this->getCurrentUsageInBytes($processIds));
	}

	/**
	 * @param array $processIds
	 * @return int
	 */
	public function getCurrentUsageInMegaBytes(array $processIds = array()) {
		return $this->bytesInMegaBytes($this->getCurrentUsageInBytes($processIds));
	}

	/**
	 * @param array $processIds
	 * @return int
	 */
	public function getCurrentUsageInGigaBytes(array $processIds = array()) {
		return $this->bytesInGigaBytes($this->getCurrentUsageInBytes($processIds));
	}

	/**
	 * @param array $processIds
	 * @return bool
	 */
	public function isLimitReached(array $processIds = array()) {
		$currentUsageInBytes = $this->getCurrentUsageInBytes($processIds);
		$currentUsageWithBufferInBytes = $currentUsageInBytes + $this->bufferInBytes;
		return ($currentUsageWithBufferInBytes >= $this->limitInBytes);
	}

	/**
	 * @param int $bytes
	 * @return int
	 */
	private function bytesInKiloBytes($bytes) {
		return ((int) ($bytes / 1024));
	}

	/**
	 * @param int $bytes
	 * @return int
	 */
	private function bytesInMegaBytes($bytes) {
		return ((int) ($bytes / 1048576));  //1048576 = 1024 * 1024
	}

	/**
	 * @param int $bytes
	 * @return int
	 */
	private function bytesInGigaBytes($bytes) {
		return ((int) ($bytes / 1073741824));   //1073741824 = 1024 * 1024 * 1024
	}

	/**
	 * @param int $kiloBytes
	 * @return int
	 */
	private function kiloBytesInBytes($kiloBytes) {
		return ((int) ($kiloBytes * 1024));
	}

	/**
	 * @param int $megaBytes
	 * @return int
	 */
	private function megaBytesInBytes($megaBytes) {
		return ((int) ($megaBytes * 1048576));  //1048576 = 1024 * 1024
	}

	/**
	 * @param int $gigaBytes
	 * @return int
	 */
	private function gigaBytesInBytes($gigaBytes) {
		return ((int) ($gigaBytes * 1073741824)); //1073741824 = 1024 * 1024 * 1024
	}
}
