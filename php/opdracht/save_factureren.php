<?php

require('../../common/connection.php');

$stmt = $con -> prepare('UPDATE project_tasks_lines SET facturenren = ? WHERE contact_id = ? AND projects_tasks_id = ?');

$stmt -> bind_param('iii', $_POST['facturenren'], $_POST['contact_id'], $_POST['invoice_task_id']);
$stmt -> execute();

echo 'Contact saved.';