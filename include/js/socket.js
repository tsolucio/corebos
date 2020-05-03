var host = getObj('sockethost').value;
var socket = io.connect(host);
var recordid = getObj('record').value+'##'+gVTUserID;
if(getObj('from_link')!=undefined && getObj('from_link').value == 'DetailView'){
var editdetail = 0;
} else {
    editdetail = 1;
}

socket.emit('change', { data: recordid,editdetail:editdetail});
socket.on("block", function (data) {
if(data=='1'){
    getObj("Socketblockit").style.display='block';
    getObj("socketblock").value='1';

} else {
    getObj("Socketblockit").style.display='none';
    getObj("socketblock").value='0';
}
});

