<?php

// Change this to your connection info.

require( '../../common/connection.php');



if ($stmt = $con->prepare('SELECT * FROM suppliers WHERE id = ?')) {



$stmt->bind_param('s', $_POST['supplierid']);

$stmt->execute();

// Store the result so we can check if the account exists in the database.

$result = $stmt->get_result();



}



while ($row = $result->fetch_assoc()) {



$result_array[] = $row;



}

echo json_encode($result_array);





?>