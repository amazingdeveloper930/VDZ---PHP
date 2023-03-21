<?php

require( '../../common/connection.php');

$stmt = $con -> prepare('INSERT INTO werkplanning_block (werkplanning_id, contact_id, year, week, day, datum) VALUES (?, ?, ?, ?, ?, ?)');

$stmt->bind_param('iiiiis', $_POST['werkplanning_id'], $_POST['contact_id'],  $_POST['year'], $_POST['week'],  $_POST['day'], $_POST['datum']);

$stmt->execute();

$id = $stmt -> insert_id;

$data = [];
$data['id'] = $id;
$data['activity'] = [];
$data['medewerker'] = [];
echo json_encode(['message' => 'success', 'data' => $data]);