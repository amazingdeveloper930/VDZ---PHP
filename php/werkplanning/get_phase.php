<?php

require( '../../common/connection.php');

$result = null;
if(isset($_POST['id']) && $_POST['id']!= '') {
    $stmt = $con -> prepare('SELECT * FROM werkplanning WHERE id = ? ORDER BY sort_order');
    $stmt -> bind_param('i', $_POST['id']);
    $stmt -> execute();
    $result = $stmt -> get_result();
}
else{
    $stmt = $con -> prepare('SELECT * FROM werkplanning WHERE contact_id is NULL ORDER BY sort_order');
    $stmt -> execute();
    $result = $stmt -> get_result();
}

$data = [];

while($row = $result -> fetch_assoc())
{
    $item = $row;
    
    $stmt_1 = $con -> prepare('SELECT * FROM werkplanning_block WHERE werkplanning_id = ? ORDER BY id');
    $stmt_1 -> bind_param('i', $row['id']);
    $stmt_1 -> execute();
    $result_1 = $stmt_1 -> get_result();
    $item['block'] = [];
    while($row_1 = $result_1 -> fetch_assoc())
    {
        $item_1 = $row_1;
        $stmt_2 = $con -> prepare('SELECT * FROM werkplanning_activity WHERE block_id = ? ORDER BY id');
        $stmt_2 -> bind_param('i', $item_1['id']);
        $stmt_2 -> execute();
        $result_2 = $stmt_2 -> get_result();
        $item_2 = [];
        while($row_2 = $result_2 -> fetch_assoc())
        {
            $item_2 []= $row_2;
        }
        $item_1['activity'] = $item_2;
        $item['block'] []= $item_1;
    }
    
    $data []= $item;
}
echo json_encode(['data' => $data]);