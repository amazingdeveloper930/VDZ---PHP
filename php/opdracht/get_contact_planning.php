<?php

require( '../../common/connection.php');
require( '../../common/global.php');
$result_array = [];

if($_POST['contact_id']) {

    $stmt = $con -> prepare('SELECT * FROM project_planning WHERE contact_id = ? ORDER BY sort_order');
    $stmt -> bind_param('i', $_POST['contact_id']);
    $stmt -> execute();
    $result = $stmt -> get_result();
    
    while ($row = $result->fetch_assoc()) {
        $result_array  []= $row;
    }
    
}
echo json_encode($result_array);