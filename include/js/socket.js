var host = getObj('sockethost').value;
var socket = io.connect(host);
var recordid = getObj('record').value+'##'+gVTUserID;
if(getObj('from_link')!=undefined){
var editdetail = getObj('from_link').value;
} else {
    editdetail = 'EditView';
}
if(editdetail == "DetailView"){
    var editdetail = 0;
} else {
    editdetail = 1;
}
socket.emit('change', { data: recordid,editdetail:editdetail});
socket.on("block", function (data) {
if(data=='1'){
    getObj("Socketblockit").style.display='block';

} else {
    getObj("Socketblockit").style.display='none';
}
});

