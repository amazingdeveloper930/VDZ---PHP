<?php

require( '../../common/connection.php');


$stmt = $con -> prepare('INSERT INTO werkplanning_medewerker (contact_id, werkplanning_id, block_id, employee_id, hour, daypart) VALUES (?, ?, ?, ?, ?, ?)');

$stmt -> bind_param('iiiiis', $_POST['contact_id'], $_POST['werkplanning_id'], $_POST['block_id'], $_POST['employee_id'], $_POST['hour'], $_POST['daypart']);

$stmt -> execute();

$id = $stmt -> insert_id;

$stmt = $con -> prepare('SELECT WM.*, E.name, E.achternaam FROM werkplanning_medewerker WM LEFT JOIN  employees E ON WM.employee_id = E.id WHERE WM.id = ?');
$stmt -> bind_param('i', $id);
$stmt -> execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
echo json_encode(['status' => 'success', 'data' => $data]);
