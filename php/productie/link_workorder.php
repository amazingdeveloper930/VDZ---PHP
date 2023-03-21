<?php

require( '../../common/connection.php');
require( '../../common/global.php');

if($_POST['parent'])
{
    $stmt = $con -> prepare("SELECT * FROM werkplanning_medewerker WHERE parent_plan is NULL AND id = ?");
    $stmt -> bind_param('i', $_POST['parent']);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $flag = false;
    $result_array = [];
    while($row = $result->fetch_assoc())
    {
        $flag = true;
    }
    if(!$flag)
    {
        $result_array['message'] = 'There is no such parent.';
    }
    else{
        $stmt = $con -> prepare("UPDATE werkplanning_medewerker SET parent_plan = ? WHERE id = ?");
        $stmt -> bind_param('ii', $_POST['parent'], $_POST['plan_id']);
        $stmt -> execute();

        $stmt = $con -> prepare("SELECT * FROM work_orders WHERE plan_id = ?");
        $stmt -> bind_param('i', $_POST['parent']);
        $stmt -> execute();
        $result = $stmt -> get_result();
        $orders = [];
        while($row = $result->fetch_assoc())
        {
            if($row['status'] == 'ENDED')
        {
            $row['timer_widget'] = "<span class='new badge green' data-badge-caption=''><i class='material-icons'>done</i></span>";
        }
            $orders []= $row;
        }
        $result_array['items'] = $orders;
        $result_array['message'] = 'Werkorder linked.';
       
    }
    echo json_encode($result_array);
}