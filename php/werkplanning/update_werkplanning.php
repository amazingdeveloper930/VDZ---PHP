<?php

require( '../../common/connection.php');

$stmt = $con -> prepare('UPDATE werkplanning SET name = ? WHERE id = ? AND contact_id = ?');
$stmt -> bind_param('sii', $_POST['name'], $_POST['werkplanning_id'], $_POST['contact_id']);

$stmt -> execute();

echo json_encode(['status' => 'success']);