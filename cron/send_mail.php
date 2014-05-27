<?php
require 'modules/Emails/PHPMailerAutoload.php';
require_once 'include/utils/CommonUtils.php';

function sendmail($to,$from,$subject,$contents,$mail_server,$mail_server_username,$mail_server_password,$filename,$smtp_auth='')
{
  $mail = new PHPMailer();
  $mail->Subject = $subject;
	$mail->Body    = $contents;//"This is the HTML message body <b>in bold!</b>";

	$initialfrom = $from;

	$mail->IsSMTP();                                      // set mailer to use SMTP
	//$mail->Host = "smtp1.example.com;smtp2.example.com";  // specify main and backup server
	$mail->Host = $mail_server;  // specify main and backup server
	if($smtp_auth == 'true')
		$mail->SMTPAuth = true;
	else
		$mail->SMTPAuth = false;
	$mail->Username = $mail_server_username ;//$smtp_username;  // SMTP username
	$mail->Password = $mail_server_password ;//$smtp_password; // SMTP password
	$mail->From = $from;
	$mail->FromName = $initialfrom;
	$mail->AddAddress($to);                  // name is optional
	$mail->AddReplyTo($from);
	$mail->WordWrap = 50;                                 // set word wrap to 50 characters
	$mail->IsHTML(true);                                  // set email format to HTML
	
	$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
	$mail->Sender= getReturnPath($mail->Host, $mail->From);
	if(!$mail->Send()) 
	{
	   echo "Message could not be sent. <p>";
	   echo "Mailer Error: " . $mail->ErrorInfo;
	   exit;
	}

}
?>
