<?php

require( '../../common/connection.php');

if ($stmt = $con->prepare('SELECT CL.id, CL.entry_type, CL.entry_date, CL.entry_description, A.username FROM contact_log CL LEFT JOIN accounts A ON CL.account_id = A.id Where CL.contact_id = ? ORDER BY CL.entry_date DESC LIMIT 1')) {
$stmt->bind_param('i', $_POST['contactid']);
$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();
}

while ($row = $result->fetch_assoc()) {
$result_array[] = $row;
}

echo json_encode($result_array);

?>