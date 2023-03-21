<?php

require( '../../common/connection.php');

$stmt = $con -> prepare('UPDATE werkplanning SET end_year = ?, end_week = ? WHERE id = ?');
$stmt -> bind_param('iii', $_POST['end_year'], $_POST['end_week'], $_POST['id']);
$stmt -> execute();


/*

    */

$stmt = $con -> prepare('SELECT * FROM werkplanning_block WHERE werkplanning_id = ? AND (year > ? OR (year = ? AND week >= ?))');
$stmt -> bind_param('iiii', $_POST['id'], $_POST['end_year'],   $_POST['end_year'], $_POST['end_week']);
$stmt -> execute();
$result = $stmt -> get_result();
while($row = $result -> fetch_assoc())
{
    $stmt_1 = $con -> prepare('DELETE FROM werkplanning_activity WHERE block_id = ?');
    $stmt_1 -> bind_param('i', $row['id']);
    $stmt_1 -> execute();
    
    $stmt_1 = $con -> prepare('DELETE FROM werkplanning_medewerker WHERE block_id = ?');
    $stmt_1 -> bind_param('i', $row['id']);
    $stmt_1 -> execute();
    
    $stmt_1 = $con -> prepare('DELETE FROM werkplanning_block WHERE id = ?');
    $stmt_1 -> bind_param('i', $row['id']);
    $stmt_1 -> execute();
}


$stmt = $con -> prepare('SELECT * FROM werkplanning WHERE id = ? AND end_year = certain_year AND end_week = certain_week');
$stmt -> bind_param('i', $_POST['id']);
$stmt -> execute();
$result = $stmt -> get_result();

$flag = 0;
while($row = $result -> fetch_assoc())
{
    $flag = 1;
}

if($flag == 1)
{
    
    
    $stmt = $con -> prepare('DELETE FROM werkplanning WHERE id = ?');
    $stmt -> bind_param('i', $_POST['id']);
    $stmt -> execute();
}


echo json_encode(['status' => 'success']);
