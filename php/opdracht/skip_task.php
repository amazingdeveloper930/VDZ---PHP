<?php

require( '../../common/connection.php');
require( '../../common/global.php');

if($_POST['contact_id']) {
    if($stmt = $con -> prepare('SELECT PTL.*, PT.chapter, PT.sort_order, PT.name FROM project_tasks_lines PTL LEFT JOIN projects_tasks PT ON PTL.projects_tasks_id = PT.id  WHERE contact_id = ? AND projects_tasks_id = ? AND status = "PROCESSING"'))
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

        $date = date_create();
        $dt = $date->format("Y-m-d");
        $stmt = $con -> prepare('UPDATE project_tasks_lines SET status = "SKIPPED", user_id = ? WHERE id = ?');
        $stmt -> bind_param('ii', $_POST['user_id'], $task_line['id']);
        $stmt -> execute();
        
        addNewLog($_POST['contact_id'], $_POST['user_id'], 'Taak overgeslagen: ' . $task_line['name']);
        ////////////////////////////


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


        $result_array = [];
        $result_array['message'] = 'Taak overgeslagen!';
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
            
            $result_array['skipped_task'] = $row;
        }

        ////////////////////////////



        $stmt = $con -> prepare('SELECT PT.*,  PTL.id AS line_id, PTL.status, PTL.contact_id, PTL.started_at, PTL.status, PTL.besteldatum, PTL.leverdatum, PTL.supplier_id, PTL.user_id, PTL.special_jaarplanning, A.username FROM projects_tasks PT LEFT JOIN project_tasks_lines PTL ON PT.id = PTL.projects_tasks_id AND PTL.contact_id = ? LEFT JOIN accounts A ON PTL.user_id = A.id WHERE PT.chapter = ? AND PT.sort_order > ? ORDER BY PT.sort_order ASC LIMIT 1');
        $stmt -> bind_param('iii', $_POST['contact_id'], $task_line['chapter'], $task_line['sort_order']);
        $stmt -> execute();
        $result = $stmt->get_result();
        

        while ($row = $result->fetch_assoc()) {
            if($row['line_id'] == null)
            {
                


                $stmt1 = $con -> prepare('INSERT INTO project_tasks_lines (contact_id, projects_tasks_id, started_at, status, besteldatum, leverdatum, supplier_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $date = date_create();
                $dtt = $date->format("Y-m-d H:i:s");
                $dt = $date->format("Y-m-d");
                $status = 'PROCESSING';
                $bdate = null;
                $ldate = null;
                $sid = null;
                // if($row['order_date'] == 'true')
                //     $bdate = $dt;
                // if($row['supply_date'] == 'true')
                //     $ldate = $dt;
                // if($row['supply_date'] == 'true')
                //     $sid = $min_sid;
                $stmt1 -> bind_param('iisssss',$_POST['contact_id'], $row['id'], $dtt, $status, $bdate, $ldate, $sid );
                $stmt1 -> execute();
                $inserted_line_id = $stmt1 -> insert_id;


                $stmt1 = $con -> prepare('SELECT PT.*, PTL.id AS line_id, PTL.contact_id, PTL.started_at, PTL.status, PTL.besteldatum, PTL.leverdatum, PTL.supplier_id, PTL.user_id, PTL.special_jaarplanning, A.username FROM projects_tasks PT LEFT JOIN project_tasks_lines PTL ON PT.id = PTL.projects_tasks_id AND PTL.contact_id = ? LEFT JOIN accounts A ON PTL.user_id = A.id WHERE PTL.id = ?');
                $stmt1->bind_param('ii', $_POST['contact_id'], $inserted_line_id);
                $stmt1->execute();
                $result1 = $stmt1->get_result();
                while($row1 = $result1 -> fetch_assoc())
                {
                    $mode = null;
                    if($row1['status'] == 'COMPLETED')
                        $mode = 2;
                    if($row1['status'] == 'PROCESSING')
                        $mode = 1;
                    if($row1['status'] == 'SKIPPED')
                        $mode = 0;
                    $row1['timer_widget'] = getTaskTimer($row1['started_at'],  $row1['timer'], $mode);

                    $stmt_new = $con->prepare('SELECT COUNT(id) AS note_count FROM projects_tasks_notes WHERE contact_id = ? AND project_tasks_id=?');
                    $stmt_new->bind_param('ii', $_POST['contact_id'], $row['id']);
                    $stmt_new->execute();
                    $result_new =  $stmt_new->get_result();
                    while($row_new = $result_new -> fetch_assoc())
                    {
                        $row1['note_count'] = $row_new['note_count'];
                    }


                    $result_array['created_task'] = $row1;
                }
                


            }
            
        }
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