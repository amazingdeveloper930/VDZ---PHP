<?php

require( '../../common/connection.php');
require( '../../common/global.php');
require ('../../common/sessie_check.php');

if($_POST['contact_id']) {
    if($stmt = $con -> prepare('SELECT PTL.*, PT.chapter, PT.sort_order, PT.order_date, PT.supplier, PT.supply_date, PT.timer, P.startdatum FROM project_tasks_lines PTL LEFT JOIN projects_tasks PT ON PTL.projects_tasks_id = PT.id  LEFT JOIN projects P ON PTL.contact_id = P.contact_id WHERE PTL.contact_id = ? AND PTL.projects_tasks_id = ? AND ( PTL.status = "SKIPPED" OR  PTL.status = "COMPLETED") '))
    {
        $stmt -> bind_param('ii', $_POST['contact_id'], $_POST['projects_tasks_id']);
        $stmt -> execute();
        $result = $stmt->get_result();
        $task_line = null;
        while ($row = $result->fetch_assoc()) {
            $task_line = $row;
        }
        if($task_line == null)
        {
            echo json_encode(['message' => 'Database fout!']);
            return;
        }

        $min_sid = null;
        $stmt_sup = $con -> prepare('SELECT * FROM suppliers ORDER BY name ASC');
        $stmt_sup -> execute();
        $result_sup = $stmt_sup->get_result();
        $suppliers = [];
        while($row_sup = $result_sup -> fetch_assoc())
        {
            $min_sid = $row_sup['id'];
            $suppliers []= $row_sup;
        }
        if(count($suppliers) > 0)
            $min_sid = $suppliers[0]['id'];


        $dtt = null;
        if($task_line['timer'] != 0)
        {
            $date = date_create();
            $dtt = $date->format("Y-m-d H:i:s");
            $dt = $date->format("Y-m-d");
        }
        else{
            $date = date_create($task_line['startdatum']);
            $dtt = $date->format("Y-m-d H:i:s");
            $dt = $date->format("Y-m-d");
        }
        

        $bdate = null;
        $ldate = null;
        $sid = null;
        $factuurnummer = null;
        // if($task_line['order_date'] == 'true')
        //     $bdate = $dt;
        // if($task_line['supply_date'] == 'true')
        //     $ldate = $dt;





        // $stmt = $con -> prepare('UPDATE project_tasks_lines SET status = "PROCESSING", user_id = null,  started_at = ?, besteldatum = ?, leverdatum = ?, supplier_id = null  WHERE id = ?');
        // $stmt -> bind_param('sssi', $dtt, $bdate, $ldate, $task_line['id']);
        // $stmt -> execute();

        $stmt = $con -> prepare('UPDATE project_tasks_lines SET status = "PROCESSING", user_id = null  WHERE id = ?');
        $stmt -> bind_param('i', $task_line['id']);
        $stmt -> execute();

        addNewLog($_POST['contact_id'], $_SESSION['id'], 'Taak gereset: ' . $task_line['name']);
        
        ////////////////////////////


        

        // $stmt = $con -> prepare('UPDATE project_tasks_special_lines SET  price = ?, price_inc = ?, betaaldatum = ?, factuurnummer = null WHERE contact_id = ? AND projects_tasks_id = ?');
        // $stmt -> bind_param('sssii', $bdate, $bdate, $bdate, $_POST['contact_id'], $_POST['projects_tasks_id']);
        // $stmt -> execute();


        $result_array = [];
        $result_array['message'] = 'Taak gereset!';
        $result_array['suppliers'] = $suppliers;


        $stmt = $con -> prepare('SELECT PT.*, PTL.id AS line_id, PTL.contact_id, PTL.started_at, PTL.status, PTL.besteldatum, PTL.leverdatum, PTL.supplier_id, PTL.user_id, PTL.special_jaarplanning, A.username, PTSL.factuurnummer, PTSL.price, PTSL.price_inc, PTSL.betaaldatum FROM projects_tasks PT LEFT JOIN project_tasks_lines PTL ON PT.id = PTL.projects_tasks_id AND PTL.contact_id = ? LEFT JOIN accounts A ON PTL.user_id = A.id LEFT JOIN project_tasks_special_lines PTSL ON PT.id = PTSL.projects_tasks_id AND PTSL.contact_id = PTL.contact_id WHERE PTL.id = ?');
        $stmt->bind_param('ii', $_POST['contact_id'], $task_line['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result -> fetch_assoc())
        {
            $mode = null;
            if($row['status'] == 'COMPLETED')
                $mode = 2;
            if($row['status'] == 'PROCESSING')
                $mode = 1;
            if($row['status'] == 'SKIPPED')
                $mode = 0;
            if(isset($row['status']) && $row['isspecial_task'] == 1 && $row['id'] == 48)
                $mode = 3;
            $row['timer_widget'] = getTaskTimer($row['started_at'],  $row['timer'], $mode);

            $stmt_new = $con->prepare('SELECT COUNT(id) AS note_count FROM projects_tasks_notes WHERE contact_id = ? AND project_tasks_id=?');
            $stmt_new->bind_param('ii', $_POST['contact_id'], $row['id']);
            $stmt_new->execute();
            $result_new =  $stmt_new->get_result();
            while($row_new = $result_new -> fetch_assoc())
            {
                $row['note_count'] = $row_new['note_count'];
            }
            

            $result_array['restarted_task'] = $row;
        }

        ////////////////////////////
        echo json_encode($result_array);


        
            
    }

    else{
        echo json_encode(['message' => 'Database fout!']);
    }
    $con -> close();
}
else{
    
}
?>