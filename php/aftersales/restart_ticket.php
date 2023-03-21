<?php

require( '../../common/connection.php');
require( '../../common/global.php');

if($_POST['ticket_id'])
{
    $stmt = $con -> prepare('UPDATE projects_tickets SET status = "OPENED", user = ? WHERE id = ?');
    $user = null;
    $stmt -> bind_param('si', $user, $_POST['ticket_id']);
    $stmt -> execute();


    $stmt = $con -> prepare('SELECT PT.*, E.name, A.username From projects_tickets PT LEFT JOIN employees E ON PT.employee = E.id LEFT JOIN accounts A ON PT.user = A.id WHERE PT.id = ? ORDER BY PT.id');
    $stmt -> bind_param('i', $_POST['ticket_id']);
    $stmt -> execute();
    $result = $stmt->get_result();
    $result_array = [];
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
    echo json_encode(['message' => 'Ticket sluiten.', 'item' => $result_array]);
}
else
{
    echo json_encode(['message' => 'Database fout!']);
}