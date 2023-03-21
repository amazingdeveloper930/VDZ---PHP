<?php



require( '../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php');	

require( '../../common/connection.php');




//Create a new PHPMailer instance

	$mail = new PHPMailer;



	$mail->IsSMTP(); // telling the class to use SMTP

	$mail->SMTPAuth   = true;                  // enable SMTP authentication

	$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier

	//$mail->Host       = "n3plcpnl0244.prod.ams3.secureserver.net";      // sets GMAIL as the SMTP server

	$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server

	$mail->Port       = 465;                   // set the SMTP port for the GMAIL server

	//$mail->Username   = "configurator@groterwonen.nl";  	   // GMAIL username

	//$mail->Password   = "aanbouwplaatsen19!";        // GMAIL password

	$mail->Username   = "configurator@groterwonen.nl";  	   // GMAIL username

	$mail->Password   = "Configurator123!";        // GMAIL password

	

	

if ($stmt = $con->prepare('SELECT quotes.pdf_file, contacts.email FROM quotes LEFT JOIN contacts ON(contacts.id = quotes.contact_id)  WHERE quotes.id = ? ORDER BY quotes.id DESC LIMIT 1')) {

        $stmt->bind_param('i', $_POST['qID']);
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $result = $stmt->get_result();
        
}
       
$pdf_file = '';
$email = '';
        
while ($row = $result->fetch_assoc()) {
    $pdf_file = $row['pdf_file'];
	$email = $row['email'];
}



$mail->setFrom('info@groterwonen.nl', 'GroterWonen');

$mail->addReplyTo('info@groterwonen.nl', 'GroterWonen');

$mail->addBCC('configurator@groterwonen.nl');

$mail->addAddress($email);

$mail->Subject = "Offerte voor uw aanbouw";	

$mail->addAttachment("../../pdf/".$pdf_file); 





// Compose a simple HTML email message

$message = "<html>

<body leftmargin=\"0\" marginwidth=\"0\" topmargin=\"0\" marginheight=\"0\" offset=\"0\">



<table width=\"100%\" bgcolor=\"#e9e9e9\" cellpadding=\"10\" cellspacing=\"0\" background=\"\">









	<tr valign=\"top\" align=\"center\">

		<td>

			<table width=\"550\" cellpadding=\"0\" cellspacing=\"0\">

				<tr>

					<td align=\"center\" style=\"padding:0px;text-align:center;border-bottom:7px solid #1dbded\" bgcolor=\"#2c3255\">

						&nbsp;&nbsp;&nbsp;<img width=\"300\" height=\"90\" src=\"https://www.groterwonen.nl/model/logo.png\" alt=\"Logo Groterwonen\" />						

					</td>

				</tr>				

				<tr>

					<td style=\"margin: 25px; padding:15px;font-family: Verdana,sans-serif;font-size:14px;line-height:21px\" bgcolor=\"#ffffff\">

						

				 Beste klant<br /><br />
				 
				 In de bijlage vind u uw offerte.<br /><br />

	

	Groterwonen.nl B.V.<Br />

	023 - 555 15 51<Br /><br />

	

	<img width=\"220\" height=\"92\" src=\"https://www.groterwonen.nl/model/3-gezichten-2021.png\" alt=\"Gezichten achter Groterwonen\" />						

	

	<Br /><br />

						</p><Br />

						

					</td>

				</tr>

				<tr>

					<td bgcolor=\"#e9e9e9\" style=\"text-align:center\">



						<p style=\"font-family:Verdana,Helvetica,Arial,sans-serif; line-height: 16px; color: #7d7d7d; font-size: 11px;\"><br />Copyright &copy; 2020 GroterWonen<br /><br />

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





// Sending email



//mail($to, $subject, $message, $headers);



$mail->msgHTML($message);





if (!$mail->send()) {

    echo "Mailer Error: " . $mail->ErrorInfo;

} else {

    echo "E-mail verstuurd naar de klant.";

}



?>