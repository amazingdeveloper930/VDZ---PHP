<?php

require( '../../common/connection.php');
require( '../../common/global.php');

$stmt = $con -> prepare('SELECT SUM(week) as WEEK FROM project_planning WHERE contact_id = ?');
$stmt -> bind_param('i', $_POST['contact_id']);
$stmt -> execute();
$week = 0;
$result = $stmt -> get_result();
while($row = $result -> fetch_assoc())
{
    $week = $row['WEEK'];
}

echo json_encode(['week' => $week]);
