<?php
/* Displays all error/success messages */
session_start();
?>
<link href='https://fonts.googleapis.com/css?family=Anton|Passion+One|PT+Sans+Caption' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

<style type="text/css">

*   
html,body{
     background-image: linear-gradient(-225deg, #FFFEFF 0%, #D7FFFE 100%);
     height:100%;
     min-height:100%;
     max-height:100%;
}
{
  font-family: 'PT Sans Caption', sans-serif, 'arial', 'Times New Roman';
}
.error h2
{
    color: #A2A2A2;
    font-weight: bold;
    padding-bottom: 20px;
}

.center {
    margin: auto;
    width: 100%;
    padding: 100px;
}

</style>
</head>

<body>

    <!-- Error Page -->
    <div class="error">
        <div class="container-floud">
            <div class="col-xs-12 ground-color text-center">
                <div class="container-error-404">
                    <div class="clip"><div class="shadow"><span class="digit thirdDigit"></span></div></div>
                    <div class="clip"><div class="shadow"><span class="digit secondDigit"></span></div></div>
                    <div class="clip"><div class="shadow"><span class="digit firstDigit"></span></div></div>

                </div>
                <div class="center">
                  <br/><br/><br/>
                    <h2 class="h1">
                      <?php 
                          if( isset($_SESSION['reset_password_status']) AND !empty($_SESSION['reset_password_status']) ): 
                            echo $_SESSION['reset_password_status'];    
                          else:
                            header( "location: index.php" );
                          endif;
                       ?>
                     </h2>
                    <a href="index.php"> <button  class="btn btn-primary center-block">
                       Go to Login Page
                   </button></a>
               </div>

           </div>
       </div>
       <div class="form-group">
        <label class="col-md-4 control-label" for="singlebutton"></label>
    </div>
</body>