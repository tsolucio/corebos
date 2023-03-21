<pre>
	show here list of wfs exec
	if record != 0 we filter on the given record id
	we have to add a "delete all logs of this record" action
	we have to add a "delete all logs" action
	use tuigrid to show a tree view of
	pid, Date, WFname as link, type, conditions, evaluation (green if positive, red if negative), recordID, button to show record values
	  > task, type, condition, evaluation (green if positive, red if negative), queued, haserror (green/red), button to show task log
	permit filtering on all columns
	"show record values" is a modal with the values
	"show task log" is just a modal with a row view of the messages
</pre>
<?php
$wflogs = $adb->pquery('select * from cbwflog where parentid=0 order by exectime', []);
echo '<table class="slds-table_bordered">';
echo '<tr>';
echo '<th>pid</th><th>date</th><th>wf</th><th>type</th><th>conditions</th><th>eval</th><th>recid</th><th>action</th>';
echo '</tr>';
while ($wflog = $adb->fetch_array($wflogs)) {
	echo '<tr>';
	echo '<td>'.$wflog['pid'].'</td><td>'.$wflog['exectime'].'</td><td>'.$wflog['wftkid'].$wflog['name'].'</td><td>'.$wflog['wftype'].'</td><td>'.$wflog['conditions'].'</td><td>'.$wflog['evaluation'].'</td><td>'.$wflog['recid'].'</td><td>action</td>';
	echo '</tr>';
	$tklogs = $adb->pquery('select * from cbwflog where parentid=? order by exectime', [$wflog['cbwflogid']]);
	if ($adb->num_rows($tklogs)) {
		echo '<tr>';
		echo '<td>&gt;</td><td colspan="6"><table class="slds-table_bordered">';
		echo '<tr>';
		echo '<th>task</th><th>type</th><th>conditions</th><th>eval</th>';
		echo '</tr>';
		echo '</table>';
		echo '</td><td></td></tr>';
		while ($tklog = $adb->fetch_array($tklogs)) {
			echo '<tr><td></td>';
			echo '<td colspan="6"><table class="slds-table_bordered"><tr>';
			echo '<td>'.$tklog['wftkid'].$tklog['name'].'</td><td>'.$tklog['wftype'].'</td><td>'.$tklog['conditions'].'</td><td>'.$tklog['evaluation'].'</td>';
			echo '</tr></table></td><td>action</td></tr>';
		}
	}
}
echo '<table>';
