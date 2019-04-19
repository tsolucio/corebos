let fileurl = 'module=com_vtiger_workflow&action=com_vtiger_workflowAjax&file=getrelatedmods&currentmodule='+moduleName;
$(document).ready(function () {
  jQuery.ajax({
    method: 'GET',
    url: 'index.php?' + fileurl
  }).done(function (modlistres) {
  document.getElementById('relModlist_type').innerHTML =modlistres;
  });
});
function changeIdlistVal(recval) {
  var idinputval = document.getElementById('idlist').value;
  if (document.getElementById('checkbox-'+recval).checked) {
    document.getElementById('idlist').value = (idinputval == '') ? recval : idinputval+','+recval;
  } else {
      var idlistvals=idinputval.split(',');
      for (var i = 0; i < idlistvals.length; i++) {
      if (idlistvals[i] == recval) {
        idlistvals.splice(i, 1);
        document.getElementById('idlist').value = idlistvals;
      }
    }
   }
}

function addrecList(recId, recvalue) {
  var reclist ='<tr id="row-'+recId+'" aria-level="1" aria-posinset="1" aria-selected="false" aria-setsize="4" class="slds-hint-parent" tabindex="0">'+
      '<td class="slds-text-align_right" role="gridcell" style="width: 3.25rem;">'+
      '<div class="slds-checkbox">'+
          '<input type="checkbox" onclick="changeIdlistVal('+recId+')" name="options[]" value='+recId+' id="checkbox-'+recId+'" aria-labelledby="check-button-label-04 column-group-header" value="checkbox-04" checked />'+
          '<label class="slds-checkbox__label" for="checkbox-'+recId+'" id="check-button-label-04">'+
          '<span class="slds-checkbox_faux"></span>'+
          '<span class="slds-form-element__label slds-assistive-text">Select item 4</span>'+
          '</label>'+
        '</div>'+
      '</td>'+
      '<th class="slds-tree__item" data-label="Entity Name" scope="row">'+
        '<button class="slds-button slds-button_icon slds-button_icon-x-small slds-m-right_x-small slds-is-disabled" aria-hidden="true" tabindex="-1" title="Expand Rewis Inc">'+
          '<svg class="slds-button__icon slds-button__icon_small" aria-hidden="true">'+
              '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright" />'+
          '</svg>'+
          '<span class="slds-assistive-text">'+recvalue+'</span>'+
        '</button>'+
        '<div class="slds-truncate" title="Rewis Inc"><a href="javascript:void(0);" tabindex="-1">'+recvalue+'</a></div>'+
      '</th>'+
      '<td data-label="Entity" role="gridcell">'+
        '<div class="slds-truncate" title='+document.getElementById('relModlist_type').value+'>'+document.getElementById('relModlist_type').value+'</div>'+
      '</td>';
  return reclist;
}
jQuery(document).ready(function () {
  var recsavedDiv='';
	if (relrecords.length > 0) {
		for (var i=0; i<relrecords.length; i++) {
      var reclist ='<tr id="row-'+relrecords[i].recid+'" aria-level="1" aria-posinset="1" aria-selected="false" aria-setsize="4" class="slds-hint-parent" tabindex="0">'+
            '<td class="slds-text-align_right" role="gridcell" style="width: 3.25rem;">'+
            '<div class="slds-checkbox">'+
                '<input type="checkbox" onclick="changeIdlistVal('+relrecords[i].recid+')" name="options[]" value='+relrecords[i].recid+' id="checkbox-'+relrecords[i].recid+'" aria-labelledby="check-button-label-04 column-group-header" value="checkbox-04" checked />'+
                '<label class="slds-checkbox__label" for="checkbox-'+relrecords[i].recid+'" id="check-button-label-04">'+
                '<span class="slds-checkbox_faux"></span>'+
                '<span class="slds-form-element__label slds-assistive-text">Select item 4</span>'+
                '</label>'+
              '</div>'+
            '</td>'+
            '<th class="slds-tree__item" data-label="Entity Name" scope="row">'+
              '<button class="slds-button slds-button_icon slds-button_icon-x-small slds-m-right_x-small slds-is-disabled" aria-hidden="true" tabindex="-1" title="Expand Rewis Inc">'+
                '<svg class="slds-button__icon slds-button__icon_small" aria-hidden="true">'+
                    '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#chevronright" />'+
                '</svg>'+
                '<span class="slds-assistive-text">'+relrecords[i].entityName+'</span>'+
              '</button>'+
              '<div class="slds-truncate" title="'+relrecords[i].entityName+'"><a href="javascript:void(0);" tabindex="-1">'+relrecords[i].entityName+'</a></div>'+
            '</th>'+
            '<td data-label="Entity" role="gridcell">'+
              '<div class="slds-truncate" title='+relrecords[i].entityType+'>'+relrecords[i].entityType+'</div>'+
            '</td>';
      recsavedDiv += reclist;
    }
    document.getElementById('selected_recordsDiv').innerHTML = recsavedDiv;
  }
});