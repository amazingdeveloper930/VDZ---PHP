<?php

session_start();


require( '../../common/connection.php');


if ($_POST['supplierid'] == '') {

	die ('Er ging iets mis bij het verwijderen.');

}



// We need to check if the account with that username exists.

if ($stmt = $con->prepare('DELETE FROM suppliers WHERE id = ?')) {

	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.

	$stmt->bind_param('s', $_POST['supplierid']);

	$stmt->execute();	

	$stmt->close();

	

	echo "Leverancier verwijderd";

	

} else {

	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.

	echo 'Database fout!';

}

$con->close();

?>