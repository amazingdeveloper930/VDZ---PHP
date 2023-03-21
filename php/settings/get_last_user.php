<?php
// Change this to your connection info.
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'b1057_ordersvdz';
$DATABASE_PASS = 'fGvflEVD';
$DATABASE_NAME = 'b1057_ordersvdz';

// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	// If there is an error with the connection, stop the script and display the error.
	die ('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if ($stmt = $con->prepare('SELECT id,username, email FROM accounts ORDER BY id DESC LIMIT 1')) {

$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();

}

while ($row = $result->fetch_assoc()) {

$result_array[] = $row;

}
echo json_encode($result_array);


?>