<?php

require( '../../common/connection.php');
require( '../../common/global.php');

$result_id = null;
if(isset($_POST['plan_id'])){
    $stmt = $con -> prepare('UPDATE project_planning SET name = ?, sort_order = ?, week = ?, color = ?, medewerkers = ?, uitbesteed = ? WHERE contact_id = ? AND id = ?');
    $stmt -> bind_param('siisisii', $_POST['name'], $_POST['sort_order'], $_POST['week'], $_POST['color'], $_POST['medewerkers'], $_POST['uitbesteed'], $_POST['contact_id'], $_POST['plan_id']);
    $stmt -> execute();
    $result_id = $_POST['plan_id'];

}
else{
    $stmt = $con -> prepare('INSERT INTO project_planning (name, sort_order, week, color, contact_id, medewerkers, uitbesteed) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt -> bind_param('siisiis', $_POST['name'], $_POST['sort_order'], $_POST['week'], $_POST['color'], $_POST['contact_id'], $_POST['medewerkers'], $_POST['uitbesteed']);
    $stmt -> execute();
    $result_id = $stmt -> insert_id;
}

$total_week = 0;
$stmt = $con -> prepare('SELECT * FROM project_planning WHERE id = ?');
$stmt -> bind_param('i', $result_id);
$stmt -> execute();
$result = $stmt -> get_result();
$result_array = [];
while($row = $result -> fetch_assoc()){
    $row['color_widget'] = '<span class="new badge pt-' . $row['color']. '" data-badge-caption=""></span>';
    $result_array = $row;
    
}

$flag = 0;
$stmt = $con -> prepare('SELECT * FROM project_planning WHERE contact_id = ?');
$stmt -> bind_param('i', $_POST['contact_id']);
$stmt -> execute();
$result = $stmt -> get_result();
while($row = $result -> fetch_assoc()){
    $total_week += $row['week'];
    $flag = 1;
}

// if($flag == 0)
// {

$stmt = $con -> prepare('SELECT * FROM projects WHERE contact_id = ?');
$stmt -> bind_param('i', $_POST['contact_id']);
$stmt -> execute();
$result = $stmt -> get_result();
$date = NULL;
while($row = $result -> fetch_assoc()){
    if(!empty($row['startdatum']))
    {
        $date = date_create($row['startdatum']);
        date_add($date,date_interval_create_from_date_string(($total_week * 7) . " days"));
    }
}
if($date != NULL)
{
    $date_string = $date->format("Y-m-d");
    $stmt = $con -> prepare('UPDATE projects SET opleverdatum = ? WHERE contact_id = ?');
    $stmt -> bind_param('si', $date_string, $_POST['contact_id']);
    $stmt -> execute(); 
}
// }
echo json_encode(['result' => $result_array, 'total_week' => $total_week]);