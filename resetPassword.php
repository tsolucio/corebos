<?php
/* The password reset form, the link to this page is included
   from the forgotPassword.php email message
*/
   session_start();
   require_once('modules/Users/Users.php');

   if (!isset($_SESSION['password_not_match']))
   {
    if (isset($_REQUEST['user_name']) && isset($_REQUEST['hash'])){
      $user_name=vtlib_purify($_REQUEST['user_name']);
      $hash=vtlib_purify($_REQUEST['hash']);

      //Save username to pass it as an argument in resetPassword(Users.php) function
      $_SESSION['user_name1'] = $user_name;

      //Check if user_name and hash given belong to a user
      $result = $adb->pquery("SELECT email1,user_hash
        FROM vtiger_users WHERE user_name=('".$user_name."') and user_hash=('".$hash."')");

      //Username and hash don't belong to a user
      if ($adb->num_rows($result) === 0) {
        $_SESSION['reset_password_status'] = "You have entered invalid URL for password reset!";
        header("location: password_reset_status.php");
      }
    }
    else 
    {
      $_SESSION['reset_password_status'] = "Sorry, verification failed, try again!";
      header("location: password_reset_status.php");  
    }
  }
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <title></title><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
    html,body{
     background-image: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
     height:100%;
     min-height:100%;
     max-height:100%;
   }
 </style>
</head>
<body>
</br></br></br></br></br></br>
<div class="container bootstrap snippet">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-2">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">
            <span class="glyphicon glyphicon-th"></span>
            Reset password   
          </h3>
        </div>
        <form action="validatePassword.php" method="post">
          <div class="panel-body" align="center">
            <div class="row">
              <div class="col-xs-6 col-sm-6 col-md-6 separator social-login-box"> <br>
               <img alt="" class="img-thumbnail" src="include/images/user-avatar.png">                        
             </div>
             <div style="margin-top:50px;" class="col-xs-6 col-sm-6 col-md-6 login-box">

              <?php 
              if( isset($_SESSION['password_not_match']) AND !empty($_SESSION['password_not_match']) ){
                echo '<div class="alert alert-warning">';
                echo $_SESSION['password_not_match'];
                unset($_SESSION['password_not_match']);
                echo '</div>';}
              else{
                echo '<div class="alert alert-info" role="alert">';
                echo 'Type the new password in both fields';
                echo '</div>';
                  }
                ?>

                <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
                    <input class="form-control" name="new_password" type="password" placeholder="New Password">
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
                    <input class="form-control" name="confirm_password" type="password" placeholder="Confirm Password">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="panel-footer">
            <div class="row">
              <div class="col-xs-6 col-sm-6 col-md-6"></div>
              <div class="col-xs-6 col-sm-6 col-md-6">
                <button class="btn icon-btn-save btn-success" type="submit">
                  <span class="btn-save-label"><i class="glyphicon glyphicon-floppy-disk"></i></span> &nbsp Save</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

