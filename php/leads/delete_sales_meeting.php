<?php

session_start();


require( '../../common/connection.php');


if ($_POST['salesmeetingid'] == '') {

	die ('Er ging iets mis bij het verwijderen.');

}



// We need to check if the account with that username exists.

if ($stmt = $con->prepare('DELETE FROM sales_meeting WHERE id = ?')) {

	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.

	$stmt->bind_param('s', $_POST['salesmeetingid']);

	$stmt->execute();	

	$stmt->close();

	

	echo json_encode(['message' => "Afspraak verwijderd."]);

	

} else {

	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
    echo json_encode(['message' => "Database fout!"]);

}

$con->close();

?>