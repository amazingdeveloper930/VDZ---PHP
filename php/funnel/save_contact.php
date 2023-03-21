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
    // Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    $stmt->store_result();
    // Store the result so we can check if the account exists in the database.
	

      // email doesnt exists, insert new account

	  $l_status = "0";
      if ($stmt = $con->prepare('INSERT INTO contacts (name, city, address, email, phone, source, c_status, l_status, created_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)')) {
        // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
		$date = date_create();
		$dt = $date->format("Y-m-d");
        $stmt->bind_param('sssssiiis', $_POST['name'], $_POST['city'], $_POST['address'], $_POST['email'], $_POST['phone'], $_POST['source'], $_POST['c_status'], $l_status, $dt);
        $stmt->execute();
        echo 'Contact opgeslagen.';
		$inserted_id = $stmt -> insert_id;
		if($_POST['c_status'] == '3')
		{
			if($stmt = $con -> prepare('INSERT INTO contact_log (contact_id, entry_type, entry_title, entry_date, entry_description, account_id) VALUES (?, ?, ?, ?, ?, ?)'))
			{
				$date = date_create();
    			$dt = $date->format("Y-m-d H:i:s");
				$account_id = isset($_POST['userid'])? $_POST['userid'] : 0;
				$description = 'Convert Contact To Lead';
				$type = 0;
				$title = 'lead';
				$stmt->bind_param('iissss', $inserted_id, $type, $title, $dt, $description, $account_id);
        		$stmt->execute();
			}
			
		}
      }	      

    
    $stmt->close();

  } else {
    // Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
    echo 'Database fout!';
  }

} else { //Edit user

// We need to check if the contact with that name exists.
if ($stmt = $con->prepare('SELECT id FROM contacts WHERE (name = ? OR email = ?) AND id != ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('sss', $_POST['name'], $_POST['email'], $_POST['contactid']);
	$stmt->execute();
	$stmt->store_result();

	
	// if($_POST['c_status'] == '3') {
	// $l_status = "1";
	// } else {
	// $l_status = "0";
	// }
	
	$query = 'UPDATE contacts SET name = ?, city = ?, address = ?, email = ?, phone = ?, source = ?, c_status = ? WHERE id = ?';		

	if ($stmt = $con->prepare($query)) {
	// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
		$stmt->bind_param('sssssiii', $_POST['name'], $_POST['city'], $_POST['address'], $_POST['email'], $_POST['phone'], $_POST['source'], $_POST['c_status'], $_POST['contactid']);
		$stmt->execute();
		echo 'Contact opgeslagen.';
		if($_POST['c_status'] == '3')
		{
			if($stmt = $con -> prepare('INSERT INTO contact_log (contact_id, entry_type, entry_title, entry_date, entry_description, account_id) VALUES (?, ?, ?, ?, ?, ?)'))
			{
				$date = date_create();
    			$dt = $date->format("Y-m-d H:i:s");
				$account_id = isset($_POST['userid'])? $_POST['userid'] : 0;
				$description = 'Convert Contact To Lead';
				$type = 0;
				$title = 'lead';
				$stmt->bind_param('iissss', $_POST['contactid'], $type, $title, $dt, $description, $account_id);
        		$stmt->execute();
				
			}
			
				
		}
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
