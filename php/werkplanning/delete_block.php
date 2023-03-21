<?php

require( '../../common/connection.php');
require( '../../common/global.php');
require ('../../common/sessie_check.php');

$stmt = $con -> prepare('SELECT * FROM werkplanning_activity WHERE block_id = ?');
$stmt -> bind_param('i', $_POST['block_id']);
$stmt -> execute();
$result = $stmt -> get_result();
$text_arr = [];
$contact_id = '';
while($row = $result -> fetch_assoc())
{
    $text_arr []= $row['text'];
    $contact_id = $row['contact_id'];
}

if(count($text_arr) > 0)
    addNewLog($contact_id, $_SESSION['id'], 'Kaart verwijderd van planning: ' . join(", ",$text_arr) );

$stmt = $con -> prepare('DELETE FROM werkplanning_activity WHERE block_id = ?');
$stmt -> bind_param('i', $_POST['block_id']);
$stmt -> execute();

$stmt = $con -> prepare('DELETE FROM werkplanning_medewerker WHERE block_id = ?');
$stmt -> bind_param('i', $_POST['block_id']);
$stmt -> execute();

$stmt = $con -> prepare('DELETE FROM werkplanning_block WHERE id = ?');
$stmt -> bind_param('i', $_POST['block_id']);
$stmt -> execute();

echo json_encode(['status' => 'success']);