$(document).ready(function () {
    $('#import').click(function(e) {
        e.preventDefault();
        var filename = $('#import_file')[0].files;
        var category = document.getElementById('category').value;
        var form_data = new FormData();
        if (filename[0] == undefined || category == '') {
            if (filename[0] == undefined) {
                window.alert('Import File cannot be empty');
            }
            if (category == '') {
                window.alert('Category cannot be empty');
            }
        } else {
            var file_extension = filename[0]['name'].split('.').pop();
            if (file_extension == 'xml') {
                form_data.append('filename', filename[0]);
                form_data.append('category', category);
                $.ajax({
                    url:"index.php?module=Settings&action=SettingsAjax&file=ImportXML",
                    type: "post",
                    data: form_data,
                    contentType: false,
                    processData: false,
                    success:function(response){
                        if (response.includes("imported!")) {
                            $('#message-sucess').empty();
                            document.getElementById('message-sucess').style.display='block';
                            $('#message-sucess').append('<td>'+response+'</td>');
                            setInterval(hide_success, 8000);
                            clearInterval(setInterval(hide_success, 8000));
                            function hide_success () {
                                document.getElementById('message-sucess').style.display='none';
                            }
                        } 
                        if (response.includes("already exists!")) {
                            $('#message-warning').empty();
                            document.getElementById('message-warning').style.display='block';
                            $('#message-warning').append('<td>'+response+'</td>');
                            setInterval(hide_warning, 8000);
                            clearInterval(setInterval(hide_warning, 8000));
                            function hide_warning () {
                                document.getElementById('message-warning').style.display='none';
                            }
                        }
                    }
                });
            } else {
                window.alert('Supported File Type .XML only');
            }
        }
    })
});