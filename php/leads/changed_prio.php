<?php

session_start();

require( '../../common/connection.php');
$stmt = $con -> prepare("UPDATE contacts SET prio = ? WHERE id = ?");
$stmt -> bind_param("ii", $_POST['prio'], $_POST['contact_id']);
$stmt -> execute();

echo json_encode(['status' => 'success']);