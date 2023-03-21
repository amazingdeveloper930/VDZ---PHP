<?php

require('../../common/connection.php');
require( '../../common/global.php');
require ('../../common/sessie_check.php');


$plan_id = $_POST['plan_id'];
$stmt = $con -> prepare('SELECT * FROM werkplanning_medewerker WHERE id = ?');
$stmt -> bind_param('i', $plan_id);
$stmt -> execute();
$result = $stmt -> get_result();
$block_id = NULL;
$day = date("w", strtotime($_POST['datum']));


while($row = $result->fetch_assoc())
{
    $block_id = $row['block_id'];

    $last = date_create($_POST['datum']);
    date_add($last, date_interval_create_from_date_string(($row['hour'] * 3) . " hours"));
    $last = $last->format("Y-m-d");

    $stmt_1 = $con -> prepare('UPDATE werkplanning_block SET datum = ?, datum_end = ?, day = ? WHERE id = ?');
    $stmt_1 -> bind_param('ssii', $_POST['datum'], $last, $day, $block_id);
    $stmt_1 -> execute();

    $stmt_1 = $con -> prepare("UPDATE werkplanning_medewerker SET employee_id = ? WHERE id = ?");
    $stmt_1 -> bind_param('ii', $_POST['employee_id'], $row['id']);
    $stmt_1 -> execute();
    
}

echo json_encode(['message' => 'Weekplan opgeslagen.', 'plan_id' => $plan_id]);