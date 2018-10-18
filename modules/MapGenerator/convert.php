<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/
@include('modules/MapGenerator/flexviews/php-sql-parser.php');
include "modules/MapGenerator/dbclass.php";
$db=$_POST['nameDb'];
// istanza della classe
$data = new MysqlClass();
// chiamata alla funzione di connessione
$data->connetti($db);
if(!defined('HAVE_PHP_SQL_PARSER')){
	echo "You need to download the SQL parser component in order to use this tool. It is hosted separately here:\nhttp://php-sql-parser.googlecode.com/svn/trunk/php-sql-parser.php\n";
	exit;
}
$parser=new PHPSQLParser();
/*
if(count($argv) == 1) {
echo "usage: \n";
echo "php " . $argv[0] .  " [schema] < inserts.sql\n";
echo "-------------------------------------\n
This script expects a file to be redirected to STDIN. 

It expects the file to be a set of INSERT ... SELECT statements.\n
Each statement should be terminated by a semicolon (;)

The script takes one commandline argument. This argument specifies a schema.  The specified schema is used for:
A) The location into which to place the materialized view(s)
B) The location to use for tables that are not explicitly prefixed with a schema name in the FROM clause of the SELECT statement.\n\n";
exit;
}
*/
//$sql = file_get_contents('php://stdin');
$sql = $_POST['stringaSenzaHtml'];


process_sql($sql,@mysql_escape_string($db));

    
function process_parsed($p,$default_db = "") {
	$q = new StdClass();
	$q->mode = 'INCREMENTAL';
	if(!empty($p['GROUP'])) $q->group=true; else $q->group=false;

	$output = "";
	$notes = "";
	#process the select list

	foreach($p['SELECT'] as $s) {
		$expr = "CALL flexviews.add_expr(@mvid,'";
		switch ($s['expr_type']) {
			case 'colref':
				if($q->group) {
					$expr .= "GROUP','";
				} else {
					$expr .= "COLUMN','";
				}	
				$expr .= trim($s['base_expr']) . "','" . trim($s['alias'],'`') . "');\n";
			break;

			#treat aggregate functions special, otherwise it is just like a colref
			case 'expression':
				if($s['sub_tree'][0]['expr_type'] == "aggregate_function") {
					$expr .= $s['sub_tree'][0]['base_expr'] . "','";
					$expr .= trim($s['sub_tree'][1]['base_expr'],"() ") . "','" . trim($s['alias'],'`') . "');\n";
					
				} else {
					if($q->group) {
						$expr .= "GROUP','";
					} else {
						$expr .= "COLUMN','";
					}	
					$expr .= trim($s['base_expr']) . "','" . trim($s['alias'],'`') . "');\n";
				}
			break;

			default:
				echo "UNKNOWN:\n";
				print_r($s);
				exit;
		
		}
		$output .= $expr;
	}

	$output .= "\n";
	$first = true;
	foreach($p['FROM'] as $f) {
		#determine if it is schema.table or just table
		$info = explode('.',$f['table'],2);

		if(count($info) == 1) {
			$db = $default_db;
			$table =$f['table'];
		} else {
			$db = $info[0];
			$table = $info[1];
		}
		if($first) {
			$clause = "NULL";
			$first=0;
		} else {
			if(strtolower($f['ref_type']) == 'using') $f['ref_clause'] = '(' . $f['ref_clause'] . ')';
			$clause = "'" . $f['ref_type'] . ' ' . @mysql_escape_string($f['ref_clause']) . "'";
		}
		$table = str_replace('.','_',$table);
		$output .= "CALL flexviews.add_table(@mvid,'{$db}','{$table}','{$f['alias']}',{$clause});\n" ;

	}

	$where="";
	if(!empty($p['WHERE'])) foreach($p['WHERE'] as $w) {
		$where .= $w['base_expr'] . ' ';
	}
	$where=trim($where);

	if($where) {
		$where = @mysql_escape_string($where);
		$output .= "CALL flexviews.add_expr(@mvid,'WHERE','{$where}','where_clause');\n";
	}


	unset($p['SELECT']);
	unset($p['INSERT']);
	unset($p['FROM']);
	unset($p['WHERE']);

	return $output;

}


function process_sql($sql, $default_db="", $default_table="", $debug=false) {
	global $parser;

	$sql = preg_replace(array('/CREATE\s*TABLE\s*/i','/`/'), array('INSERT INTO ',''),$sql);

        $queries = explode(';', $sql);
        $new_queries = array();
        foreach($queries as $query) {
                $lines = explode("\n", $query);
                $out = "";
                foreach($lines as $line) {
                        if(substr(trim($line),0,2) == '--') continue;
                        if(substr(trim($line),0,2) == '/*') continue;
                        if(substr(trim($line),0,2) == '*/') continue;
                        if(substr(trim($line),0,1) == '#') continue;
                        if($out) $out .= " ";
                        $out .= str_replace("'","''",trim($line));
                }
                $new_queries[] = $out;
        }

        #we are done with the command line parameter.  use $sql for something else
        unset($sql);
	$output = "";
        foreach($new_queries as $sql) {
		if(!trim($sql)) continue;
		$parser->parse($sql);
		$p = $parser->parsed;

		if(!empty($p['INSERT'])) {
			$table=$p['INSERT']['table'];
		} else {
			$table = $default_table;
		}

		$info=explode('.',$table);

		if(count($info) == 1) {
			$db = $default_db;
			$table = $info[0];
		} else {
			$db = $info[0];
			$table = $info[1];
		}
                $output .= "CALL flexviews.create('{$db}', '{$table}', 'INCREMENTAL');\n";
                $output .= "SET @mvid := LAST_INSERT_ID();\n";

		$output .= process_parsed($p,$default_db);
                $output .= "CALL flexviews.enable(@mvid);\n\n";

        }
        $callArray = explode(';',$output);
        for($i=0;$i<count($callArray)-1;$i++){
            $callArray[$i]=$callArray[$i].';';
            $query=mysql_query($callArray[$i]);
        }
        if($query){
            echo "<b>La vista materializzata è stata creata!</b>";
        }

}

