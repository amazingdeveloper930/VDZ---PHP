<?php

require( '../../common/connection.php');

if($_POST['st_id'])
{
    $stmt = $con -> prepare('SELECT * FROM special_tasks WHERE id = ?');
    $stmt -> bind_param('i', $_POST['st_id']);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $result_array = [];
    while ($row = $result->fetch_assoc()) {
        $result_array = $row;
    }
    echo json_encode($result_array);
    $stmt -> close();
    $con -> close();
}