<?php

// Change this to your connection info.

require( '../../common/connection.php');

if ($stmt = $con->prepare('SELECT * FROM employees WHERE inweekplanning = "Ja" ORDER BY sort_order')) {
    $result_array = [];
    $stmt -> execute();
    $result = $stmt -> get_result();
    while($row = $result -> fetch_assoc())
    {
        $result_array []= $row;
    }

    echo json_encode(['data' => $result_array]);
}