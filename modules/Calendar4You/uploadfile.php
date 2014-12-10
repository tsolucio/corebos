<?php
global $root_directory;
define('UPLOAD_DIR', $root_directory.'/modules/Calendar4You/googlekeys/');

if(isset($_POST['sub']))
{
    if(isset($_FILES['user_file']))
    {
        $file = $_FILES['user_file'];
        if($file['error'] == UPLOAD_ERR_OK and is_uploaded_file($file['tmp_name']))
        {
            move_uploaded_file($file['tmp_name'], UPLOAD_DIR.$file['name']);
            $msg='success';
        }
        else $msg=$file['error'];
    }
}
?>
<script type="text/javascript">
  window.location.href='index.php?module=Calendar4You&action=index';
</script>