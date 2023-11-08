<?php
require_once('class.phpmailer.php');
/*
define('GUSER', 'webmaster@dil.in'); // GMail username
define('GPWD', 'Web@m@$ter@342'); // GMail password
*/
define('GUSER', 'noreply@datagroup.in'); // GMail username
define('GPWD', 'N0reply@123*'); // GMail password

function smtpmailer($to, $from,$from_name,$sub, $msg,$mail_type='',$attach_file='') { 

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
	$mail->Subject = $sub;
	$mail->Body = $msg;
	$mail->CharSet  = 'UTF-8';
	$mail->Encoding = 'base64';
        
   
	if($mail_type=='user')
	{ 
		  
		
	}
	if($attach_file!='')
	{ 
		$mail->AddAttachment($attach_file);		
	} 
	if(is_array($to))
	{
		foreach($to as $email => $name)
		{
			//echo $email; die;
		   $mail->AddAddress($email, $name);
		}
	}
	else
	{
		$mail->AddAddress($to);
	}
	if(!$mail->Send()) {
		//return 0; 
		$error = 'Mail error: '.$mail->ErrorInfo; 
		return $error;
		//return false;
	} else {
		// $error = 'Message sent!';
		//return true;
		return 1;
	}  
	
	 
}
  
?>