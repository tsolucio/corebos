/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
var sweetAlert = document.createElement('script');  
sweetAlert.setAttribute('src','https://unpkg.com/sweetalert/dist/sweetalert.min.js');
document.head.appendChild(sweetAlert);


function showMapWindow(mapid) {
    var url = 'index.php?module=cbMap&action=cbMapAjax&file=generateMap&mapid='+mapid;
    window.open(url, 'Create Mapping', 'width=940,height=800,resizable=1,scrollbars=1');
}

function validateMap(mapid) {
    var url = "index.php?module=cbMap&action=cbMapAjax&file=validateMap";
    var stringData = "mapid=" + mapid;
    jQuery.ajax({
        type: "POST",
        url: url,
        data: stringData,
        cache: false,
        async: false,
        success: function(text) {
            if (text=='VALIDATION_NOT_IMPLEMENTED_YET'){
                swal("Notice!", "The validation for this map type hasn't been implemented yet.", "warning");
            }

            else if (text){
                cleanText = text.replace(/<\/?[^>]+(>|$)/g, "");        
                cleanText_final= cleanText.replace("Error: The document has no document element. on line -1", " ");
                swal({
                        title: "Oops!",
                        text: "XML is NOT valid, please fix it",
                        icon: "warning",
                        dangerMode: true,
                        buttons: {
                            catch: {
                                text: "Show Error",
                                value: "catch",
                            },
                            Ok: {
                                text: "Ok",
                            },
                        },
                    })
                    .then((value) => {
                        switch (value) {
                            case "catch":
                                swal("Error Log", cleanText_final, "error");
                                break;
                        }
                    });

            } else {
                swal("Good job!", "XML is valid", "success");
            }
        },

    });
}
