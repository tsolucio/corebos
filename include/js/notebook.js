/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
/**
 * this file contains all the utility functions for notebook
 */

/**
 * this function saves the contents of the notebook and restores the div once the control is moved out of the textarea
 * @param object node - the textarea div
 */
function saveContents(node, notebookid) {
	var contents = node.value;
	jQuery.ajax({
			method: 'POST',
			url: 'index.php?module=Home&action=HomeAjax&file=SaveNotebookContents&contents='+encodeURIComponent(contents)+'&notebookid='+notebookid
	}).done(function (response) {
				var responsedata = trim(response);
				var responsearray = JSON.parse(responsedata);
				if(responsearray['status'] == false){
					alert("Some error has occurred during save");
				}else{
					//success
					node.style.display = 'none';
					
					temp = document.getElementById('notebook_contents_'+notebookid);
					temp.style.display = 'block';
					temp.innerHTML = '<pre>' + responsearray['contents'] + '</pre>';
					document.getElementById('notebook_'+notebookid).style.display = 'block';
					
					var notebook_dbl_click_message = document.getElementById('notebook_dbl_click_message');
					var notebook_save_message = document.getElementById('notebook_save_message');
					notebook_dbl_click_message.style.display = 'block';
					notebook_save_message.style.display = 'none';
				}
			}
	);
}
/**
 * this function changes the div of the notebook to a textarea when double-clicked
 * @param object node - the notebook div
 */
function editContents(node, notebookid) {
	var notebook = document.getElementById('notebook_textarea_'+notebookid);
	var contents = document.getElementById('notebook_contents_'+notebookid);
	var notebook_dbl_click_message = document.getElementById('notebook_dbl_click_message');
	var notebook_save_message = document.getElementById('notebook_save_message');
	
	notebook.value = contents.getElementsByTagName('pre')[0].innerHTML;
	node.style.display = 'none';
	notebook.style.display = 'block';
	notebook_dbl_click_message.style.display = 'none';
	notebook_save_message.style.display = 'block';
	
	notebook.focus();
}