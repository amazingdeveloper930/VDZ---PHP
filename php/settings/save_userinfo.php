<?php

require('../../common/connection.php');


if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	die ('Dat ziet er niet uit als een e-mailadres?');
}

if (preg_match('/[A-Za-z0-9]+/', $_POST['username']) == 0) {
    die ('Ongeldige gebruikersnaam.');
}

if($_POST['userid'] == '') { //New user


if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
	die ('Wachtwoord moet tussen de 5 en de 20 karakters lang zijn.');
}

// We need to check if the account with that username exists.
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE (username = ? OR email = ?)')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('ss', $_POST['username'], $_POST['email']);
	$stmt->execute();
	$stmt->store_result();
	// Store the result so we can check if the account exists in the database.
	if ($stmt->num_rows > 0) {
		// Username already exists
		echo 'Gebruikersnaam of e-mailadres is al in gebruik.';
		
	} else {
		
		// Username doesnt exists, insert new account
	if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email, account_level, activation_code, img) VALUES (?, ?, ?, ?, ?, ?)')) {
	// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	//$uniqid = uniqid();
	$activation_code = 'activated';
	$stmt->bind_param('ssssss', $_POST['username'], $password, $_POST['email'], $_POST['userrole'], $activation_code, $_POST['group1']);
	$stmt->execute();
	
	echo 'Gebruiker opgeslagen.';
	
	} else {
	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	echo 'Database fout!';
	}		
		
	}
	$stmt->close();
} else {
	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	echo 'Database fout!';
}



} else { //Edit user

if($_POST['password'] != '' && (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5)) {

die ('Wachtwoord moet tussen de 5 en de 20 karakters lang zijn.');
	
}
	
// We need to check if the account with that username exists.
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE (username = ? OR email = ?) AND id != ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('sss', $_POST['username'], $_POST['email'], $_POST['userid']);
	$stmt->execute();
	$stmt->store_result();
	// Store the result so we can check if the account exists in the database.
	if ($stmt->num_rows > 0) {
		// Username already exists
		echo 'Gebruikersnaam of e-mailadres is al in gebruik.';
		
	} else {

	if($_POST['password'] != '') {
	$query = 'UPDATE accounts SET username = ?, password = ?, email = ?, account_level = ?, img = ? WHERE id = ?';		
	} else {
	$query = 'UPDATE accounts SET username = ?, email = ?, account_level = ?, img = ? WHERE id = ?';			
	}
		
	if ($stmt = $con->prepare($query)) {
	// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
	if($_POST['password'] != '') {
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$stmt->bind_param('ssssss', $_POST['username'], $password, $_POST['email'], $_POST['userrole'], $_POST['group1'], $_POST['userid']);
	} else {		
		$stmt->bind_param('sssss', $_POST['username'], $_POST['email'], $_POST['userrole'], $_POST['group1'], $_POST['userid']);
	}	
	$stmt->execute();
	
	echo 'Gebruiker opgeslagen.';	
	
	} else {
	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	echo 'Database fout!';
	}		
		
	}
	$stmt->close();
	} else {
	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	echo 'Database fout!';
	}

}		


$con->close();
?>