<?php 
require( '../../common/connection.php');
require '../../common/global.php';

$werkplanning_id = $_POST['werkplanning_id'];
$stmt = $con -> prepare("SELECT W.default_werkplanning_id FROM werkplanning W WHERE W.id = ? AND default_werkplanning_id is NOT NULL");
$stmt -> bind_param('i', $_POST['werkplanning_id']);
$stmt -> execute();
$result = $stmt -> get_result();
while($row = $result -> fetch_assoc())
{

    $stmt_2 = $con -> prepare('SELECT * FROM werkplanning WHERE id = ?');
    $stmt_2 -> bind_param('i', $row['default_werkplanning_id']);
    $stmt_2 -> execute();
    $result_2 = $stmt_2 -> get_result();
    while($row_2 = $result_2 -> fetch_assoc())
    {
        $werkplanning_id = $row['default_werkplanning_id'];
    }
}


$stmt = $con -> prepare('SELECT WA.text FROM werkplanning_activity WA LEFT JOIN werkplanning_block WB ON WA.block_id = WB.id WHERE WA.werkplanning_id = ? AND WB.week = ? ORDER BY WA.id');

$stmt -> bind_param('ii', $werkplanning_id, $_POST['week']);
$stmt -> execute();

$result = $stmt -> get_result();

$result_array = [];

while($row = $result -> fetch_assoc())
{
    $result_array []= $row['text'];
}

echo json_encode(['activity' => $result_array]);