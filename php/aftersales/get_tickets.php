<?php

require( '../../common/connection.php');
require('../../common/global.php');

$result_array = [];

if(isset($_POST['mode']) && $_POST['mode'] == 'OPENED'){
    $stmt = $con -> prepare('SELECT PT.* FROM projects_tickets PT WHERE PT.contact_id = ? AND PT.status = "OPENED" ORDER BY PT.id');
    $stmt->bind_param('s', $_POST['contactid']);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $result_array []= $row;
    }
    echo json_encode($result_array);
}
else{
    $sql = "";
    if(isset($_POST['ticket_id']))
    {
        $sql = 'SELECT PT.*, E.name, A.username From projects_tickets PT LEFT JOIN employees E ON PT.employee = E.id LEFT JOIN accounts A ON PT.user = A.id WHERE PT.id = ? ORDER BY PT.id';
    }
    else{
        $sql = 'SELECT PT.*, E.name, A.username From projects_tickets PT LEFT JOIN employees E ON PT.employee = E.id LEFT JOIN accounts A ON PT.user = A.id WHERE PT.contact_id = ? ORDER BY PT.id';
    }
    if ($stmt = $con->prepare($sql)) {
        if(isset($_POST['ticket_id']))
        {
            $stmt->bind_param('s', $_POST['ticket_id']);
        }

        if(isset($_POST['contactid']))
        {
            $stmt->bind_param('s', $_POST['contactid']);
        }
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $result = $stmt->get_result();
        
        
        
        
        
        while ($row = $result->fetch_assoc()) {

            $stmt_new = $con -> prepare('SELECT WM.employee_id, WB.datum, E.name, E.teamleader FROM werkplanning_medewerker WM LEFT JOIN werkplanning_block WB ON WM.block_id = WB.id LEFT JOIN employees E ON WM.employee_id = E.id WHERE WM.ticket_id = ?');
            $stmt_new -> bind_param('i', $row['id']);
            $stmt_new -> execute();
            $result_new =  $stmt_new->get_result();
            $row_new = $result_new -> fetch_assoc();
            if($row_new)
            {
                $row['name'] = $row_new['name'];
                $row['plan_datum'] = $row_new['datum'];
            }
            while($row_new = $result_new -> fetch_assoc())
            {
               if($row_new['teamleader'] != 0){
                    $row['name'] = $row_new['name'];
                    $row['plan_datum'] = $row_new['datum'];
               }
                

            }
        
        
            $stmt_new = $con->prepare('SELECT COUNT(id) AS note_count FROM projects_tickets_notes WHERE ticket_id = ?');
            $stmt_new->bind_param('i', $row['id']);
            $stmt_new->execute();
            $result_new =  $stmt_new->get_result();
            while($row_new = $result_new -> fetch_assoc())
            {
                $row['note_count'] = $row_new['note_count'];
            }
        
        
        
            $row['timer_widget'] = getTicketTimer($row['datum'], $row['status']);
            $result_array []= $row;
        
        }
        echo json_encode($result_array);
    }
}


?>