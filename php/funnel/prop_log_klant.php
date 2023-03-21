<?php

require( '../../common/connection.php');

$stmt = $con -> prepare('SELECT * FROM contact_log where id = ?');
$stmt -> bind_param('i', $_POST['contact_log_id']);
$stmt -> execute();

$result = $stmt -> get_result();

$flag = 0;
while($row = $result -> fetch_assoc())
{
    $flag = $row['klanten'];
}

$flag = !$flag;
$stmt = $con -> prepare('UPDATE contact_log set klanten = ? WHERE id = ?');
$stmt -> bind_param('ii', $flag, $_POST['contact_log_id']);
$stmt -> execute();

echo json_encode(['klanten' => $flag]);