<?php

require( '../../common/connection.php');
require( '../../common/global.php');

$result = null;
$result_array = [];
$date = date_create($_POST['date']);
$dt_mon = $date->format("Y-m-d");
if($_POST['plan_id'] != '')
{

    $stmt = $con -> prepare('SELECT WM.*, C.name, C.address, P.project_number, PT.title AS ticket_title, WB.datum, WB.datum_end, PP.color FROM werkplanning_medewerker WM JOIN contacts C ON WM.contact_id = C.id JOIN projects P ON WM.contact_id = P.contact_id LEFT JOIN  project_planning PP ON WM.contact_id = PP.contact_id AND WM.project_planning_id = PP.id LEFT JOIN projects_tickets PT ON WM.ticket_id = PT.id LEFT JOIN werkplanning_block WB ON WM.block_id = WB.id WHERE WM.id = ?');

    $stmt -> bind_param('i', $_POST['plan_id']);
    $stmt -> execute();
    $result = $stmt->get_result();
}
else{


    $date_sat = strtotime("+6 day", strtotime($_POST['date']));
    $dt_sat = date('Y-m-d', $date_sat);

    $stmt = $con -> prepare('SELECT WM.*, C.name, C.address, P.project_number, PT.title AS ticket_title, PT.status AS ticket_status, WB.datum, WB.datum_end, PP.color FROM werkplanning_medewerker WM JOIN contacts C ON WM.contact_id = C.id JOIN projects P ON WM.contact_id = P.contact_id LEFT JOIN  project_planning PP ON WM.contact_id = PP.contact_id AND WM.project_planning_id = PP.id LEFT JOIN projects_tickets PT ON WM.ticket_id = PT.id LEFT JOIN werkplanning_block WB ON WM.block_id = WB.id WHERE (WB.datum >= ? AND WB.datum <= ?) OR (WM.daypart = "Meerdere dagen" AND (WB.datum <= ? AND WB.datum_end >= ?)) ORDER BY WM.employee_id, FIELD(WM.daypart, "Middag", "Ochtend", "Heledag")');

    $stmt -> bind_param('ssss', $dt_mon, $dt_sat,  $dt_mon, $dt_mon);
    $stmt -> execute();
    $result = $stmt->get_result();
}

$parent_plan = 0;
while ($row = $result->fetch_assoc()) {
    $date = date_create($row['datum']);
    if($row['daypart'] == 'Meerdere dagen')
        {
            if($dt_mon > $row['datum'])
                $date = date_create($dt_mon);
        }
    $text_arr = [];
    $stmt_1 = $con -> prepare('SELECT * FROM werkplanning_activity WHERE block_id = ? ORDER BY id');
    $stmt_1 -> bind_param('i', $row['block_id']);
    $stmt_1 -> execute();
    $result_1 = $stmt_1->get_result();
    while($row_1 = $result_1 -> fetch_assoc())
    {
        $text_arr []= $row_1['text'];
    }
    $row['text'] = join(",", $text_arr);
    $row['w'] = $date->format("w");
    $parent_plan = $row['parent_plan'];
    $result_array[] = $row;    
}

$result_array_1 = [];

if($stmt = $con -> prepare('SELECT * FROM work_orders WHERE plan_id = ? ORDER BY id'))
{   
    if($parent_plan == 0)
        $stmt -> bind_param('i', $_POST['plan_id']);
    else
        $stmt -> bind_param('i', $parent_plan);
    $stmt -> execute();
    $result = $stmt -> get_result();
    
    while($row = $result->fetch_assoc())
    {
        
        if($row['status'] == 'ENDED')
        {
            $row['timer_widget'] = "<span class='new badge green' data-badge-caption=''><i class='material-icons'>done</i></span>";
        }
        $result_array_1 []= $row;
    }

}

$result_array_2 = [];
if($stmt = $con -> prepare('SELECT WM.id FROM werkplanning_medewerker WM WHERE WM.parent_plan = ? ORDER BY WM.id'))
{
    $stmt -> bind_param('i', $_POST['plan_id']);
    $stmt -> execute();
    $result = $stmt -> get_result();
    while($row = $result->fetch_assoc())
    {
        $result_array_2 []= $row['id'];
    }
}

echo json_encode(['week_plan_item' => $result_array, 'workorder_item' => $result_array_1, 'children_item' => $result_array_2 ]);