<?php

require_once('class.phpmailer.php');
/*
define('GUSER', 'webmaster@dil.in'); // GMail username
define('GPWD', 'Web@m@$ter@342'); // GMail password
*/
define('GUSER', 'aryagayathri317@gmail.com'); // GMail username
define('GPWD', 'jvmbgcgqatxpxddz'); // GMail APP password   lfjderrmwrliarke //password is corrects

//follw this link to generate passeod https://support.google.com/mail/answer/185833?hl=en
header('Content-Type: text/html; charset=utf-8');
function smtpmailer($to, $from,$from_name,$sub, $msg,$attach_file='') { 

 

	global $error;
	$mail = new PHPMailer();  // create a new object
	$mail->IsSMTP(); // enable SMTP
    $mail->IsHTML();
	$mail->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true;  // authentication enabled
	$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465; 
	$mail->Username = GUSER;  
	$mail->Password = GPWD;           
	$mail->SetFrom($from,$from_name);
	$mail->Subject = $sub;
	$mail->Body = $msg;	
	$mail->CharSet  = 'UTF-8';
	$mail->Encoding = 'base64';
        
    //$mail->AddReplyTo("","Sales Ashoka Oils");
    //$mail->AddCC("manvendra.s@bharatsync.com");
	if($attach_file!='')
	{ 
		$mail->AddAttachment($attach_file);		
	} 
	$mail->AddAddress($to);
	 
	if(!$mail->Send()) {		 
		return 0;  
		//return false;
	} else {
		// $error = 'Message sent!';
		//return true;
		return 1;
	}  
	
	 
}
 


 $email ='arya2609007@gmail.com';
$from ='aryagayathri317@gmail.com';
$from_name = "PEMS Engineering Consultants";// email name
$subject   = 'No replay,  Permission Approved';
             
              echo smtpmailer($email, $from,$from_name,$subject, "test"); 

 
?>