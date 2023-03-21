<?php 
require( '../../common/connection.php');
require '../../common/global.php';

$stmt =  $con -> prepare('SELECT * FROM werkplanning WHERE id = ? AND contact_id is NULL');

$stmt -> bind_param('i', $_POST['werkplanning_id']);
$stmt -> execute();
$result_data = [];
$result_data['original_id'] = $_POST['werkplanning_id'];
$result = $stmt -> get_result();
while($row = $result -> fetch_assoc())
{
    $stmt_1 = $con -> prepare('INSERT INTO werkplanning (name, contact_id, default_werkplanning_id, length, start, mode, hour, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt_1 -> bind_param('siiiisii', $row['name'], $_POST['contact_id'], $_POST['werkplanning_id'], $row['length'], $row['start'], $row['mode'], $row['hour'], $row['sort_order']);
    $stmt_1 -> execute();
    $result_data['new_id'] = $stmt_1 -> insert_id;
}
$result_data['block'] = [];
if(isset($result_data['new_id']))
{
    $stmt = $con -> prepare('SELECT * FROM werkplanning_block WHERE werkplanning_id = ? ORDER BY id');
    $stmt -> bind_param('i', $result_data['original_id']);
    $stmt -> execute();
    $result = $stmt -> get_result();
    
    while($row = $result -> fetch_assoc())
    {
        $item = [];
        $item['original_id'] = $row['id'];
        $stmt_1 = $con -> prepare('INSERT INTO werkplanning_block (werkplanning_id, week, day) VALUES (?, ?, ?)');
        $stmt_1 -> bind_param('iii', $result_data['new_id'], $row['week'], $row['day']);
        $stmt_1 -> execute();
        $item['new_id'] = $stmt_1 -> insert_id;
        
        $stmt_1 = $con -> prepare('SELECT * FROM werkplanning_activity WHERE werkplanning_id = ? AND block_id = ? ORDER BY id');
        $stmt_1 -> bind_param('ii', $_POST['werkplanning_id'], $row['id']);
        $stmt_1 -> execute();
        $result_1 = $stmt_1 -> get_result();
        $item['activity'] = [];
        while($row_1 = $result_1 -> fetch_assoc())
        {
            $item_1 = [];
            $item_1['original_id'] = $row_1['id'];
            $stmt_2 = $con -> prepare('INSERT INTO werkplanning_activity (werkplanning_id, block_id, text) VALUES (?, ?, ?)');
            $stmt_2 -> bind_param('iis', $result_data['new_id'], $item['new_id'], $row_1['text'] );
            $stmt_2 -> execute();
            $item_1['new_id'] = $stmt_2 -> insert_id;
            $item['activity'] []= $item_1;
        }
        $result_data['block'] []= $item;
    }
}
echo json_encode($result_data);