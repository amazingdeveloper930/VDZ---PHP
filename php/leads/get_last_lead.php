<?php

require( '../../common/connection.php');

if ($stmt = $con->prepare('SELECT id, name, city, address, email, phone, source, c_status, l_status FROM contacts ORDER BY id DESC LIMIT 1')) {

$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();

}

while ($row = $result->fetch_assoc()) {
$result_array[] = $row;
}

echo json_encode($result_array);


?>