<?php

require( '../../common/connection.php');

$stmt = $con -> prepare("DELETE FROM werkplanning_medewerker WHERE id = ?");
$stmt -> bind_param('i', $_POST['id']);
$stmt -> execute();

echo json_encode(['status' => 'success']);