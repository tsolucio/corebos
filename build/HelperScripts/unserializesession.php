<?php
/**
 * HOW TO DEBUG SESSION ERROR IN coreBOS
 *
 * vi vendor/stefangabos/zebra_session/Zebra_Session.php
 * Add on line 535, before the IF ($result)
 * global $log; $log->fatal($result['data']['session_data']);
 *
 * reproduce the error and look in the log file, you should see a series of session logs
 * until a destroy happens (an empty session). The session previous to the empty one is
 * the one that is broken.
 *
 * This is what an empty session looks like: {"num_rows":0,"data":null}
 *
 * Use this script to find out which part is incorrect
 * Then find the code that generates that value and fix accordingly
 *
 */

function unserialize_php($session_data, $dumpOnlyError = false) {
	$return_data = array();
	$offset = 0;
	while ($offset < strlen($session_data)) {
		if (!strstr(substr($session_data, $offset), '|')) {
			echo 'invalid data, remaining: '.substr($session_data, $offset);
			return $return_data;
		}
		$pos = strpos($session_data, '|', $offset);
		$num = $pos - $offset;
		$varname = substr($session_data, $offset, $num);
		$offset += $num + 1;
		try {
			$data = unserialize(substr($session_data, $offset));
			if (!$dumpOnlyError) {
				var_dump(array($varname => $data));
			}
		} catch (Exception $e) {
			echo "\nERROR!\n";
			var_dump($e);
			var_dump(array($varname => substr($session_data, $offset)));
		}
		$return_data[$varname] = $data;
		$offset += strlen(serialize($data));
	}
	return $return_data;
}

$session_data = 'pbss_date_start|s:10:"2018-03-01";pbss_date_end|s:10:"2018-03-31";pbss_ids|a:4:{i:0;s:2:"97";i:1;s:2:"42";i:2;s:2:"78";i:3;s:2:"98";}lsbo_lead_sources|a:9:{i:0;s:8:"--None--";i:1;s:8:"Facebook";i:2;s:6:"Google";i:3;s:2:"TV";i:4;s:13:"Site Próprio";i:5;s:4:"Chat";i:6;s:11:"Indicação";i:7;s:9:"RDStation";i:8;s:9:"Instagram";}lsbo_ids|a:7:{i:0;s:2:"91";i:1;s:2:"67";i:2;s:2:"64";i:3;s:2:"61";i:4;s:2:"50";i:5;s:2:"65";i:6;s:2:"49";}__CBOSSession_Info|i:2;KCFINDER|a:4:{s:8:"disabled";b:0;s:9:"uploadURL";s:57:"https://maiscredit.novavrs.ecrm360.cloud/storage/kcimages";s:9:"uploadDir";s:50:"/usr/share/eCRM360/ecrmweb-master/storage/kcimages";s:10:"deniedExts";s:114:"php php3 php4 php5 pl cgi py asp cfm js vbs html htm exe bin bat sh dll phps phtml xhtml rb msi jsp shtml sth shtm";}USER_PREFERENCES|a:5:{s:15:"pbss_date_start";s:10:"2018-03-01";s:13:"pbss_date_end";s:10:"2018-03-31";s:8:"pbss_ids";a:4:{i:0;s:2:"97";i:1;s:2:"42";i:2;s:2:"78";i:3;s:2:"98";}s:17:"lsbo_lead_sources";a:9:{i:0;s:8:"--None--";i:1;s:8:"Facebook";i:2;s:6:"Google";i:3;s:2:"TV";i:4;s:13:"Site Próprio";i:5;s:4:"Chat";i:6;s:11:"Indicação";i:7;s:9:"RDStation";i:8;s:9:"Instagram";}s:8:"lsbo_ids";a:7:{i:0;s:2:"91";i:1;s:2:"67";i:2;s:2:"64";i:3;s:2:"61";i:4;s:2:"50";i:5;s:2:"65";i:6;s:2:"49";}}authenticated_user_id|s:1:"1";app_unique_key|s:32:"d41804f7d8d23cc50e69bd5b866eea44";conn_unique_key|s:13:"62d9afd1b352f";vtiger_authenticated_user_theme|s:6:"softed";authenticated_user_language|s:5:"pt_br";internal_mailer|s:1:"0";__UnifiedSearch_SelectedModules__|a:4:{i:0;s:9:"CobroPago";i:1;s:8:"Contacts";i:2;s:10:"Potentials";i:3;s:16:"ServiceContracts";}last_reminder_check_time|i:1658433552;next_reminder_interval|i:60;next_reminder_time|i:1658433612;';
unserialize_php($session_data, false);
