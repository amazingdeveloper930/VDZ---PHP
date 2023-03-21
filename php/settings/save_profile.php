<?php
session_start();

require( '../../common/connection.php');

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	die ('Dat ziet er niet uit als een e-mailadres?');
}

if (preg_match('/[A-Za-z0-9]+/', $_POST['username']) == 0) {
    die ('Ongeldige gebruikersnaam.');
}

if ((strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) && $_POST['password'] != '') {
	die ('Wachtwoord moet tussen de 5 en 20 karakters zijn');
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
	$query = 'UPDATE accounts SET username = ?, password = ?, email = ?, img = ? WHERE id = ?';		
	} else {
	$query = 'UPDATE accounts SET username = ?, email = ?, img = ? WHERE id = ?';			
	}
		
	// Username doesnt exists, insert new account
	if ($stmt = $con->prepare($query)) {
	// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
	if($_POST['password'] != '') {
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$stmt->bind_param('sssss', $_POST['username'], $password, $_POST['email'], $_POST['group1'], $_POST['userid']);
	} else {		
		$stmt->bind_param('ssss', $_POST['username'], $_POST['email'], $_POST['group1'], $_POST['userid']);
	}	
	$stmt->execute();
	
	
	
	$_SESSION['name'] = $_POST['username'];		
	$_SESSION['img'] = $_POST['group1'];

	
	if((int)($_POST['data_img_changed']) == 1 )
	{
		$filename = "";

		if(count($_FILES) > 0)
		{
			$filename = $_FILES['file']['name'];
			$filename = str_replace(' ', '-', $filename);
			$filename =  preg_replace('/[^A-Za-z0-9.\-]/', '', $filename);
			$filename = strtotime("now") . $filename;
			$location = "../../upload/" . $filename;
			$maxDim = 820;
			$file_tmp_name = $_FILES['file']['tmp_name'];
			list($width, $height, $type, $attr) = getimagesize( $file_tmp_name );
				
			move_uploaded_file($_FILES['file']['tmp_name'],$location);
		
		}


		
		$stmt -> prepare('UPDATE accounts SET img_path = ? WHERE id = ?');
		$stmt -> bind_param("si", $filename, $_POST['userid']);
		$stmt->execute();
		$_SESSION['img_path'] = $filename;
	}

	echo 'Wijzigingen opgeslagen';
	
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
$con->close();
?>
