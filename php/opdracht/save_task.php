<?php

require( '../../common/connection.php');
require( '../../common/global.php');

if($_POST['contact_id']) {
    if($stmt = $con -> prepare('UPDATE project_tasks_lines SET supplier_id = ?, besteldatum = ?, leverdatum = ?, special_jaarplanning = ? WHERE contact_id = ? AND projects_tasks_id = ? AND status = "PROCESSING"'))
    {
        $stmt ->  bind_param("isssii", $_POST['supplier_id'], $_POST['besteldatum'], $_POST['leverdatum'], $_POST['special_jaarplanning'], $_POST['contact_id'], $_POST['projects_tasks_id']);
        $stmt -> execute();
        echo json_encode(["message" => 'Opdracht opgeslagen.']);
        $stmt->close();
    }
    else{
        echo json_encode(["message" => 'Database fout!']);
    }
    $con -> close();
}
else{
    
}
?>