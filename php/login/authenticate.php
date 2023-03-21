<?php
session_start();
require( '../../common/connection.php');

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, username, password, account_level, activation_code, img FROM accounts WHERE email = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $_POST['email']);
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
	
	// Account exists, now we verify the password.
	// Note: remember to use password_hash in your registration file to store the hashed passwords.
	if (password_verify($_POST['password'], $password)) {
		// Verification success! User has loggedin!
		// Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
		session_regenerate_id();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['name'] = $username;
		$_SESSION['ac_level'] = $account_level;
		$_SESSION['id'] = $id;
		$_SESSION['img'] = $img;
		echo 'Ingelogd!';
		
	} else {
		
		echo 'Incorrect password!';
	}
	}
	
} else {
	echo 'Incorrect username!';
}

$stmt->close();