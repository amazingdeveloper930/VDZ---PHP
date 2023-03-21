<?php
session_start();
require( '../../common/connection.php');
require('../../common/global.php');

$email = decrypt($_POST['EL']);
$password_posted = decrypt($_POST['SS']);
$time = decrypt($_POST['TL']);
$path = $_POST['PATH'];

// if(strtotime(date("Y-m-d H:i:s")) - $time > 60)
// {
//     header('Location: https://orders2.vanderzeeuwbouw.nl/');
// 	exit();
// }

if ($stmt = $con->prepare('SELECT id, username, password, account_level, activation_code, img FROM accounts WHERE email = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $email);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();
}

if ($stmt->num_rows > 0) {

    $stmt->bind_result($id, $username, $password, $account_level, $activation, $img);
	$stmt->fetch();
	
	if($activation != 'activated') {
		
	echo 'Please activate your account!';
	
	} else {

        if ($password_posted == $password) {
            // Verification success! User has loggedin!
            // Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $username;
            $_SESSION['ac_level'] = $account_level;
            $_SESSION['id'] = $id;
            $_SESSION['img'] = $img;
            header('Location: https://orders2.vanderzeeuwbouw.nl' . $path);
	        exit();
            
        } else {
            
            header('Location: https://orders2.vanderzeeuwbouw.nl/');
	        exit();
        }
    }
}
else{
    header('Location: https://orders2.vanderzeeuwbouw.nl/');
	exit();
}