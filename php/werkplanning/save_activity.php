<?php

require( '../../common/connection.php');
require( '../../common/global.php');
require ('../../common/sessie_check.php');

$stmt = $con -> prepare('INSERT INTO werkplanning_activity (contact_id, werkplanning_id, block_id, text) VALUES (?, ?, ?, ?)');

$stmt -> bind_param('iiis', $_POST['contact_id'], $_POST['werkplanning_id'], $_POST['block_id'], $_POST['text']);

$stmt -> execute();

$id = $stmt -> insert_id;

$stmt = $con -> prepare('SELECT * FROM werkplanning_activity WHERE id = ?');
$stmt -> bind_param('i', $id);
$stmt -> execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$title = "Medewerker ingepland";

addNewLog($_POST['contact_id'], $_SESSION['id'], $title, $_POST['text']);



echo json_encode(['status' => 'success', 'data' => $data]);