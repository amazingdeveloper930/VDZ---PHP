<?php

require('../../common/connection.php');

if($_POST['plan_id'] == '') { 
    echo json_encode(['message' => 'Database fout!']);
}
else{
    $plan_id = $_POST['plan_id'];
    $stmt = $con -> prepare('SELECT * FROM werkplanning_medewerker WHERE id = ?');
    $stmt -> bind_param('i', $plan_id);
    $stmt -> execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if( $row['parent_plan'] != null)
            $plan_id = $row['parent_plan'];
    }
    $stmt = $con -> prepare('INSERT INTO work_orders (plan_id, description, material, tool) VALUES (?, ?, ?, ?) ');
    $stmt -> bind_param('isss', $plan_id, $_POST['description'], $_POST['material'], $_POST['tool']);
    $stmt -> execute();
    $inserted_id = $stmt -> insert_id;
    $stmt = $con -> prepare('SELECT * FROM work_orders WHERE id = ?');
    $stmt -> bind_param('i', $inserted_id);
    $stmt -> execute();
    $result = $stmt->get_result();
    $result_array = [];
    while ($row = $result->fetch_assoc()) {
        if($row['status'] == 'ENDED')
        {
            $row['timer_widget'] = "<span class='new badge green' data-badge-caption=''><i class='material-icons'>done</i></span>";
        }
        $result_array []= $row;    
    }
    echo json_encode(['message' => 'Werkorder opgeslagen.', 'item' => $result_array]);
}