<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page..
if (!isset($_SESSION['loggedin'])) {
	header('Location: http://127.0.0.1:8000/');
	exit();
}
?>
		