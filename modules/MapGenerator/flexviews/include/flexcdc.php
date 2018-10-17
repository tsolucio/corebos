<?php
$percorsoAssolutoConsumer="/var/www2/flexviews/consumer/consumer.ini";

error_reporting(E_ALL);
ini_set('memory_limit', 1024 * 1024 * 1024);

/* 
The exit/die() functions normally exit with error code 0 when a string is passed in.
We want to exit with error code 1 when a string is passed in.
*/
function die1($error = 1,$error2=1) {
	if(is_string($error)) { 
		echo1($error . "\n");
		exit($error2);
	} else {
		exit($error);
	}
}

function echo1($message) {
	global $ERROR_FILE;
	fputs(isset($ERROR_FILE) && is_resource($ERROR_FILE) ? $ERROR_FILE : STDERR, $message);

}

function my_mysql_query($a, $b=NULL, $debug=true) {
	if($b) {
	$r = mysql_query($a, $b);
		} else { 
	$r = mysql_query($a);
	}

	if(!$r) {
		echo1("SQL_ERROR IN STATEMENT:\n$a\n");
		if($debug) {
			$pr = mysql_error($b);
			echo1(print_r(debug_backtrace(),true));
			echo1($pr);
		}
	}

	return $r;
}

class FlexCDC {
	static function concat() {
    	$result = "";
    	for ($i = 0;$i < func_num_args();$i++) {
      		$result .= func_get_arg($i);
    	}
    	return $result;
  	}
  	
  	static function split_sql($sql) {
		$regex=<<<EOREGEX
/
|(\(.*?\))   # Match FUNCTION(...) OR BAREWORDS
|("[^"](?:|\"|"")*?"+)
|('[^'](?:|\'|'')*?'+)
|(`(?:[^`]|``)*`+)
|([^ ,]+)
/x
EOREGEX
;
		$tokens = preg_split($regex, $sql,-1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		return $tokens;	

	}
  	
	# Settings to enable bulk import
	protected $inserts = array();
	protected $deletes = array();
	protected $bulk_insert = true;
  	
	protected $mvlogDB = NULL;
	public	$mvlogList = array();
	protected $activeDB = NULL;
	protected $onlyDatabases = array();
	protected $cmdLine;

	protected $tables = array();

	protected $mvlogs = 'mvlogs';
	protected $binlog_consumer_status = 'binlog_consumer_status';
	protected $mview_uow = 'mview_uow';

	protected $source = NULL;
	protected $dest = NULL;

	protected $serverId = NULL;
	
	protected $binlogServerId=1;
	
	public  $raiseWarnings = false;
	
	public  $delimiter = ';';
	public function get_source() {
		return $this->source;
	}
	
	public function get_dest() {
		return $this->dest;
	}

	#Construct a new consumer object.
	#By default read settings from the INI file unless they are passed
	#into the constructor	
	public function __construct($settings = NULL) {
		
		if(!$settings) {
			$settings = $this->read_settings();
		}
		if(!$this->cmdLine) $this->cmdLine = `which mysqlbinlog`;
		if(!$this->cmdLine) {
			die1("could not find mysqlbinlog!",2);
		}
		
		
		#only record changelogs from certain databases?
		if(!empty($settings['flexcdc']['only_database'])) {
			$vals = explode(',', $settings['flexcdc']['only_databases']);
			foreach($vals as $val) {
				$this->onlyDatabases[] = trim($val);
			}
		}

		if(!empty($settings['flexcdc']['mvlogs'])) $this->mvlogs=$settings['flexcdc']['mvlogs'];
		if(!empty($settings['flexcdc']['binlog_consumer_status'])) $this->binlog_consumer_status=$settings['flexcdc']['binlog_consumer_status'];
		if(!empty($settings['flexcdc']['mview_uow'])) $this->mview_uow=$settings['flexcdc']['mview_uow'];
		
		#the mysqlbinlog command line location may be set in the settings
		#we will autodetect the location if it is not specified explicitly
		if(!empty($settings['flexcdc']['mysqlbinlog'])) {
			$this->cmdLine = $settings['flexcdc']['mysqlbinlog'];
		} 
		
		#build the command line from user, host, password, socket options in the ini file in the [source] section
		foreach($settings['source'] as $k => $v) {
			$this->cmdLine .= " --$k=$v";
		}
		
		#database into which to write mvlogs
		$this->mvlogDB = $settings['flexcdc']['database'];
		
		$this->auto_changelog = $settings['flexcdc']['auto_changelog'];		
		#shortcuts
		$S = $settings['source'];
		$D = $settings['dest'];

		if(!empty($settings['raise_warnings']) && $settings['raise_warnings'] != 'false') {
 			$this->raiseWarnings=true;
		}

		if(!empty($settings['flexcdc']['bulk_insert']) && $settings['flexcdc']['bulk_insert'] != 'false') {
			$this->bulk_insert = true;
		}
	
		/*TODO: support unix domain sockets */
		$this->source = mysql_connect($S['host'] . ':' . $S['port'], $S['user'], $S['password'], true) or die1('Could not connect to MySQL server:' . mysql_error());
		$this->dest = mysql_connect($D['host'] . ':' . $D['port'], $D['user'], $D['password'], true) or die1('Could not connect to MySQL server:' . mysql_error());

		$this->settings = $settings;
	    
	}

	protected function initialize() {
		$this->initialize_dest();
		$this->get_source_logs();
		$this->cleanup_logs();
		
	}
	
	public function table_exists($schema, $table) {
		$sql = "select 1 from information_schema.tables where table_schema='%s' and table_name='%s'";
		$schema = mysql_real_escape_string($schema);
		$table  = mysql_real_escape_string($table, $this->dest);
		$sql = sprintf($sql, $schema, $table);
		$stmt = my_mysql_query($sql, $this->dest);
		if(mysql_fetch_array($stmt) !== false) {
			mysql_free_result($stmt);
			return true;
		}
		return false;
	}

	public function table_ordinal_datatype($schema,$table,$pos) {
		static $cache;

		$key = $schema . $table . $pos;
		if(!empty($cache[$key])) {
			return $cache[$key];
		}

		$log_name = $schema . '_' . $table;
		$table  = mysql_real_escape_string($table, $this->dest);
		$pos	= mysql_real_escape_string($pos);

		$sql = 'select data_type from information_schema.columns where table_schema="%s" and table_name="%s" and ordinal_position="%s"';

		$sql = sprintf($sql, $this->mvlogDB, $log_name, $pos+3);

		$stmt = my_mysql_query($sql, $this->dest);
		if($row = mysql_fetch_array($stmt) ) {
			$cache[$key] = $row[0];	
			return($row[0]);
		}
		return false;
			
		
	}
	
	public function setup($force=false , $only_table=false) {
		$sql = "SELECT @@server_id";
		$stmt = my_mysql_query($sql, $this->source);
		$row = mysql_fetch_array($stmt);
		$this->serverId = $row[0];
		if(!mysql_select_db($this->mvlogDB,$this->dest)) {
			 my_mysql_query('CREATE DATABASE ' . $this->mvlogDB) or die1('Could not CREATE DATABASE ' . $this->mvlogDB . "\n");
			 mysql_select_db($this->mvlogDB,$this->dest);
		}

		if($only_table === false || $only_table == 'mvlogs') {
			if($this->table_exists($this->mvlogDB, $this->mvlogs, $this->dest)) {
				if(!$force) {
					trigger_error('Table already exists:' . $this->mvlogs . '. Setup aborted! (use --force to ignore this error)' , E_USER_ERROR);
					return false;
				}
				my_mysql_query('DROP TABLE `' . $this->mvlogDB . '`.`' . $this->mvlogs . '`;') or die1('COULD NOT DROP TABLE: ' . $this->mvlogs . "\n" . mysql_error() . "\n");
			}	
			my_mysql_query("CREATE TABLE 
					 `" . $this->mvlogs . "` (table_schema varchar(50), 
                             table_name varchar(50), 
                             mvlog_name varchar(50),
                             active_flag boolean default true,
                             primary key(table_schema,table_name),
                             unique key(mvlog_name)
                     	) ENGINE=INNODB DEFAULT CHARSET=utf8;"
		            , $this->dest) or die1('COULD NOT CREATE TABLE ' . $this->mvlogs . ': ' . mysql_error($this->dest) . "\n"); 
		}

		if($only_table === false || $only_table == 'mview_uow') {
			if(FlexCDC::table_exists($this->mvlogDB, $this->mview_uow, $this->dest)) {
				if(!$force) {
					trigger_error('Table already exists:' . $this->mview_uow . '. Setup aborted!' , E_USER_ERROR);
					return false;
				}
				my_mysql_query('DROP TABLE `' . $this->mvlogDB . '`.`' . $this->mview_uow . '`;') or die1('COULD NOT DROP TABLE: ' . $this->mview_uow . "\n" . mysql_error() . "\n");
			}		            
			my_mysql_query("CREATE TABLE 
			 			 `" . $this->mview_uow . "` (
						  	`uow_id` BIGINT AUTO_INCREMENT,
						  	`commit_time` TIMESTAMP,
						  	PRIMARY KEY(`uow_id`),
						  	KEY `commit_time` (`commit_time`)
						) ENGINE=InnoDB DEFAULT CHARSET=latin1;"
				    , $this->dest) or die1('COULD NOT CREATE TABLE ' . $this->mview_uow . ': ' . mysql_error($this->dest) . "\n");
		}	
		if($only_table === false || $only_table == 'binlog_consumer_status') {
			if(FlexCDC::table_exists($this->mvlogDB, $this->binlog_consumer_status, $this->dest)) {
				if(!$force) {
					trigger_error('Table already exists:' . $this->binlog_consumer_status .'  Setup aborted!' , E_USER_ERROR);
					return false;
				}
				my_mysql_query('DROP TABLE `' . $this->mvlogDB . '`.`' . $this->binlog_consumer_status . '`;') or die1('COULD NOT DROP TABLE: ' . $this->binlog_consumer_status . "\n" . mysql_error() . "\n");
			}	
			my_mysql_query("CREATE TABLE 
						 `" . $this->binlog_consumer_status . "` (
  						 	`server_id` int not null, 
  							`master_log_file` varchar(100) NOT NULL DEFAULT '',
  							`master_log_size` int(11) DEFAULT NULL,
  							`exec_master_log_pos` int(11) default null,
  							PRIMARY KEY (`server_id`, `master_log_file`)
						  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			            , $this->dest) or die1('COULD NOT CREATE TABLE ' . $this->binlog_consumer_status . ': ' . mysql_error($this->dest) . "\n");
			
		
			#find the current master position
			$stmt = my_mysql_query('FLUSH TABLES WITH READ LOCK', $this->source) or die1(mysql_error($this->source));
			$stmt = my_mysql_query('SHOW MASTER STATUS', $this->source) or die1(mysql_error($this->source));
			$row = mysql_fetch_assoc($stmt);
			$stmt = my_mysql_query('UNLOCK TABLES', $this->source) or die1(mysql_error($this->source));
			$this->initialize();

			my_mysql_query("COMMIT;", $this->dest);
			
			$sql = "UPDATE `" . $this->binlog_consumer_status . "` bcs 
			           set exec_master_log_pos = master_log_size 
			         where server_id={$this->serverId} 
			           AND master_log_file < '{$row['File']}'";
			$stmt = my_mysql_query($sql, $this->dest) or die1($sql . "\n" . mysql_error($this->dest) . "\n");

			$sql = "UPDATE `" . $this->binlog_consumer_status . "` bcs 
			           set exec_master_log_pos = {$row['Position']} 
			         where server_id={$this->serverId} 
			           AND master_log_file = '{$row['File']}'";
			$stmt = my_mysql_query($sql, $this->dest) or die1($sql . "\n" . mysql_error($this->dest) . "\n");
		}
		
		my_mysql_query("commit;", $this->dest);
		
		return true;
		
			
	}
	#Capture changes from the source into the dest
	public function capture_changes($iterations=1) {
				
		$this->initialize();
		
		$count=0;
		$sleep_time=0;
		while($iterations <= 0 || ($iterations >0 && $count < $iterations)) {
			$this->initialize();
			#retrieve the list of logs which have not been fully processed
			#there won't be any logs if we just initialized the consumer above
			$sql = "SELECT bcs.* 
			          FROM `" . $this->mvlogDB . "`.`" . $this->binlog_consumer_status . "` bcs 
			         WHERE server_id=" . $this->serverId .  
			       "   AND exec_master_log_pos < master_log_size 
			         ORDER BY master_log_file;";
			
		
			#echo " -- Finding binary logs to process\n";
			$stmt = my_mysql_query($sql, $this->dest) or die1($sql . "\n" . mysql_error() . "\n");
			$processedLogs = 0;
			while($row = mysql_fetch_assoc($stmt)) {
				++$processedLogs;
				$this->delimiter = ';';
	
				if ($row['exec_master_log_pos'] < 4) $row['exec_master_log_pos'] = 4;
				$execCmdLine = sprintf("%s --base64-output=decode-rows -v -R --start-position=%d --stop-position=%d %s", $this->cmdLine, $row['exec_master_log_pos'], $row['master_log_size'], $row['master_log_file']);
				$execCmdLine .= " 2>&1";
				echo  "-- $execCmdLine\n";
				$proc = popen($execCmdLine, "r");
				if(!$proc) {
					die1('Could not read binary log using mysqlbinlog\n');
				}

				$line = fgets($proc);
				if(preg_match('%/mysqlbinlog:|^ERROR:%', $line)) {
					die1('Could not read binary log: ' . $line . "\n");
				}	

				$this->binlogPosition = $row['exec_master_log_pos'];
				$this->logName = $row['master_log_file'];
				$this->process_binlog($proc, $row['master_log_file'], $row['exec_master_log_pos'],$line);
				$this->set_capture_pos();	
				my_mysql_query('commit', $this->dest);
				pclose($proc);
			}

			if($processedLogs) ++$count;

			#we back off further each time up to maximum
			if(!empty($this->settings['flexcdc']['sleep_increment']) && !empty($this->settings['flexcdc']['sleep_maximum'])) {
				if($processedLogs) {
					$sleep_time=0;
				} else {
					$sleep_time += $this->settings['flexcdc']['sleep_increment'];
					$sleep_time = $sleep_time > $this->settings['flexcdc']['sleep_maximum'] ? $this->settings['flexcdc']['sleep_maximum'] : $sleep_time;
					#echo1('sleeping:' . $sleep_time . "\n");
					sleep($sleep_time);
				}
			}

		}
		return $processedLogs;

	}
	
	protected function read_settings() {
		
		if(!empty($argv[1])) {
			$iniFile = $argv[1];
		} else {
                        global $percorsoAssolutoConsumer;
			$iniFile = $percorsoAssolutoConsumer;
		}
	
		$settings=@parse_ini_file($iniFile,true) or die1("Could not read ini file: $iniFile\n");
		if(!$settings || empty($settings['flexcdc'])) {
			die1("Could not find [flexcdc] section or .ini file not found");
		}

		return $settings;

	}

	
	protected function refresh_mvlog_cache() {
		
		$this->mvlogList = array();
			
		$sql = "SELECT table_schema, table_name, mvlog_name from `" . $this->mvlogs . "` where active_flag=1";
		$stmt = my_mysql_query($sql, $this->dest);
		while($row = mysql_fetch_array($stmt)) {
			$this->mvlogList[$row[0] . $row[1]] = $row[2];
		}
	}
	
	/* Set up the destination connection */
	function initialize_dest() {
		#my_mysql_query("SELECT GET_LOCK('flexcdc::SOURCE_LOCK::" . $this->server_id . "',15)") or die1("COULD NOT OBTAIN LOCK\n");
		mysql_select_db($this->mvlogDB) or die1('COULD NOT CHANGE DATABASE TO:' . $this->mvlogDB . "\n");
		my_mysql_query("commit;", $this->dest);
		$stmt = my_mysql_query("SET SQL_LOG_BIN=0", $this->dest);
		if(!$stmt) die1(mysql_error());
		my_mysql_query("BEGIN;", $this->dest) or die1(mysql_error());

		$stmt = my_mysql_query("select @@max_allowed_packet", $this->dest);
		$row = mysql_fetch_array($stmt);
		$this->max_allowed_packet = $row[0];	

		#echo1("Max_allowed_packet: " . $this->max_allowed_packet . "\n");
		
	}
	
	/* Get the list of logs from the source and place them into a temporary table on the dest*/
	
	function get_source_logs() {
		/* This server id is not related to the server_id in the log.  It refers to the ID of the 
		 * machine we are reading logs from.
		 */
		$sql = "SELECT @@server_id";
		$stmt = my_mysql_query($sql, $this->source);
		$row = mysql_fetch_array($stmt) or die1($sql . "\n" . mysql_error() . "\n");
		$this->serverId = $row[0];


		$sql = "select @@binlog_format";
		$stmt = my_mysql_query($sql, $this->source);
		$row = mysql_fetch_array($stmt) or die1($sql . "\n" . mysql_error() . "\n");

		if($row[0] != 'ROW') {
			die1("Exiting due to error: FlexCDC REQUIRES that the source database be using ROW binlog_format!\n");
		}
		
		$stmt = my_mysql_query("SHOW BINARY LOGS", $this->source);
		if(!$stmt) die1(mysql_error());
		$has_logs = false;	
		while($row = mysql_fetch_array($stmt)) {
			if(!$has_logs) {
				my_mysql_query("CREATE TEMPORARY table log_list (log_name char(50), primary key(log_name))",$this->dest) or die1(mysql_error());
				$has_logs = true;
			}
			$sql = sprintf("INSERT INTO `" . $this->binlog_consumer_status . "` (server_id, master_log_file, master_log_size, exec_master_log_pos) values (%d, '%s', %d, 0) ON DUPLICATE KEY UPDATE master_log_size = %d ;", $this->serverId,$row['Log_name'], $row['File_size'], $row['File_size']);
			my_mysql_query($sql, $this->dest) or die1($sql . "\n" . mysql_error() . "\n");
	
			$sql = sprintf("INSERT INTO log_list (log_name) values ('%s')", $row['Log_name']);
			my_mysql_query($sql, $this->dest) or die1($sql . "\n" . mysql_error() . "\n");
		}
	}
	
	/* Remove any logs that have gone away */
	function cleanup_logs() {
		if(FlexCDC::table_exists($this->mvlogDB, 'log_list', $this->dest)) {	
			// TODO Detect if this is going to purge unconsumed logs as this means we either fell behind log cleanup, the master was reset or something else VERY BAD happened!
			$sql = "DELETE bcs.* FROM `" . $this->binlog_consumer_status . "` bcs where server_id={$this->serverId} AND master_log_file not in (select log_name from log_list)";
			my_mysql_query($sql, $this->dest) or die1($sql . "\n" . mysql_error() . "\n");
		} 

		$sql = "DROP TEMPORARY table log_list";
		my_mysql_query($sql, $this->dest) or die1("Could not drop TEMPORARY TABLE log_list\n");
		
	}

	/* Update the binlog_consumer_status table to indicate where we have executed to. */
	function set_capture_pos() {
		$sql = sprintf("UPDATE `" . $this->mvlogDB . "`.`" . $this->binlog_consumer_status . "` set exec_master_log_pos = %d where master_log_file = '%s' and server_id = %d", $this->binlogPosition, $this->logName, $this->serverId);
		
		my_mysql_query($sql, $this->dest) or die1("COULD NOT EXEC:\n$sql\n" . mysql_error($this->dest));
		
	}

	/* Called when a new transaction starts*/
	function start_transaction() {
		my_mysql_query("START TRANSACTION", $this->dest) or die1("COULD NOT START TRANSACTION;\n" . mysql_error());
        $this->set_capture_pos();
		$sql = sprintf("INSERT INTO `" . $this->mview_uow . "` values(NULL,str_to_date('%s', '%%y%%m%%d %%H:%%i:%%s'));",rtrim($this->timeStamp));
		my_mysql_query($sql,$this->dest) or die1("COULD NOT CREATE NEW UNIT OF WORK:\n$sql\n" .  mysql_error());
		 
		$sql = "SET @fv_uow_id := LAST_INSERT_ID();";
		my_mysql_query($sql, $this->dest) or die1("COULD NOT EXEC:\n$sql\n" . mysql_error($this->dest));

	}

    
    /* Called when a transaction commits */
	function commit_transaction() {
		//Handle bulk insertion of changes
		if(!empty($this->inserts) || !empty($this->deletes)) {
			$this->process_rows();
		}
		$this->inserts = $this->deletes = $this->tables = array();

		$this->set_capture_pos();
		my_mysql_query("COMMIT", $this->dest) or die1("COULD NOT COMMIT TRANSACTION;\n" . mysql_error());
	}

	/* Called when a transaction rolls back */
	function rollback_transaction() {
		$this->inserts = $this->deletes = $this->tables = array();
		my_mysql_query("ROLLBACK", $this->dest) or die1("COULD NOT ROLLBACK TRANSACTION;\n" . mysql_error());
		#update the capture position and commit, because we don't want to keep reading a truncated log
		$this->set_capture_pos();
		my_mysql_query("COMMIT", $this->dest) or die1("COULD NOT COMMIT TRANSACTION LOG POSITION UPDATE;\n" . mysql_error());
		
	}

	/* Called when a row is deleted, or for the old image of an UPDATE */
	function delete_row() {
		$key = '`' . $this->mvlogDB . '`.`' . $this->mvlog_table . '`';
		$this->tables[$key]=array('schema'=>$this->db ,'table'=>$this->base_table); 
		if ( $this->bulk_insert ) {
			if(empty($this->deletes[$key])) $this->deletes[$key] = array();
			$this->deletes[$key][] = $this->row;
			if(count($this->deletes[$key]) >= 10000) {
				$this->process_rows();	
			}
		} else {
			$row=array();
			foreach($this->row as $col) {
				if($col[0] == "'") {
					 $col = trim($col,"'");
				}
				$col = mysql_real_escape_string($col);
				$row[] = "'$col'";
			}
			$valList = "(-1, @fv_uow_id, {$this->binlogServerId}," . implode(",", $row) . ")";
			$sql = sprintf("INSERT INTO `%s`.`%s` VALUES %s", $this->mvlogDB, $this->mvlog_table, $valList );
			my_mysql_query($sql, $this->dest) or die1("COULD NOT EXEC SQL:\n$sql\n" . mysql_error() . "\n");
		}
	}

	/* Called when a row is inserted, or for the new image of an UPDATE */
	function insert_row() {
		$key = '`' . $this->mvlogDB . '`.`' . $this->mvlog_table . '`';
		$this->tables[$key]=array('schema'=>$this->db ,'table'=>$this->base_table); 
		if ( $this->bulk_insert ) {
			if(empty($this->inserts[$key])) $this->inserts[$key] = array();
			$this->inserts[$key][] = $this->row;
			if(count($this->inserts[$key]) >= 10000) {
				$this->process_rows();	
			}
		} else {
			$row=array();
			foreach($this->row as $col) {
				if($col[0] == "'") {
					 $col = trim($col,"'");
				}
				$col = mysql_real_escape_string($col);
				$row[] = "'$col'";
			}
			$valList = "(1, @fv_uow_id, $this->binlogServerId," . implode(",", $row) . ")";
			$sql = sprintf("INSERT INTO `%s`.`%s` VALUES %s", $this->mvlogDB, $this->mvlog_table, $valList );
			my_mysql_query($sql, $this->dest) or die1("COULD NOT EXEC SQL:\n$sql\n" . mysql_error() . "\n");
		}
	}

	function process_rows() {
		$i = 0;
		
		
		while($i<2) {
			$valList =  "";
			if ($i==0) {
				$data = $this->inserts;
				$mode = 1;
			} else {
				$data = $this->deletes;
				$mode = -1;
			}		
			$tables = array_keys($data);
			foreach($tables as $table) {
				$rows = $data[$table];	
				
				$sql = sprintf("INSERT INTO %s VALUES ", $table);
				foreach($rows as $the_row) {	
					$row = array();
					
					foreach($the_row as $col) {
						if($col[0] == "'") {
							$col = "'" . mysql_real_escape_string(trim($col,"'")) . "'";
							
						}
						$datatype = $this->table_ordinal_datatype($this->tables[$table]['schema'],$this->tables[$table]['table'],count($row)+1);
						echo1("DATATYPE: $datatype\n");
						switch(trim($datatype)) {
							case 'int':
									echo1("COL: $col\n");
								if($col[0] == "-" && strpos($col, '(')) {
									$col = trim(strstr($col,'('), '()');
								}
							break;

							case 'timestamp':
								$col = 'from_unixtime(' . $col . ')';
							break;

							case 'datetime': 
								$col = "'" . mysql_real_escape_string(trim($col,"'")) . "'";
							break;
						}

						$row[] = $col;
					}

					if($valList) $valList .= ",\n";	
					$valList .= "($mode, @fv_uow_id, $this->binlogServerId," . implode(",", $row) . ")";
					$bytes = strlen($valList) + strlen($sql);
					$allowed = floor($this->max_allowed_packet * .9);  #allowed len is 90% of max_allowed_packet	
					if($bytes > $allowed) {
						my_mysql_query($sql . $valList, $this->dest) or die1("COULD NOT EXEC SQL:\n$sql\n" . mysql_error() . "\n");
						$valList = "";
					}
					
				}
				if($valList) {
					my_mysql_query($sql . $valList, $this->dest) or die1("COULD NOT EXEC SQL:\n$sql\n" . mysql_error() . "\n");
				}
			}

			++$i;
		}

		unset($this->inserts);
		unset($this->deletes);
		$this->inserts = array();
		$this->deletes = array();

	}

	/* Called for statements in the binlog.  It is possible that this can be called more than
	 * one time per event.  If there is a SET INSERT_ID, SET TIMESTAMP, etc
	 */	
	function statement($sql) {

		$sql = trim($sql);
		#TODO: Not sure  if this might be important..
		#      In general, I think we need to worry about character
		#      set way more than we do (which is not at all)
		if(substr($sql,0,6) == '/*!\C ') {
			return;
		}
		
		if($sql[0] == '/') {
			$end_comment = strpos($sql, ' ');
			$sql = trim(substr($sql, $end_comment, strlen($sql) - $end_comment));
		}
		
		preg_match("/([^ ]+)(.*)/", $sql, $matches);
		
		//print_r($matches);
		
		$command = $matches[1];
		$command = str_replace($this->delimiter,'', $command);
		$args = $matches[2];
	
		switch(strtoupper($command)) {
			#register change in delimiter so that we properly capture statements
			case 'DELIMITER':
				$this->delimiter = trim($args);
				break;
				
			#ignore SET and USE for now.  I don't think we need it for anything.
			case 'SET':
				break;
			case 'USE':
				$this->activeDB = trim($args);	
				$this->activeDB = str_replace($this->delimiter,'', $this->activeDB);
				break;
				
			#NEW TRANSACTION
			case 'BEGIN':
				$this->start_transaction();
				break;
			#END OF BINLOG, or binlog terminated early, or mysqlbinlog had an error
			case 'ROLLBACK':
				$this->rollback_transaction();
				break;
				
			case 'COMMIT':
				$this->commit_transaction();
				break;
				
			#Might be interestested in CREATE statements at some point, but not right now.
			case 'CREATE':
				break;
				
			#DML IS BAD....... :(
			case 'INSERT':
			case 'UPDATE':
			case 'DELETE':
			case 'REPLACE':
			case 'TRUNCATE':
				/* TODO: If the table is not being logged, ignore DML on it... */
				if($this->raiseWarnings) trigger_error('Detected statement DML on a table!  Changes can not be tracked!' , E_USER_WARNING);
				break;

			case 'RENAME':
/*
				
				#TODO: Find some way to make atomic rename atomic.  split it up for now
				$tokens = FlexCDC::split_sql($sql);
				
				$clauses=array();
				$new_sql = '';
				$clause = "";
				for($i=4;$i<count($tokens);++$i) {
					#grab each alteration clause (like add column, add key or drop column)
					if($tokens[$i] == ',') {
						$clauses[] = $clause;
						$clause = "";
					} else {
						$clause .= $tokens[$i]; 
					}		
				}
				if($clause) $clauses[] = $clause;
				$new_clauses = "";
				
				foreach($clauses as $clause) {
					
					$clause = trim(str_replace($this->delimiter, '', $clause));
					$tokens = FlexCDC::split_sql($clause);
					$old_table = $tokens[0];
					if(strpos($old_table, '.') === false) {
						$old_base_table = $old_table;
						$old_table = $this->activeDB . '.' . $old_table;
						$old_schema = $this->activeDB;
						
					} else {
						$s = explode(".", $old_table);
						$old_schema = $s[0];
						$old_base_table = $s[1];
					}
					$old_log_table = str_replace('.','_',$old_table);
					
					$new_table = $tokens[4];
					if(strpos($new_table, '.') === false) {
						$new_schema = $this->activeDB;
						$new_base_table = $new_table;
						$new_table = $this->activeDB . '.' . $new_table;
						
					} else {
						$s = explode(".", $new_table);
						$new_schema = $s[0];
						$new_base_table = $s[1];
					}
					
					$new_log_table = str_replace('.', '_', $new_table);
										
					$clause = "$old_log_table TO $new_log_table";
							
					
					$sql = "DELETE from `" . $this->mvlogs . "` where table_name='$old_base_table' and table_schema='$old_schema'";
					
					my_mysql_query($sql, $this->dest) or die1($sql . "\n" . mysql_error($this->dest) . "\n");
					$sql = "REPLACE INTO `" . $this->mvlogs . "` (mvlog_name, table_name, table_schema) values ('$new_log_table', '$new_base_table', '$new_schema')";
					my_mysql_query($sql, $this->dest) or die1($sql . "\n" . mysql_error($this->dest) . "\n");
					
					$sql = 'RENAME TABLE ' . $clause;
					@my_mysql_query($sql, $this->dest);# or die1('DURING RENAME:\n' . $new_sql . "\n" . mysql_error($this->dest) . "\n");
					my_mysql_query('commit', $this->dest);					
				
					$this->mvlogList = array();
					$this->refresh_mvlog_cache();
					
					
				}
						
			*/	
				break;
			#ALTER we can deal with via some clever regex, when I get to it.  Need a test case
			#with some complex alters
			case 'ALTER':
				/* TODO: If the table is not being logged, ignore ALTER on it...  If it is being logged, modify ALTER appropriately and apply to the log.*/
				$tokens = FlexCDC::split_sql($sql);
				$is_alter_table = -1;
				foreach($tokens as $key => $token) {
					if(strtoupper($token) == 'TABLE') {
						$is_alter_table = $key;
						break;
					}
				}
				if(!preg_match('/\s+table\s+([^ ]+)/i', $sql, $matches)) return;
				
				if(empty($this->mvlogList[str_replace('.','',trim($matches[1]))])) {
					return;
				}
				$table = $matches[1];
				#switch table name to the log table
				if(strpos($table, '.')) {
				  $s = explode('.', $table);
				  $old_schema = $s[0];
				  $old_base_table = $s[1];
				} else {
				  $old_schema = $this->activeDB;
				  $old_base_table = $table;
				}
				unset($table);
				
				$old_log_table = $s[0] . '_' . $s[1];
				
				#IGNORE ALTER TYPES OTHER THAN TABLE
				if($is_alter_table>-1) {
					$clauses = array();
					$clause = "";

					for($i=$is_alter_table+4;$i<count($tokens);++$i) {
						#grab each alteration clause (like add column, add key or drop column)
						if($tokens[$i] == ',') {
							$clauses[] = $clause;
							$clause = "";
						} else {
							$clause .= $tokens[$i]; 
						}		
					}	
					$clauses[] = $clause;
					
					
					$new_clauses = "";
					$new_log_table="";
					$new_schema="";
					$new_base_Table="";
					foreach($clauses as $clause) {
						$clause = trim(str_replace($this->delimiter, '', $clause));
						
						#skip clauses we do not want to apply to mvlogs
						if(!preg_match('/^ORDER|^DISABLE|^ENABLE|^ADD CONSTRAINT|^ADD FOREIGN|^ADD FULLTEXT|^ADD SPATIAL|^DROP FOREIGN|^ADD KEY|^ADD INDEX|^DROP KEY|^DROP INDEX|^ADD PRIMARY|^DROP PRIMARY|^ADD PARTITION|^DROP PARTITION|^COALESCE|^REORGANIZE|^ANALYZE|^CHECK|^OPTIMIZE|^REBUILD|^REPAIR|^PARTITION|^REMOVE/i', $clause)) {
							
							#we have three "header" columns in the mvlog.  Make it so that columns added as
							#the FIRST column on the table go after our header columns.
							$tokens = preg_split('/\s/', $clause);
														
							if(strtoupper($tokens[0]) == 'RENAME') {
								if(strtoupper(trim($tokens[1])) == 'TO') {
									$tokens[1] = $tokens[2];
								}
								
								if(strpos($tokens[1], '.') !== false) {
									$new_log_table = $tokens[1];
									$s = explode(".", $tokens[1]);
									$new_schema = $s[0];
									$new_base_table = $s[1];
								} else {
									$new_base_table = $tokens[1];
									$new_log_table = $this->activeDB . '.' . $tokens[1];
								}
								$new_log_table = str_replace('.', '_', $new_log_table);
								$clause = "RENAME TO $new_log_table";
																			
							}
							
							if(strtoupper($tokens[0]) == 'ADD' && strtoupper($tokens[count($tokens)-1]) == 'FIRST') {
								$tokens[count($tokens)-1] = 'AFTER `fv$server_id`';
								$clause = join(' ', $tokens);
							}
							if($new_clauses) $new_clauses .= ', ';
							$new_clauses .= $clause;
						}
					}
					if($new_clauses) {
						$new_alter = 'ALTER TABLE ' . $old_log_table . ' ' . $new_clauses;
						
						my_mysql_query($new_alter, $this->dest) or die1($new_alter. "\n" . mysql_error($this->dest) . "\n");
						if($new_log_table) {
							$sql = "DELETE from `" . $this->mvlogs . "` where table_name='$old_base_table' and table_schema='$old_schema'";
							my_mysql_query($sql, $this->dest) or die1($sql . "\n" . mysql_error($this->dest) . "\n");

							$sql = "INSERT INTO `" . $this->mvlogs . "` (mvlog_name, table_name, table_schema) values ('$new_log_table', '$new_base_table', '$new_schema')";
							
							my_mysql_query($sql, $this->dest) or die1($sql . "\n" . mysql_error($this->dest) . "\n");
							$this->mvlogList = array();
							$this->refresh_mvlog_cache();
						}
					}
				}	
											 
				break;

			#DROP probably isn't bad.  We might be left with an orphaned change log.	
			case 'DROP':
				/* TODO: If the table is not being logged, ignore DROP on it.  
				 *       If it is being logged then drop the log and maybe any materialized views that use the table.. 
				 *       Maybe throw an errro if there are materialized views that use a table which is dropped... (TBD)*/
				if($this->raiseWarnings) trigger_error('Detected DROP on a table!  This may break CDC, particularly if the table is recreated with a different structure.' , E_USER_WARNING);
				break;
				
			#I might have missed something important.  Catch it.	
			#Maybe this should be E_USER_ERROR
			default:
				#if($this->raiseWarnings) trigger_error('Unknown command: ' . $command, E_USER_WARNING);
				if($this->raiseWarnings) trigger_error('Unknown command: ' . $command, E_USER_WARNING);
				break;
		}
	}
	
	static function ignore_clause($clause) {
		$clause = trim($clause);
		if(preg_match('/^(?:ADD|DROP)\s+(?:PRIMARY KEY|KEY|INDEX)')) {
			return true;
		}
		return false;
	} 
	
	function process_binlog($proc, $lastLine="") {
		$binlogStatement="";
		$this->timeStamp = false;

		$this->refresh_mvlog_cache();
		$sql = "";

		#read from the mysqlbinlog process one line at a time.
		#note the $lastLine variable - we process rowchange events
		#in another procedure which also reads from $proc, and we
		#can't seek backwards, so this function returns the next line to process
		#In this case we use that line instead of reading from the file again
		while( !feof($proc) ) {
			if($lastLine) {
				#use a previously saved line (from process_rowlog)
				$line = $lastLine;
				$lastLine = "";
			} else {
				#read from the process
				$line = trim(fgets($proc));
			}

			#echo "-- $line\n";
			#It is faster to check substr of the line than to run regex
			#on each line.
			$prefix=substr($line, 0, 5);
			if($prefix=="ERROR") {
				if(preg_match('/Got error/', $line)) 
				die1("error from mysqlbinlog: $line");
			}
			$matches = array();

			#Control information from MySQLbinlog is prefixed with a hash comment.
			if($prefix[0] == "#") {
				$binlogStatement = "";
				if (preg_match('/^#([0-9]+\s+[0-9:]+)\s+server\s+id\s+([0-9]+)\s+end_log_pos ([0-9]+).*/', $line,$matches)) {
					$this->timeStamp = $matches[1];
					$this->binlogPosition = $matches[3];
					$this->binlogServerId = $matches[2];
					#$this->set_capture_pos();
				} else {
					#decoded RBR changes are prefixed with ###				
					if($prefix == "### I" || $prefix == "### U" || $prefix == "### D") {
						if(preg_match('/### (UPDATE|INSERT INTO|DELETE FROM)\s([^.]+)\.(.*$)/', $line, $matches)) {
							$this->db          = $matches[2];
							$this->base_table  = $matches[3];
						
							if($this->db == $this->mvlogDB && $this->base_table == $this->mvlogs) {
								$this->refresh_mvlog_cache();
							}
							
							if(empty($this->mvlogList[$this->db . $this->base_table])) {
								if($this->auto_changelog && !strstr($this->base_table,'_delta') ) {
								 		$this->create_mvlog($this->db, $this->base_table);  
								 		$this->refresh_mvlog_cache();
								}
							}
							if(!empty($this->mvlogList[$this->db . $this->base_table])) {
								$this->mvlog_table = $this->mvlogList[$this->db . $this->base_table];
								$lastLine = $this->process_rowlog($proc, $line);
							}
							
						}
					} 
				}
		 
			}	else {
				
				if($binlogStatement) {
					$binlogStatement .= " ";
				}
				$binlogStatement .= $line;
				$pos=false;				
				if(($pos = strpos($binlogStatement, $this->delimiter)) !== false)  {
					#process statement
					$this->statement($binlogStatement);
					$binlogStatement = "";
				} 
			}
		}
	}
	
	
	function process_rowlog($proc) {
		$sql = "";
		$skip_rows = false;
		$line = "";
		#if there is a list of databases, and this database is not on the list
		#then skip the rows
		if(!empty($this->onlyDatabases) && empty($this->onlyDatabases[trim($this->db)])) {
			$skip_rows = true;
		}

		# loop over the input, collecting all the input values into a set of INSERT statements
		$this->row = array();
		$mode = 0;
		
		while($line = fgets($proc)) {
			$line = trim($line);	
            #DELETE and UPDATE statements contain a WHERE clause with the OLD row image
			if($line == "### WHERE") {
				if(!empty($this->row)) {
					switch($mode) {
						case -1:
							$this->delete_row();
							break;
						case 1:
							$this->insert_row();
							break;
						default:
							die1('UNEXPECTED MODE IN PROCESS_ROWLOG!');
					}
					$this->row = array();
				}
				$mode = -1;
				
			#INSERT and UPDATE statements contain a SET clause with the NEW row image
			} elseif($line == "### SET")  {
				if(!empty($this->row)) {
					switch($mode) {
						case -1:
							$this->delete_row();
							break;
						case 1:
							$this->insert_row();
							break;
						default:
							die1('UNEXPECTED MODE IN PROCESS_ROWLOG!');
					}
					$this->row = array();
				}
				$mode = 1;
			} elseif(preg_match('/###\s+@[0-9]+=(-[0-9]*) .*$/', $line, $matches)) {
				$this->row[] = $matches[1];
			
			#Row images are in format @1 = 'abc'
			#                         @2 = 'def'
			#Where @1, @2 are the column number in the table	
			} elseif(preg_match('/###\s+@[0-9]+=(.*)$/', $line, $matches)) {
				$this->row[] = $matches[1];

			#This line does not start with ### so we are at the end of the images	
			} else {
				#echo ":: $line\n";
				if(!$skip_rows) {
					switch($mode) {
						case -1:
							$this->delete_row();
							break;
						case 1:
							$this->insert_row();
							break;
						default:
							die1('UNEXPECTED MODE IN PROCESS_ROWLOG!');
					}					
				} 
				$this->row = array();
				break; #out of while
			}
			#keep reading lines
		}
		#return the last line so that we can process it in the parent body
		#you can't seek backwards in a proc stream...
		return $line;
	}

	function drop_mvlog($schema, $table) {

		#will implicit commit	
		$sql = "DROP TABLE IF EXISTS " . $this->mvlogDB . "." . "`%s_%s`";	
		$sql = sprintf($sql, mysql_real_escape_string($schema), mysql_real_escape_string($table));
		if(!my_mysql_query($sql)) return false;

		my_mysql_query("BEGIN", $this->dest);
		$sql = "DELETE FROM " . $this->mvlogDB . ". " . $this->mvlogs . " where table_schema = '%s' and table_name = '%s'";	
		$sql = sprintf($sql, mysql_real_escape_string($schema), mysql_real_escape_string($table));
		if(!my_mysql_query($sql)) return false;

		return my_mysql_query('commit');

	}

	#AUTOPORTED FROM FLEXVIEWS.CREATE_MVLOG() w/ minor modifications for PHP
	function create_mvlog($v_schema_name,$v_table_name) { 
		$v_done=FALSE;
		$v_column_name=NULL;
		$v_data_type=NULL;
		$v_sql=NULL;
	
		$cursor_sql = "SELECT COLUMN_NAME, IF(COLUMN_TYPE='TIMESTAMP', 'TIMESTAMP', COLUMN_TYPE) COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='$v_table_name' AND TABLE_SCHEMA = '$v_schema_name'";
	
		$cur_columns = my_mysql_query($cursor_sql, $this->source);
		$v_sql = '';
	
		while(1) {
			if( $v_sql != '' ) {
				$v_sql = FlexCDC::concat($v_sql, ', ');
			}
	
			$row = mysql_fetch_array($cur_columns);
			if( $row === false ) $v_done = true;
	
			if( $row ) {
				$v_column_name = $row[0];
				$v_data_type = $row[1];
			}
	
			if( $v_done ) {
				mysql_free_result($cur_columns);
				break;
			}
	
			$v_sql = FlexCDC::concat($v_sql, $v_column_name, ' ', $v_data_type);
		}
	
		if( trim( $v_sql ) == "" ) {
			trigger_error('Could not access table:' . $v_table_name, E_USER_ERROR);
		}
			
		$v_sql = FlexCDC::concat('CREATE TABLE IF NOT EXISTS`', $this->mvlogDB ,'`.`' ,$v_schema_name, '_', $v_table_name,'` ( dml_type INT DEFAULT 0, uow_id BIGINT, `fv$server_id` INT UNSIGNED, ', $v_sql, 'KEY(uow_id, dml_type) ) ENGINE=INNODB');
		$create_stmt = my_mysql_query($v_sql, $this->dest);
		if(!$create_stmt) die1('COULD NOT CREATE MVLOG. ' . $v_sql . "\n");
		$exec_sql = " INSERT IGNORE INTO `". $this->mvlogDB . "`.`" . $this->mvlogs . "`( table_schema , table_name , mvlog_name ) values('$v_schema_name', '$v_table_name', '" . $v_schema_name . "_" . $v_table_name . "')";
		my_mysql_query($exec_sql) or die1($exec_sql . ':' . mysql_error($this->dest) . "\n");

		return true;
	
	}
}

