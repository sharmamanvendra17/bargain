<?php 
require_once('class.phpmailer.php');
/*
define('GUSER', 'webmaster@dil.in'); // GMail username
define('GPWD', 'Web@m@$ter@342'); // GMail password
*/
	define('GUSER', 'noreply@datagroup.in'); // GMail username
	define('GPWD', 'N0reply@123*'); // GMail password
	header('Content-Type: text/html; charset=utf-8');
	$from = "noreply@datagroup.in"; 

	global $error;
	$mail = new PHPMailer();  // create a new object
	$mail->IsSMTP(); // enable SMTP
    $mail->IsHTML();
	$mail->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true;  // authentication enabled
	$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
	$mail->Host = 'smtps.xgen.in';
	$mail->Port = 465; 
	$mail->Username = GUSER;  
	$mail->Password = GPWD;           
	$mail->SetFrom($from,$from_name);
	$mail->Subject = $subject;
	$mail->Body = $mail_mesage;	
	$mail->CharSet  = 'UTF-8';
	$mail->Encoding = 'base64';
        
    //$mail->AddReplyTo("","Sales Ashoka Oils");
    //$mail->AddCC("manvendra.s@bharatsync.com");
	if($attach_file!='')
	{ 
		$mail->AddAttachment($attach_file);		
	} 
	$mail->AddAddress($email);
	 
	if(!$mail->Send()) {	 
		//return false;
	} else {
		// $error = 'Message sent!';
		//return true; 
	}  
?>