<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
/**
 * This example shows settings to use when sending via Google's Gmail servers.
 */
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Europe/Amsterdam');

require_once("phpmailer/class.phpmailer.php");
require_once("phpmailer/class.smtp.php");

$body = "testmessage";
$from = 'sandervh@outlook.com';
$from_name = 'Sander';
$subject = 'Hufman email test';
$to = 'info@logo4life.nl';

//Create a new PHPMailer instance
	$mail = new PHPMailer();  // create a new object
    $mail->IsSMTP(); // enable SMTP
    $mail->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true;  // authentication enabled
    //$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = 'mail.logo4life.nl';
    $mail->Port = 587; 
    $mail->Username = 'info@logo4life.nl';  
    $mail->Password = 'kerstfeest2016!';           
    $mail->SetFrom($from, $from_name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);
    if(!$mail->Send()) {
        $error = 'Mail error: '.$mail->ErrorInfo; 
        return false;
    } else {
        $error = 'Message sent!';
        return true;
    }


?>