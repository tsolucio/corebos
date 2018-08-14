<?php
/* The password reset form, the link to this page is included
   from the forgot.php email message
*/
   session_start();
   require_once('modules/Users/Users.php');

   $user_name = $_SESSION['user_name1'];

   //Get lastAskedReset value to check if 30 min have passed since password reset asking
   $check_timestamp = $adb->query("select lastAskedReset from vtiger_users where user_name=('".$user_name."')");
   $lastAskedReset=$adb->query_result($check_timestamp,0,0);
   $time=time();

   if (($time-$lastAskedReset)>1800){
    $_SESSION['reset_password_status'] = "Password link has expired. Please ask for new password again!";
    header("location: password_reset_status.php");
  }

  //Password validation
  else if ( $_POST['new_password'] == $_POST['confirm_password'] ) 
  {
    if (strlen($_POST["new_password"]) <=8) {
      //Password length validation failed
      $_SESSION['password_not_match'] = "Your Password Must Contain At Least 8 Characters!";
      header("location: resetPassword.php");
    }
    else 
    {//Change User Password
      $password= $_POST['new_password'];
      $instance = new Users();
      $var = $instance->resetPassword($password,$user_name);

      //Password successfully reset
      $_SESSION['reset_password_status'] = "Password has been successfully reset, You can go now login with the new password  ";
      header("location: password_reset_status.php");
    }

  }
  else
  { //Password validation failed
    $_SESSION['password_not_match'] = "Passwords don't match, Please try again";
    header("location: resetPassword.php");
  }
  ?>