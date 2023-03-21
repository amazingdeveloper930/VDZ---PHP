<?php

require( '../../common/connection.php');
require( '../../common/global.php');

$stmt = $con -> prepare("UPDATE werkplanning_medewerker SET parent_plan = NULL WHERE id = ?");
$stmt -> bind_param('i', $_POST['plan_id']);
$stmt -> execute();

$stmt = $con -> prepare("SELECT * FROM work_orders WHERE plan_id = ?");
$stmt -> bind_param('i', $_POST['plan_id']);
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
$result_array['message'] = 'Werkorder delinked.';
echo json_encode($result_array);