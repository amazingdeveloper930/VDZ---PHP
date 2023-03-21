<?php

session_start();
require( '../../common/connection.php');

if ($_POST['clog'] == '') {
	die ('Er ging iets mis bij het verwijderen.');
}

// We need to check if the account with that username exists.



$contact_id = null;
$l_status = 0;
if ($stmt = $con->prepare('SELECT * FROM contact_log WHERE id = ? ORDER BY id DESC LIMIT 1')) {
	$stmt->bind_param('s',$_POST['clog']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result = $stmt->get_result();
  }
  $file_name = '';
  while ($row = $result->fetch_assoc()) {
	$contact_id = $row['contact_id'];
	$file_name = $row['file_path'];
  }

  
  if($file_name != '' && $file_name != null)
  unlink('../../upload/' . $file_name);



if ($stmt = $con->prepare('DELETE FROM contact_log WHERE id = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('s', $_POST['clog']);
	$stmt->execute();	
	$stmt->close();



	// if ($stmt = $con->prepare('SELECT * FROM contact_log WHERE contact_id = ? AND entry_title like "lead" ORDER BY id DESC LIMIT 1')) {
	// 	$stmt->bind_param('i', $contact_id);
	// 	$stmt->execute();
	// 	// Store the result so we can check if the account exists in the database.
	// 	$result = $stmt->get_result();
	//   }
	  
	//   while ($row = $result->fetch_assoc()) {
	// 	$l_status = $row['entry_type'];
	//   }


	//   $query = 'UPDATE contacts SET l_status = ? WHERE id = ?';		

	//   if ($stmt = $con->prepare($query)) {
	//   // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
	// 	$stmt->bind_param('ii', $l_status, $contact_id);
	// 	$stmt->execute();
	//   } else {

	//   }	
  



	echo "Logboek item verwijderd.";	

} else {
	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	echo 'Database fout!';
}

$con->close();

?>