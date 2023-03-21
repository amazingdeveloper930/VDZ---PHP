<?php
session_start();

// Change this to your connection info.
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'b1057_ordersvdz';
$DATABASE_PASS = 'fGvflEVD';
$DATABASE_NAME = 'b1057_ordersvdz';

// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	// If there is an error with the connection, stop the script and display the error.
	die ('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if ($_POST['user'] == '') {
	die ('Er ging iets mis bij het verwijderen.');
}

// We need to check if the account with that username exists.
if ($stmt = $con->prepare('DELETE FROM accounts WHERE id = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('s', $_POST['user']);
	$stmt->execute();	
	$stmt->close();
	
	echo "Gebruiker verwijderd.";
	
} else {
	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	echo 'Database fout!';
}
$con->close();
?>