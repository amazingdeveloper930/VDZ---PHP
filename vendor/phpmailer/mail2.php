<?php
/**
 * This example shows sending a message using PHP's mail() function.
 */

$message = "<html>
<body leftmargin=\"0\" marginwidth=\"0\" topmargin=\"0\" marginheight=\"0\" offset=\"0\">

<table width=\"100%\" bgcolor=\"#e9e9e9\" cellpadding=\"10\" cellspacing=\"0\" background=\"\">




	<tr valign=\"top\" align=\"center\">
		<td>
			<table width=\"550\" cellpadding=\"0\" cellspacing=\"0\">
				<tr>
					<td style=\"padding:0px\" bgcolor=\"#ffffff\">
						&nbsp;&nbsp;&nbsp;<img width=\"218\" height=\"80\" src=\"http://www.hufman.nl/email/logo.jpg\" alt=\"header Hufman email\" style=\"margin-top:0px;margin-left:15px;float:left\" />
						
																</td>
				</tr>				
				<tr>
					<td style=\"margin: 25px; padding:15px;font-family: Verdana,sans-serif;font-size:14px;line-height:21px\" bgcolor=\"#ffffff\">
						
				 Beste $naam,<br /><Br />
	
Bedankt voor de inschrijving voor de theoriecursus motor. Je wordt om $tijdc op $datumc verwacht op de $locatiec.
	Je theorie-examen is om $tijde op $datume. Je hebt hiervan een bevestigingsmail had van het cbr. Hierop staat het tijdstip vermeldt
	van je theorie-examen en dat jij je <b><u>legitimatie</u></b> moet meenemen. Je hebt betaald met iDeal.  <Br /><br />
	
	Naast het theorielokaal is een Esso benzinepomp. Hier kan je eventueel drinken en eten (gezonde broodjes & snacks) kopen. <Br /><br />
 
	Mocht je nog vragen hebben, dan ben ik te bereiken op nummer: 06-54362630. <Br /><br />

	
	Met vriendelijke groet, <Br /><Br />
	Andr&eacute; Hufman <Br /><Br />
						</p><Br /><Br />
						
					</td>
				</tr>
				<tr>
					<td bgcolor=\"#393536\" style=\"text-align:center\">

						<p style=\"font-family:Verdana,Helvetica,Arial,sans-serif; line-height: 13px; color: #ffffff; font-size: 9px;\"><br />Copyright &copy; 2015 Rijschool Hufman<br /><br />
</p>
					</td>
				</tr>
				
			
				
				<tr>
					<td style=\"padding: 10px;\" >
						<br /><Br /><Br />
					</td>
				</tr>
				
				
			</table>
		</td>
	</tr>
</table>

</body>
</html>";

 
require 'phpmailer/PHPMailerAutoload.php';
/*include("phpmailer/class.smtp.php");*/

//Create a new PHPMailer instance
$mail = new PHPMailer;

$mail->IsSMTP(); // telling the class to use SMTP
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->SMTPSecure = "tls";                 // sets the prefix to the servier
$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
$mail->Port       = 587;                   // set the SMTP port for the GMAIL server
$mail->Username   = "info@hufman.nl";  // GMAIL username
$mail->Password   = "Daghufman123";            // GMAIL password

//Set who the message is to be sent from
$mail->setFrom('info@hufman.nl', 'Rijschool Hufman');
//Set an alternative reply-to address
$mail->addReplyTo('info@hufman.nl', 'Rijschool Hufman');
//Set who the message is to be sent to
$mail->addAddress('sandervh@outlook.com','Sander van Horen');
$mail->addAddress('justinkraak@live.nl','Justin Kraak');
$mail->addAddress('info@logo4life.nl','Sander van Horen');
//Set the subject line
$mail->Subject = 'Bevestiging van je inschrijving voor de cursus';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML($message);
//Replace the plain text body with one created manually
//$mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
