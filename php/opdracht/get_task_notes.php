<?php

require( '../../common/connection.php');

$result_array = [];
if ($stmt = $con->prepare('SELECT PTN.*, A.username FROM projects_tasks_notes PTN LEFT JOIN accounts A ON PTN.user_id = A.id WHERE PTN.contact_id = ? AND PTN.project_tasks_id = ? ORDER BY PTN.created_at DESC')) {
    $stmt -> bind_param('ii', $_POST['contact_id'], $_POST['task_id']);
    $stmt -> execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $dt = new DateTime($row['created_at']);
    $row['created_at']  =   $dt->format('d-m-Y') . " " . $dt->format('H:i');  
    $result_array[] = $row;
    }

}
echo json_encode($result_array);




?>