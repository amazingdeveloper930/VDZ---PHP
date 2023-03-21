<?php

require('../../common/connection.php');

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	die ('Dat ziet er niet uit als een e-mailadres?');
}

if (preg_match('/[A-Za-z0-9]+/', $_POST['name']) == 0) {
    die ('Ongeldige gebruikersnaam.');
}

if($_POST['contactid'] == '') { //New contact

  // We need to check if the account with that name exists.
  if ($stmt = $con->prepare('SELECT id FROM contacts WHERE email = ?')) {

      // email doesnt exists, insert new account
      if ($stmt = $con->prepare('INSERT INTO contacts (name, city, address, email, phone, source, c_status) VALUES (?, ?, ?, ?, ?, ?, ?)')) {
        // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
        $stmt->bind_param('sssssii', $_POST['name'], $_POST['city'], $_POST['address'], $_POST['email'], $_POST['phone'], 0, 3);
        $stmt->execute();
        echo 'Lead opgeslagen.';
      } else {
        // Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
        echo 'Database fout!';
      }		      

    
    $stmt->close();

  } else {
    // Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
    echo 'Database fout!';
  }

} else { //Edit user

// We need to check if the contact with that name exists.
if ($stmt = $con->prepare('SELECT id FROM contacts WHERE (name = ? OR email = ?) AND id != ?')) {
	

	
	$query = 'UPDATE contacts SET name = ?, city = ?, address = ?, email = ?, phone = ? WHERE id = ?';		

	if ($stmt = $con->prepare($query)) {
	// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
		$stmt->bind_param('sssssi', $_POST['name'], $_POST['city'], $_POST['address'], $_POST['email'], $_POST['phone'], $_POST['contactid']);
		$stmt->execute();
		echo 'Lead opgeslagen.';

	} else {

	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	echo 'Database fout!';
	}	



	$stmt->close();

	} else {

	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	echo 'Database fout!';

	}

}


$con->close();

?>