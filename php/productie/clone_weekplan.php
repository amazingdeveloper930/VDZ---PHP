<?php

require( '../../common/connection.php');
require( '../../common/global.php');
require ('../../common/sessie_check.php');

$plan_id = '';
$stmt = $con -> prepare("SELECT WM.*, WB.werkplanning_id, WB.contact_id, WB.year, WB.week, WB.day, WB.datum, WB.datum_end FROM werkplanning_medewerker WM LEFT JOIN werkplanning_block WB ON WM.block_id = WB.id WHERE WM.id = ?");
$stmt -> bind_param('i', $_POST['plan_id']);
$stmt -> execute();
$result = $stmt -> get_result();

while($row = $result->fetch_assoc())
{
    $medewerker_row = $row;
    $stmt_1 = $con -> prepare("INSERT INTO werkplanning_block (werkplanning_id, contact_id, year, week, day, datum, datum_end) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_1 -> bind_param("iiiiiss", $row['werkplanning_id'], $row['contact_id'], $row['year'], $row['week'], $row['day'], $row['datum'], $row['datum_end']);
    $stmt_1 -> execute();
    $block_id = $stmt_1 -> insert_id;
    
    $stmt_1 = $con -> prepare("SELECT * FROM werkplanning_activity WHERE block_id = ?");
    $stmt_1 -> bind_param("i", $row['block_id']);
    $stmt_1 -> execute();

    $result_1 = $stmt_1 -> get_result();
    while($row_1 = $result_1->fetch_assoc())
    {
        $stmt_2 = $con -> prepare("INSERT INTO werkplanning_activity (werkplanning_id, contact_id, block_id, text) VALUES (?, ?, ?, ?)");
        $stmt_2 -> bind_param("iiis", $row_1['werkplanning_id'], $row_1['contact_id'], $block_id, $row_1['text']);
        $stmt_2 -> execute();
    }

    $stmt_1 = $con -> prepare("INSERT INTO werkplanning_medewerker (contact_id, werkplanning_id, block_id, employee_id, hour, daypart, projecttype, parent_plan, project_planning_id, ticket_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_1 -> bind_param('iiiiisssii', $row['contact_id'],  $row['werkplanning_id'],  $block_id,  $_POST['employee_id'],  $row['hour'],  $row['daypart'],  $row['projecttype'], $row['parent_plan'],  $row['project_planning_id'],  $row['ticket_id']);
    $stmt_1 -> execute();
    $plan_id = $stmt_1 -> insert_id;
    
    $stmt_1 = $con -> prepare('SELECT * FROM work_orders WHERE plan_id = ?');
    $stmt_1 -> bind_param('i', $_POST['plan_id']);
    $stmt_1 -> execute();
    
    $result_1 = $stmt_1 -> get_result();

    while($row_1 = $result_1->fetch_assoc())
    {
        $stmt_2 = $con -> prepare("INSERT INTO work_orders (plan_id, description, material, tool, status) VALUES (?, ?, ?, ?, ?)");
        $stmt_2 -> bind_param('issss', $plan_id, $row_1['description'], $row_1['material'], $row_1['tool'], $row_1['status']);
        $stmt_2 -> execute();
    }

}

echo json_encode(['weekplanning_id' => $plan_id, 'message' => 'Successfully Cloned']);