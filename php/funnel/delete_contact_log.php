<?php

session_start();
require( '../../common/connection.php');

if ($_POST['clog'] == '') {
	die ('Er ging iets mis bij het verwijderen.');
}

// We need to check if the account with that username exists.

$stmt = $con->prepare('SELECT * FROM contact_log WHERE id = ?');

$stmt -> bind_param('i', $_POST['clog']);
$stmt -> execute();
$result = $stmt -> get_result();
$file_name = '';
while ($row = $result->fetch_assoc()) {
	$file_name = $row['file_path'];
}
if($file_name != '' && $file_name != null)
unlink('../../upload/' . $file_name);



if ($stmt = $con->prepare('DELETE FROM contact_log WHERE id = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('s', $_POST['clog']);
	$stmt->execute();	
	$stmt->close();

	echo "Logboek item verwijderd.";	

} else {
	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	echo 'Database fout!';
}

$con->close();

?>