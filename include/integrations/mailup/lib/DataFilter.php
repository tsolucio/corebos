<?php
class DataFilter {

	public static function convertToString($raw_data) {
		$trimmed_string = trim($raw_data);
		return !is_string($trimmed_string) ? strval($trimmed_string) : $trimmed_string;
	}

	public static function sanitizeString($string) {
		return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
	}
}