<?php

require( '../../common/connection.php');
require( '../../common/global.php');

if ($stmt = $con->prepare('SELECT PT.id, PT.timer,  PTL.contact_id, PTL.started_at, PTL.status  FROM projects_tasks PT LEFT JOIN project_tasks_lines PTL ON PT.id = PTL.projects_tasks_id AND PTL.contact_id = ? 
WHERE PTL.status = "PROCESSING" AND PTL.contact_id = ?
ORDER BY PT.chapter ASC,  PT.sort_order ASC')) {
$stmt->bind_param('ii', $_POST['contactid'],  $_POST['contactid']);
$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();
$hour = null;
$result_array = [];
$result_array['timer_widget'] = null;

while ($row = $result->fetch_assoc()) {

    $difTime = dateDifferenceT2($row['started_at'], $row['timer']);
    if($hour == null || $difTime < $hour)
        {
            $hour = $difTime;
            $row['timer_widget'] = getTaskTimer($row['started_at'],  $row['timer'], 1);
            $result_array = $row;
        }
    
}

$stmt = $con -> prepare('SELECT * FROM contacts WHERE id = ?');
$stmt -> bind_param('i', $_POST['contactid']);
$stmt->execute();

$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {

    $result_array['email'] = $row['email'];
    $result_array['phone'] = $row['phone'];
    $result_array['name'] = $row['name'];
    $result_array['address'] = $row['address'];
}


echo json_encode($result_array);
}
