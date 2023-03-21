<?php

require( '../../common/connection.php');
require( '../../common/global.php');

$stmt = $con -> prepare("INSERT INTO file_folder (name, contact_id, parent, created_at, account_id) VALUES (?, ?, ?, ?, ?)");

$date = date_create();
$dt = $date->format("Y-m-d H:i:s");


$stmt -> bind_param('siisi', $_POST['title'], $_POST['contact_id'], $_POST['folder_id'], $dt, $_POST['user_id']);
$stmt -> execute();

$folder_id = $stmt -> insert_id;

$result_item = [];
$stmt = $con -> prepare("SELECT FF.*, A.username FROM file_folder FF LEFT JOIN accounts A ON FF.account_id = A.id WHERE FF.id = ? LIMIT 1");
$stmt -> bind_param('i', $folder_id);
$stmt -> execute();
$result = $stmt->get_result();
while($row = $result -> fetch_assoc())
{
    $result_item = $row;
}

echo json_encode(['message' => 'Bestand opgeslagen', 'item' => $result_item]);
