<?php

require( '../../common/connection.php');
require( '../../common/global.php');
require ('../../common/sessie_check.php');


$stmt = $con -> prepare('SELECT * FROM werkplanning_activity WHERE id = ?');
$stmt -> bind_param('i', $_POST['id']);
$stmt -> execute();

$result = $stmt -> get_result();
while($row = $result -> fetch_assoc())
{
    addNewLog($row['contact_id'], $_SESSION['id'], 'Kaart verwijderd van planning: ' . $row['text']);
}

$stmt = $con -> prepare("DELETE FROM werkplanning_activity WHERE id = ?");
$stmt -> bind_param('i', $_POST['id']);
$stmt -> execute();

echo json_encode(['status' => 'success']);