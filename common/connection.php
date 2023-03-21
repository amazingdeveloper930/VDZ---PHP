<?php

// Change this to your connection info.

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'b1057_ordersvdz';


//$DATABASE_HOST = 'localhost';
// $DATABASE_USER = 'b1057_ordersvdz27';
// $DATABASE_PASS = 'herstellen';
// $DATABASE_NAME = 'b1057_ordersvdz27';


// Try and connect using the info above.

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_errno()) {

	// If there is an error with the connection, stop the script and display the error.

	die ('Failed to connect to MySQL: ' . mysqli_connect_error());

}

?>