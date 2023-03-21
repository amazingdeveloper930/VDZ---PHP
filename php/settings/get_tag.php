<?php


require( '../../common/connection.php');

if($stmt = $con -> prepare('SELECT * FROM tags WHERE id = ?'))
{
    $stmt -> bind_param('i', $_POST['tag_id']);
    $stmt -> execute();
    $item = [];
    $result = $stmt -> get_result();
    while ($row = $result->fetch_assoc()) {
        $item []= $row;  
    }

    echo json_encode($item);
}
