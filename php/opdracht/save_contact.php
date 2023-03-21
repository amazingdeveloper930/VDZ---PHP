<?php

require('../../common/connection.php');

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	die ('Dat ziet er niet uit als een e-mailadres?');
}

if (preg_match('/[A-Za-z0-9]+/', $_POST['name']) == 0) {
    die ('Ongeldige gebruikersnaam.');
}

if($_POST['contactid'] == '') { //New contact

	echo 'Database fout!';

} else { //Edit user

// We need to check if the contact with that name exists.
if ($stmt = $con->prepare('SELECT id FROM contacts WHERE (name = ? OR email = ?) AND id != ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('sss', $_POST['name'], $_POST['email'], $_POST['contactid']);
	$stmt->execute();
	$stmt->store_result();
	// Store the result so we can check if the account exists in the database.
	if ($stmt->num_rows > 0) {
		// name already exists
		echo 'Gebruikersnaam of e-mailadres is al in gebruik.';
	} else {
	
	// if($_POST['c_status'] == '3') {
	// $l_status = "1";
	// } else {
	// $l_status = "0";
	// }
	
	$query = 'UPDATE contacts SET name = ?, city = ?, address = ?, email = ?, phone = ? WHERE id = ?';		

	if ($stmt = $con->prepare($query)) {
	// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
		$stmt->bind_param('sssssi', $_POST['name'], $_POST['city'], $_POST['address'], $_POST['email'], $_POST['phone'], $_POST['contactid']);
		$stmt->execute();
		echo 'Contact opgeslagen.';
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
