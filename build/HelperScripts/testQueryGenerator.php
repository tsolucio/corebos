<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>TSolucio::coreBOS Customizations</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>
</head><body style="font-size: 14px; margin: 2px; padding: 2px; background-color:#f7fff3; ">
<table width="100%" border=0><tr><td><span style='color:red;float:right;margin-right:30px;'><h2>Proud member of the <a href='http://corebos.org'>coreBOS</a> family!</h2></span></td></tr></table>
<hr style="height: 1px">
<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Module.php');
global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit','1024M');
$current_user = Users::getActiveAdminUser();

function testquery($query) {
	global $adb;
	$rs = $adb->query($query);
	if ($rs) {
		echo '<span style="color:green">Query OK</span><br>';
	} else {
		echo '<span style="color:red">Query NOK</span><br>';
	}
}

$moduleName = 'Accounts';

echo "<h2>Query with ID field</h2>";
$queryGenerator = new QueryGenerator($moduleName, $current_user);
$queryGenerator->setFields(array('id'));
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);

echo "<h2>Query with custom field</h2>";
$queryGenerator = new QueryGenerator($moduleName, $current_user);
$queryGenerator->setFields(array('id','cf_681'));
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);

echo "<h2>Query with invalid and non-accessible fields</h2>";
echo "<b>The invalid fields and fields the current user does not have permission to access are eliminated</b><br>";
$hold_user = $current_user;
$user = new Users();
$user->retrieveCurrentUserInfoFromFile(5);  // 5 is a normal user that does not have access to cf_681
$queryGenerator = new QueryGenerator($moduleName, $user);
$queryGenerator->setFields(array('id','cf_681','acname'));
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);
$current_user = $hold_user;

echo "<h2>Query as individual parts</h2>";
echo "<b>We can get the different parts of the query individually so we can construct specific queries easily</b><br>";
$queryGenerator = new QueryGenerator($moduleName, $current_user);
$queryGenerator->setFields(array('id','cf_681','accountname'));
echo "<b>Full query:</b><br>";
$query = $queryGenerator->getQuery();
echo "$query<br>";
echo "<b>SELECT:</b><br>";
echo $queryGenerator->getSelectClauseColumnSQL();
echo "<br><b>FROM:</b><br>";
echo $queryGenerator->getFromClause();
echo "<br><b>WHERE:</b><br>";
echo $queryGenerator->getWhereClause();
echo "<br>";
testquery($query);

echo "<h2>Query with conditions</h2>";
echo "<b>Supported operators:</b><br>";
echo "&nbsp;'e'&nbsp;= = value  (equals)<br>";
echo "&nbsp;'n'&nbsp;= <> value  (not equal)<br>";
echo "&nbsp;'s'&nbsp;= LIKE $value%  (starts with)<br>";
echo "&nbsp;'ew'&nbsp;= LIKE %$value  (ends with)<br>";
echo "&nbsp;'c'&nbsp;= LIKE %$value%  (contains)<br>";
echo "&nbsp;'k'&nbsp;= NOT LIKE %$value% (does not contain)<br>";
echo "&nbsp;'l'&nbsp;= &lt; value (less than)<br>";
echo "&nbsp;'b'&nbsp;= &lt; value (before, only for dates)<br>";
echo "&nbsp;'g'&nbsp;= &gt; value  (greater than)<br>";
echo "&nbsp;'a'&nbsp;= &gt; value  (after, only for dates)<br>";
echo "&nbsp;'m'&nbsp;= &lt;= value  (less or equal)<br>";
echo "&nbsp;'h'&nbsp;= &gt;= value  (greater or equal)<br>";
echo "&nbsp;'bw'&nbsp;= BETWEEN value1 and value2  (between two dates)<br>";
echo "&nbsp;There is special support for empty fields and for the Birthday field in Contacts<br><br><br>";

$queryGenerator = new QueryGenerator($moduleName, $current_user);
$queryGenerator->setFields(array('id','cf_681','accountname'));
$queryGenerator->addCondition('accountname','EDFG','c');
$query = $queryGenerator->getQuery();
testquery($query);
echo "$query<br><b>**INCORRECT:**&nbsp;</b>";
$queryGenerator = new QueryGenerator($moduleName, $current_user);
$queryGenerator->setFields(array('id','cf_681','accountname'));
$queryGenerator->addCondition('accountname','EDFG','c');
$queryGenerator->addCondition('employees','4','g','or');
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);
$queryGenerator = new QueryGenerator($moduleName, $current_user);
$queryGenerator->setFields(array('id','cf_681','accountname'));
$queryGenerator->startGroup();  // parenthesis to enclose our OR condition between the two groups
$queryGenerator->startGroup();  // start first group
$queryGenerator->addCondition('accountname','EDFG','c');
$queryGenerator->addCondition('employees','4','g','or');
$queryGenerator->endGroup();  // end first group
$queryGenerator->startGroup('or');  // start second group joining with OR glue
$queryGenerator->addCondition('accountname','3m','c');
$queryGenerator->addCondition('employees','4','l','or');
$queryGenerator->endGroup();  // end second groupd
$queryGenerator->endGroup();  // end enclosing parenthesis
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);

echo "<h2>Query with related module condition</h2>";
$queryGenerator = new QueryGenerator('Contacts', $current_user);
$queryGenerator->setFields(array('id','cf_681','firstname'));
$queryGenerator->addReferenceModuleFieldCondition('Accounts', 'account_id', 'accountname', 'EDFG', 'c');
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);

echo "<h2>Calendar Queries</h2>";
$queryGenerator = new QueryGenerator('Calendar', $current_user);
$queryGenerator->setFields(array('id','subject','activitytype','date_start','due_date','taskstatus'));
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);

$queryGenerator = new QueryGenerator('Events', $current_user);
$queryGenerator->setFields(array('id','subject','activitytype'));
$queryGenerator->addReferenceModuleFieldCondition('Contacts', 'contact_id', 'firstname', 'Mary', 'c');
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);

$queryGenerator = new QueryGenerator('Emails', $current_user);
$queryGenerator->setFields(array('id','subject','activitytype'));
$queryGenerator->addReferenceModuleFieldCondition('Accounts', 'parent_id', 'accountname', 'EDFG', 'c');
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);

$queryGenerator = new QueryGenerator('Emails', $current_user);
$queryGenerator->setFields(array('id','subject','activitytype','from_email'));
$queryGenerator->addReferenceModuleFieldCondition('Accounts', 'parent_id', 'accountname', 'EDFG', 'c');
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);

echo "<h2>User Queries</h2>";
$queryGenerator = new QueryGenerator('Users', $current_user);
$queryGenerator->setFields(array('id','username','first_name'));
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);

echo "<h2>Custom Module Queries</h2>";
$queryGenerator = new QueryGenerator('CobroPago', $current_user);
$queryGenerator->setFields(array('id','assigned_user_id', 'first_name'));
$queryGenerator->addReferenceModuleFieldCondition('Users', 'reports_to_id', 'first_name', 'min', 'c');
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);

$queryGenerator = new QueryGenerator('CobroPago', $current_user);
$queryGenerator->setFields(array('id','assigned_user_id', 'accountname'));
$queryGenerator->addReferenceModuleFieldCondition('Accounts', 'parent_id', 'account_no', '', 'n');
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);

echo "<h2>Document Module Queries</h2>";
$queryGenerator = new QueryGenerator('Documents', $current_user);
$queryGenerator->setFields(array('id','assigned_user_id', 'notes_title','filename'));
$queryGenerator->addCondition('filename','app','s');
$query = $queryGenerator->getQuery();
echo "$query<br>";
testquery($query);

// echo "<h2>Query with custom field</h2>";
// $queryGenerator = new QueryGenerator($moduleName, $current_user);
// $queryGenerator->setFields(array('id','cf_681'));
// $query = $queryGenerator->getQuery();
// echo "$query<br>";
// testquery($query);


?>