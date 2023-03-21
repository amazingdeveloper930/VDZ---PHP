<?php

session_start();


require( '../../common/connection.php');


if ($_POST['employeeid'] == '') {

	die ('Er ging iets mis bij het verwijderen.');

}



// We need to check if the account with that username exists.
$file_name = null;

if ($stmt = $con->prepare('SELECT * FROM employees WHERE id = ?')) {
	$stmt->bind_param('s', $_POST['employeeid']);

	$stmt->execute();
	$result = $stmt->get_result();
	while ($row = $result->fetch_assoc()) {
		$file_name = $row['file_path'];
	}
}

if($file_name != '' && $file_name != null)
  	unlink('../../upload/' . $file_name);
if ($stmt = $con->prepare('DELETE FROM employees WHERE id = ?')) {

	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.

	$stmt->bind_param('s', $_POST['employeeid']);

	$stmt->execute();	

	$stmt->close();

	

	echo "Medewerker verwijderd";

	

} else {

	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.

	echo 'Database fout!';

}

$con->close();

?>