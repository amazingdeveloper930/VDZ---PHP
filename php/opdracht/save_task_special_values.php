<?php

require( '../../common/connection.php');
require( '../../common/global.php');
if($_POST['contact_id']) {
    $stmt = $con -> prepare('SELECT * FROM project_tasks_special_lines WHERE contact_id = ? AND projects_tasks_id = ?');
    $stmt -> bind_param('ss', $_POST['contact_id'], $_POST['projects_tasks_id']);
    $stmt -> execute();
    $result = $stmt->get_result();
    $existing = false;
    while($row = $result -> fetch_assoc())
    {
        $existing = true;
    }
    if(!$existing)
    {
        $stmt = $con -> prepare("INSERT INTO project_tasks_special_lines (contact_id, projects_tasks_id) VALUES (?, ?)");
        $stmt -> bind_param('ss', $_POST['contact_id'], $_POST['projects_tasks_id']);
        $stmt -> execute();
    }

    $stmt = $con -> prepare('UPDATE project_tasks_special_lines SET price = ?, price_inc = ?, betaaldatum = ?, factuurnummer = ? WHERE contact_id = ? AND projects_tasks_id = ?');

    $date = date_create($_POST['betaaldatum']);
    $dt = $date->format("Y-m-d");
    if($_POST['betaaldatum'] == null)
    $dt = null;
    $stmt -> bind_param('ssssss', $_POST['price'], $_POST['price_inc'], $dt, $_POST['factuurnummer'], $_POST['contact_id'], $_POST['projects_tasks_id']);
    $stmt -> execute();
    echo json_encode(['message' => 'Opdracht opgeslagen.']);
}