</table>
<br /><br />
<b style="color:#FF0000">Failed Queries Log</b>
<div id="failedLog" style="border:1px solid #666666;width:90%;position:relative;height:200px;overflow:auto;left:5%;top:10px;">
	<?php
		foreach($failure_query_array as $failed_query)
		      echo '<br><font color="red">'.$failed_query.'</font>';
	?>
</div>
<br /><br />
<table width="35%" border="0" cellpadding="5" cellspacing="0" align="center" class="small">
   <tr>
	<td width="75%" align="right" nowrap>
		Total Number of queries executed : 
	</td>
	<td width="25%" align="left">
		<b><?php echo $query_count;?> </b>
	</td>
   </tr>
   <tr>
	<td align="right">
		Queries Successed : 
	</td>
	<td align="left">
		<b style="color:#006600;">
		<?php echo $success_query_count;?>
		</b>
	</td>
   </tr>
   <tr>
	<td align="right">
		Queries Failed : 
	</td>
	<td align="left">
		<b style="color:#FF0000;">
		<?php echo $failure_query_count ;?>
			</b>
	</td>
   </tr>
</table>
</body>
</html>