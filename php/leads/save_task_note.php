<?php

require( '../../common/connection.php');
require( '../../common/global.php');

if($_POST['contact_id']) {
    if($stmt = $con -> prepare("INSERT INTO projects_tasks_notes (contact_id, project_tasks_id, created_at, data, user_id) VALUES (?, ?, ?, ?, ?)"))
    {
        $date = date_create();
        $dtt = $date->format("Y-m-d H:i:s");
        $stmt -> bind_param('iissi', $_POST['contact_id'], $_POST['project_tasks_id'], $dtt, $_POST['data'], $_POST['user_id']);
        $stmt -> execute();
        $id = $stmt -> insert_id;

        $stmt = $con -> prepare("SELECT PTN.*, A.username FROM projects_tasks_notes PTN LEFT JOIN accounts A ON PTN.user_id = A.id WHERE PTN.id = ?");
        $stmt -> bind_param('i', $id);
        $stmt -> execute();
        $result = $stmt->get_result();
        $result_array = [];
        while ($row = $result->fetch_assoc()) {
            $dt = new DateTime($row['created_at']);
            $row['created_at']  =   $dt->format('d-m-Y') . " " . $dt->format('H:i');  
            
            $result_array = $row;
        }
        echo json_encode(["message" => 'Notitie opgeslagen.', "data" => $result_array]);
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