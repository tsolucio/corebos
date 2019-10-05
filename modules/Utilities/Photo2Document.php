<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
global $adb,$current_user,$singlepane_view, $app_strings, $theme, $default_charset;
$formodule = vtlib_purify($_REQUEST['formodule']);
$forrecord = vtlib_purify($_REQUEST['forrecord']);
$wsuserid = vtws_getEntityId('Users').'x'.$current_user->id;
$wsfolderid = vtws_getEntityId('DocumentFolders').'x';
$wsrecid = vtws_getEntityId($formodule).'x'.$forrecord;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $default_charset; ?>">
	<title><?php echo $app_strings['Photo2Document']; ?></title>
	<style type="text/css">@import url("themes/<?php echo $theme; ?>/style.css");</style>
	<script type="text/javascript" src="include/jquery/jquery.js"></script>
	<script src="include/Webservices/WSClient.js" type="text/javascript" charset="utf-8"></script>
<style>
<!--
.videobox {
	display: flex;
	flex-direction: row;
	width:100%;
}
.buttonbox {
	display: flex;
	flex-direction: column;
	width:18%;
	align-self: flex-end;
}
.buttonbox button {
	margin: 6px;
}
.fieldrow {
	display: flex;
	flex-direction: row;
	width:100%;
	padding; 10px;
}
.fieldlabel {
	padding-top:10px;
	width:180px;
}
.fieldinput {
	padding-top:10px;
}
.fieldinput input, .fieldinput select, .fieldinput textarea {
	width:280px;
}
.header {
	display: block;
	font-size: 1.5em;
	font-weight: bold;
	background-color: #e2e5ff;
	width:98%;
	padding: 8px;
	border-radius: 3px;
}
-->
</style>
</head>
<body style="background-color: whitesmoke">
<div class="header"><?php echo $app_strings['Photo2Document']; ?></div>
<div class="videobox">
	<div style="width:90%">
		<video id="video" width="640" height="480" autoplay></video>
		<canvas id="canvas" width="640" height="480" style="display:none"></canvas>
	</div>
	<div class="buttonbox">
		<button id="snap"><?php echo $app_strings['Snap Photo']; ?></button>
		<button id="clearp"><?php echo $app_strings['Clear Photo']; ?></button>
	</div>
</div>
<div class="fieldrow">
	<span class="fieldlabel"><?php echo getTranslatedString('Subject', 'Documents'); ?></span>
	<span class="fieldinput"><input type="text" id="docname" name="docname"></span>
</div>
<div class="fieldrow">
	<span class="fieldlabel"><?php echo getTranslatedString('LBL_FILE_NAME', 'Documents'); ?></span>
	<span class="fieldinput"><input type="text" id="filename" name="filename" value="Photo2Document.png"></span>
</div>
<div class="fieldrow">
	<span class="fieldlabel"><?php echo getTranslatedString('LBL_FOLDER_NAME', 'Documents'); ?></span>
	<span class="fieldinput"><select id="docfolder" name="docfolder">
<?php
$sql='select foldername,folderid from vtiger_attachmentsfolder order by foldername';
$res=$adb->pquery($sql, array());
for ($i=0; $i<$adb->num_rows($res); $i++) {
	echo '<option value="'.$adb->query_result($res, $i, 'folderid').'">'.$adb->query_result($res, $i, 'foldername').'</option>';
}
?>
	</select>
	</span>
</div>
<div class="fieldrow">
	<span class="fieldlabel"><?php echo getTranslatedString('LBL_DESCRIPTION', 'Documents'); ?></span>
	<span class="fieldinput"><textarea id="docdesc" name="docdesc" row=3></textarea></span>
</div>
<div align="center" style="padding:10px;">
	<input title="<?php echo $app_strings['LBL_SAVE_BUTTON_TITLE']; ?>" accessKey="<?php echo $app_strings['LBL_SAVE_BUTTON_KEY']; ?>" class="crmbutton small save" onclick="createdoc();" type="button" name="button" value="  <?php echo $app_strings['LBL_SAVE_BUTTON_LABEL']; ?>  ">
	<input title="<?php echo $app_strings['LBL_CLOSE']; ?>" accessKey="<?php echo $app_strings['LBL_CANCEL_BUTTON_KEY']; ?>" class="crmbutton small cancel" onclick="window.close();" type="button" name="button" value="  <?php echo $app_strings['LBL_CLOSE']; ?>  ">
</div>
<script type="text/javascript">
function convertCanvasToImage(canvas) {
	var image = new Image();
	image.src = canvas.toDataURL("image/png");
	return image;
}
function createdoc() {
	var cbUserID = '<?php echo $wsuserid; ?>';
	var cbFolderID = '<?php echo $wsfolderid; ?>';
	var cbconn = new Vtiger_WSClient('');
	cbconn.extendSession(function(result){
		var fname = document.getElementById('filename').value;
		if (fname.substr(fname.length - 4) != '.png') {
			fname = fname + '.png';
		}
		var model_filename={
			'name' : fname,
			'size' : 0,
			'type' : "image/png",
			'content' : document.getElementById('canvas').toDataURL("image/png")
		};
		var module = 'Documents';
		var valuesmap = {
			'assigned_user_id' : cbUserID,
			'notes_title' : document.getElementById('docname').value,
			'notecontent' : document.getElementById('docdesc').value,
			'filename' : model_filename,
			'filetype' : "image/png",
			'filesize' : 0,
			'filelocationtype' : 'I',
			'filedownloadcount' : 0,
			'filestatus' : 1,
			'folderid'  : cbFolderID+document.getElementById('docfolder').value,
			'relations' : '<?php echo $wsrecid; ?>'
		};
		cbconn.doCreate(module, valuesmap, afterCreateRecord);
	});
}
function afterCreateRecord(result, args) {
	if(result) {
		alert('<?php echo $app_strings['DocumentCreatedRelated']; ?>!!');
	} else {
		alert('<?php echo $app_strings['ERROR'].' '.$app_strings['LBL_CREATING']; ?>!!');
	}
}

if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
	// Not adding `{ audio: true }` since we only want video now
	navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
		video.src = window.URL.createObjectURL(stream);
		video.play();
	});
}
// Elements for taking the snapshot
var canvas = document.getElementById('canvas');
var context = canvas.getContext('2d');
var video = document.getElementById('video');

// Trigger photo take
document.getElementById("snap").addEventListener("click", function() {
	context.drawImage(video, 0, 0, 640, 480);
	video.style.display = 'none';
	canvas.style.display = 'block';
});
// clear photo
document.getElementById("clearp").addEventListener("click", function() {
	video.style.display = 'block';
	canvas.style.display = 'none';
});
</script>
</body>
</html>