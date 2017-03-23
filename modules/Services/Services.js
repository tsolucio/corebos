/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
document.write("<script type='text/javascript' src='modules/Products/multifile.js'></"+"script>");
document.write("<script type='text/javascript' src='include/js/Merge.js'></"+"script>");
function updateListPrice(unitprice,fieldname, oSelect)
{
	if(oSelect.checked == true)
	{
		document.getElementById(fieldname).style.visibility = 'visible';
		document.getElementById(fieldname).value = unitprice;
	}else
	{
		document.getElementById(fieldname).style.visibility = 'hidden';
	}
}

function check4null(form) {
  var isError = false;
  var errorMessage = "";
  if (trim(form.productname.value) =='') {
	isError = true;
	errorMessage += "\n Product Name";
	form.productname.focus();
  }

  if (isError == true) {
	alert(alert_arr.MISSING_REQUIRED_FIELDS + errorMessage);
	return false;
  }
  return true;
}

function set_return(product_id, product_name) {
	if(document.getElementById('from_link').value != '') {
		window.opener.document.QcEditView.parent_name.value = product_name;
		window.opener.document.QcEditView.parent_id.value = product_id;
	} else {
		window.opener.document.EditView.parent_name.value = product_name;
		window.opener.document.EditView.parent_id.value = product_id;
	}
}
function set_return_specific(product_id, product_name) {
	if(document.getElementById('from_link').value != '')
	{
		var fldName = window.opener.document.QcEditView.product_name;
		var fldId = window.opener.document.QcEditView.product_id;
	}else if(typeof(window.opener.document.DetailView) != 'undefined')
	{
	   var fldName = window.opener.document.DetailView.product_name;
	   var fldId = window.opener.document.DetailView.product_id;
	}else
	{
	   var fldName = window.opener.document.EditView.product_name;
	   var fldId = window.opener.document.EditView.product_id;
	}
	fldName.value = product_name;
	fldId.value = product_id;
}

function set_return_formname_specific(formname,product_id, product_name) {
	window.opener.document.EditView1.product_name.value = product_name;
	window.opener.document.EditView1.product_id.value = product_id;
}

function set_return_inventory(product_id,product_name,unitprice,taxstr,curr_row,desc) {
	window.opener.document.EditView.elements["productName"+curr_row].value = product_name;
	window.opener.document.EditView.elements["hdnProductId"+curr_row].value = product_id;
	window.opener.document.EditView.elements["listPrice"+curr_row].value = unitprice;
	window.opener.document.EditView.elements["comment"+curr_row].value = desc;

	// Apply decimal round-off to value
	if(!isNaN(parseFloat(unitprice))) unitprice = roundPriceValue(unitprice);
	window.opener.document.EditView.elements["listPrice"+curr_row].value = unitprice;
	
	var tax_array = new Array();
	var tax_details = new Array();
	tax_array = taxstr.split(',');
	for(var i=0;i<tax_array.length;i++)
	{
		tax_details = tax_array[i].split('=');
	}
	window.opener.document.EditView.elements["qty"+curr_row].value = service_default_units;
	window.opener.document.EditView.elements["qty"+curr_row].focus();
}

function set_return_inventory_po(product_id,product_name,unitprice,taxstr,curr_row,desc) {
	set_return_inventory(product_id,product_name,unitprice,taxstr,curr_row,desc);
}

function InventorySelectAllServices(mod,z,image_pth)
{
	if(document.selectall.selected_id != undefined) {
		var x = document.selectall.selected_id.length;
		var y=0;
		idstring = "";
		namestr = "";
		var action_str="";
		if ( x == undefined) {
			if (document.selectall.selected_id.checked) {
				idstring = document.selectall.selected_id.value;
				c = document.selectall.selected_id.value;
				var prod_array = JSON.parse(document.getElementById('popup_product_'+c).attributes['vt_prod_arr'].nodeValue);
				var prod_id = prod_array['entityid'];
				var prod_name = prod_array['prodname'];
				var unit_price = prod_array['unitprice'];
				var taxstring = prod_array['taxstring'];
				var desc = prod_array['desc'];
				var row_id = prod_array['rowid'];
				set_return_inventory(prod_id,prod_name,unit_price,taxstring,parseInt(row_id),desc);
				y=1;
			} else {
				alert(alert_arr.SELECT);
				return false;
			}
		} else {
			y=0;
			for(i = 0; i < x ; i++) {
				if(document.selectall.selected_id[i].checked) {
					idstring = document.selectall.selected_id[i].value+";"+idstring;
					c = document.selectall.selected_id[i].value;
					var prod_array = JSON.parse(document.getElementById('popup_product_'+c).attributes['vt_prod_arr'].nodeValue);
					var prod_id = prod_array['entityid'];
					var prod_name = prod_array['prodname'];
					var unit_price = prod_array['unitprice'];
					var taxstring = prod_array['taxstring'];
					var desc = prod_array['desc'];
					if(y>0) {
						var row_id = window.opener.fnAddServiceRow(mod,image_pth);
					} else {
						var row_id = prod_array['rowid'];
					}	
					set_return_inventory(prod_id,prod_name,unit_price,taxstring,parseInt(row_id),desc);
					y=y+1;
				}
			}
		}
		if (y != 0) {
			document.selectall.idlist.value=idstring;
			return true;
		} else {
			alert(alert_arr.SELECT);
			return false;
		}
	}
}

function set_return_product(product_id, product_name) {
	if(document.getElementById('from_link').value != '') {
		window.opener.document.QcEditView.parent_name.value = product_name;
		window.opener.document.QcEditView.parent_id.value = product_id;
	} else {
		window.opener.document.EditView.product_name.value = product_name;
		window.opener.document.EditView.product_id.value = product_id;
	}
}
function getImageListBody() {
	if (browser_ie) {
		var ImageListBody=getObj("ImageList");
	} else if (browser_nn4 || browser_nn6) {
		if (getObj("ImageList").childNodes.item(0).tagName=="TABLE") {
			var ImageListBody=getObj("ImageList");
		} else {
			var ImageListBody=getObj("ImageList");
		}
	}
	return ImageListBody;
}

// Function to Round off the Price Value
function roundPriceValue(val) {
   val = parseFloat(val);
   val = Math.round(val*100)/100;
   val = val.toString();
   if (val.indexOf(".")<0) {
      val+=".00";
   } else {
      var dec=val.substring(val.indexOf(".")+1,val.length);
      if (dec.length>2)
         val=val.substring(0,val.indexOf("."))+"."+dec.substring(0,2);
      else if (dec.length==1)
         val=val+"0";
   }
   return val;
}
// End

function fnAddServiceRow(module,image_path){
	rowCnt++;

	var tableName = document.getElementById('proTab');
	var prev = tableName.rows.length;
	var count = eval(prev)-1;//As the table has two headers, we should reduce the count
	var row = tableName.insertRow(prev);
	row.id = "row"+count;
	row.style.verticalAlign = "top";

	var colone = row.insertCell(0);
	var coltwo = row.insertCell(1);
	var colthree = row.insertCell(2);
	var colfour = row.insertCell(3);
	var colfive = row.insertCell(4);
	var colsix = row.insertCell(5);
	var colseven = row.insertCell(6);

	/* Product Re-Ordering Feature Code Addition Starts */
	iMax = tableName.rows.length;
	for(iCount=1;iCount<=iMax-3;iCount++)
	{
		if(document.getElementById("row"+iCount) && document.getElementById("row"+iCount).style.display != 'none')
		{
			iPrevRowIndex = iCount;
		}
	}
	iPrevCount = eval(iPrevRowIndex);
	var oPrevRow = tableName.rows[iPrevRowIndex+1]; 
	var delete_row_count=count;
	/* Product Re-Ordering Feature Code Addition ends */

	//Delete link
	colone.className = "crmTableRow small";
	colone.id = row.id+"_col1";
	colone.innerHTML='<img src="themes/softed/images/delete.gif" border="0" onclick="deleteRow(\''+module+'\','+count+',\''+image_path+'\')" style="cursor:pointer;" title="'+alert_arr.LBL_DELETE_EMAIL+'"><input id="deleted'+count+'" name="deleted'+count+'" type="hidden" value="0"><br/><br/>&nbsp;<a href="javascript:moveUpDown(\'UP\',\''+module+'\','+count+')" title="'+alert_arr.MoveUp+'"><img src="themes/images/up_layout.gif" border="0"></a>';
	/* Product Re-Ordering Feature Code Addition Starts */
	if(iPrevCount != 1)
	{
		oPrevRow.cells[0].innerHTML = '<img src="themes/softed/images/delete.gif" border="0" onclick="deleteRow(\''+module+'\','+iPrevCount+')" style="cursor:pointer;" title="'+alert_arr.LBL_DELETE_EMAIL+'"><input id="deleted'+iPrevCount+'" name="deleted'+iPrevCount+'" type="hidden" value="0"><br/><br/>&nbsp;<a href="javascript:moveUpDown(\'UP\',\''+module+'\','+iPrevCount+')" title="'+alert_arr.MoveUp+'"><img src="themes/images/up_layout.gif" border="0"></a>&nbsp;&nbsp;<a href="javascript:moveUpDown(\'DOWN\',\''+module+'\','+iPrevCount+')" title="'+alert_arr.MoveDown+'"><img src="themes/images/down_layout.gif" border="0"></a>';
	}
	else
	{
		oPrevRow.cells[0].innerHTML = '<input id="deleted'+iPrevCount+'" name="deleted'+iPrevCount+'" type="hidden" value="0"><br/><br/><a href="javascript:moveUpDown(\'DOWN\',\''+module+'\','+iPrevCount+')" title="'+alert_arr.MoveDown+'"><img src="themes/images/down_layout.gif" border="0"></a>';
	}
	/* Product Re-Ordering Feature Code Addition ends */
	
	//Product Name with Popup image to select product
	coltwo.className = "crmTableRow small";
	coltwo.innerHTML= '<table border="0" cellpadding="1" cellspacing="0" width="100%"><tr><td class="small"><input id="productName'+count+'" name="productName'+count+'" class="small" style="width: 70%;" value="" readonly="readonly" type="text" />'+
						'<input id="hdnProductId'+count+'" name="hdnProductId'+count+'" value="" type="hidden" /><input type="hidden" id="lineItemType'+count+'" name="lineItemType'+count+'" value="Services" />'+
						'&nbsp;<img id="searchIcon'+count+'" title="'+alert_arr.Services+'" src="themes/images/services.gif" style="cursor: pointer;" onclick="servicePickList(this,\''+module+'\','+count+')" align="absmiddle">'+
						'</td></tr><tr><td class="small"><input type="hidden" value="" id="subproduct_ids'+count+'" name="subproduct_ids'+count+'" /><span id="subprod_names'+count+'" name="subprod_names'+count+'" style="color:#C0C0C0;font-style:italic;"> </span>'+
						'</td></tr><tr><td class="small" id="setComment'+count+'"><textarea id="comment'+count+'" name="comment'+count+'" class=small style="width:70%;height:40px"></textarea><img src="themes/images/clear_field.gif" onClick="getObj(\'comment'+count+'\').value=\'\'"; style="cursor:pointer;" /></td></tr></tbody></table>';

	//Additional information column
	colthree.className = "crmTableRow small";
	cloneMoreInfoNode(count);

	//Quantity
	var temp='';
	colfour.className = "crmTableRow small";
	temp='<input id="qty'+count+'" name="qty'+count+'" type="text" class="small " style="width:50px" onBlur="settotalnoofrows(); calcTotal(); loadTaxes_Ajax('+count+');';
	temp+='" onChange="setDiscount(this,'+count+')" value=""/><br>';
	colfour.innerHTML=temp;
	//List Price with Discount, Total after Discount and Tax labels
	colfive.className = "crmTableRow small";
	colfive.innerHTML='<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="right"><input id="listPrice'+count+'" name="listPrice'+count+'" value="0.00" type="text" class="small" style="width:70px" onBlur="calcTotal();setDiscount(this,'+count+');callTaxCalc('+count+'); calcTotal();"'+(Inventory_ListPrice_ReadOnly==1 ? ' readonly ' : '')+'/>&nbsp;<img src="themes/images/pricebook.gif" onclick="priceBookPickList(this,'+count+')"></td></tr><tr><td align="right" style="padding:5px;" nowrap>		(-)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(this,\'discount_div'+count+'\',\'discount\','+count+')" >'+product_labelarr.DISCOUNT+'</a> : </b><div class=\"discountUI\" id=\"discount_div'+count+'"><input type="hidden" id="discount_type'+count+'" name="discount_type'+count+'" value=""><table width="100%" border="0" cellpadding="5" cellspacing="0" class="small"><tr><td id="discount_div_title'+count+'" nowrap align="left" ></td><td align="right"><img src="themes/images/close.gif" border="0" onClick="fnHidePopDiv(\'discount_div'+count+'\')" style="cursor:pointer;"></td></tr><tr><td align="left" class="lineOnTop"><input type="radio" name="discount'+count+'" checked onclick="setDiscount(this,'+count+'); callTaxCalc('+count+');calcTotal();">&nbsp; '+product_labelarr.ZERO_DISCOUNT+'</td><td class="lineOnTop">&nbsp;</td></tr><tr><td align="left"><input type="radio" name="discount'+count+'" onclick="setDiscount(this,'+count+'); callTaxCalc('+count+');calcTotal();">&nbsp; % '+product_labelarr.PERCENT_OF_PRICE+' </td><td align="right"><input type="text" class="small" size="2" id="discount_percentage'+count+'" name="discount_percentage'+count+'" value="0" style="visibility:hidden" onBlur="setDiscount(this,'+count+'); callTaxCalc('+count+');calcTotal();">&nbsp;%</td></tr><tr><td align="left" nowrap><input type="radio" name="discount'+count+'" onclick="setDiscount(this,'+count+'); callTaxCalc('+count+');calcTotal();">&nbsp; '+product_labelarr.DIRECT_PRICE_REDUCTION+'</td><td align="right"><input type="text" id="discount_amount'+count+'" name="discount_amount'+count+'" size="5" value="0" style="visibility:hidden" onBlur="setDiscount(this,'+count+'); callTaxCalc('+count+');calcTotal();"></td></tr></table></div></td></tr><tr> <td align="right" style="padding:5px;" nowrap><b>'+product_labelarr.TOTAL_AFTER_DISCOUNT+' :</b></td></tr><tr id="individual_tax_row'+count+'" class="TaxShow"><td align="right" style="padding:5px;" nowrap>(+)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(this,\'tax_div'+count+'\',\'tax\','+count+')" >'+product_labelarr.TAX+' </a> : </b><div class="discountUI" id="tax_div'+count+'"></div></td></tr></table> ';

	//Total and Discount, Total after Discount and Tax details
	colsix.className = "crmTableRow small";
	colsix.innerHTML = '<table width="100%" cellpadding="5" cellspacing="0"><tr><td id="productTotal'+count+'" align="right">&nbsp;</td></tr><tr><td id="discountTotal'+count+'" align="right">0.00</td></tr><tr><td id="totalAfterDiscount'+count+'" align="right">&nbsp;</td></tr><tr><td id="taxTotal'+count+'" align="right">0.00</td></tr></table>';

	//Net Price
	colseven.className = "crmTableRow small";
	colseven.align = "right";
	colseven.style.verticalAlign = "bottom";
	colseven.innerHTML = '<span id="netPrice'+count+'"><b>&nbsp;</b></span>';
	
	//This is to show or hide the individual or group tax
	decideTaxDiv();
	calcTotal();
	return count;
}

function servicePickList(currObj,module, row_no) {
	var trObj=currObj.parentNode.parentNode;
	var rowId = row_no;
	var currentRowId = parseInt(currObj.id.match(/([0-9]+)$/)[1]);

	// If we have mismatching rowId and currentRowId, it is due swapping of rows
	if(rowId != currentRowId) {
		rowId = currentRowId;
	}

	var currencyid = document.getElementById("inventory_currency").value;

	popuptype = 'inventory_service';
	var record_id = '';
	var additionalinfo = getInventoryModuleTaxRelatedInformation();
	if(record_id != '')
		window.open("index.php?module=Services&action=Popup&html=Popup_picker&select=enable&form=HelpDeskEditView&popuptype="+popuptype+"&curr_row="+rowId+"&relmod_id="+record_id+"&parent_module=Accounts&return_module="+module+"&currencyid="+currencyid+additionalinfo,"productWin","width=640,height=600,resizable=0,scrollbars=0,status=1,top=150,left=200");
	else
		window.open("index.php?module=Services&action=Popup&html=Popup_picker&select=enable&form=HelpDeskEditView&popuptype="+popuptype+"&curr_row="+rowId+"&return_module="+module+"&currencyid="+currencyid+additionalinfo,"productWin","width=640,height=600,resizable=0,scrollbars=0,status=1,top=150,left=200");
}
