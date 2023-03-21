<?php

require('../../common/connection.php');

if($stmt = $con -> prepare('SELECT * FROM work_orders WHERE plan_id = ? ORDER BY id'))
{
    $stmt -> bind_param('i', $_POST['plan_id']);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $result_array = [];
    while($row = $result->fetch_assoc())
    {

        if($row['status'] == 'ENDED')
        {
            $row['timer_widget'] = "<span class='new badge green' data-badge-caption=''><i class='material-icons'>done</i></span>";
        }
        $result_array []= $row;
    }

    echo json_encode($result_array);
}