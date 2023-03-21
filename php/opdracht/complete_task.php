<?php

require( '../../common/connection.php');
require( '../../common/global.php');

$spec_task_id = '';
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

        $stmt = $con -> prepare('UPDATE project_tasks_lines SET status = "COMPLETED", user_id = ? WHERE id = ?');
        $stmt -> bind_param('ii', $_POST['user_id'], $task_line['id']);
        $stmt -> execute();

        addNewLog($_POST['contact_id'], $_POST['user_id'], 'Taak afgerond: ' . $task_line['name']);

        $stmt = $con -> prepare('UPDATE projects SET last_completed_task = ? WHERE contact_id = ? ');
        $stmt -> bind_param('ii', $_POST['projects_tasks_id'], $_POST['contact_id']);
        $stmt -> execute();

        $stmt = $con -> prepare('SELECT * FROM project_tasks_lines WHERE contact_id = ? AND  projects_tasks_id = ?');
        $new_spec_row = 0;
        if($_POST['projects_tasks_id'] == 1)
        {
            $new_spec_row = 45;
            
        }
        if($_POST['projects_tasks_id'] == 2)
        {
            $new_spec_row = 46;
        }
        if($_POST['projects_tasks_id'] == 3)
        {
            $new_spec_row = 47;
        }
        $special_task_exisiting = false;
        if($_POST['projects_tasks_id'] == 1 || $_POST['projects_tasks_id'] == 2 || $_POST['projects_tasks_id'] == 3)
        {
            $stmt -> bind_param('ii', $_POST['contact_id'], $new_spec_row);
            $stmt -> execute();
            $result_spec = $stmt -> get_result();
            while($row_spec = $result_spec -> fetch_assoc())
            {
                $special_task_exisiting = true;
            }





            // if(! $special_task_exisiting)
            // {
            //     $stmt = $con -> prepare('INSERT INTO project_tasks_lines (contact_id, projects_tasks_id, started_at, status) VALUES (?, ?, ?, "PROCESSING")');
                
            //     $date = date_create();
            //     $dt = $date->format("Y-m-d");
            //     $pt_id = $_POST['projects_tasks_id'] + 44
            //     ;
            //     $stmt -> bind_param('iis', $_POST['contact_id'], $pt_id, $dt);
            //     $stmt -> execute();
            //     $spec_task_id = $stmt -> insert_id;
            //     $stmt = $con -> prepare("INSERT INTO project_tasks_special_lines (contact_id, projects_tasks_id) VALUES (?, ?)");
            //     $stmt -> bind_param('ii', $_POST['contact_id'], $pt_id);
            //     $stmt -> execute();
                
            // }
        }
        /*
        if($_POST['projects_tasks_id'] == 45 || $_POST['projects_tasks_id'] == 46 || $_POST['projects_tasks_id'] == 47)
        {



            $stmt = $con -> prepare('SELECT * FROM project_tasks_lines WHERE contact_id = ? AND  projects_tasks_id = ?');
            $new_spec_row = 48;
            $stmt -> bind_param('ii', $_POST['contact_id'], $new_spec_row);
            $stmt -> execute();
            $result_spec = $stmt->get_result();
            $special_task_exisiting = false;
            while($row_spec = $result_spec -> fetch_assoc())
            {
                $special_task_exisiting = true;
            }

            if(! $special_task_exisiting)
            {
                // $stmt = $con -> prepare("SELECT * FROM project_tasks_lines WHERE contact_id = ? AND projects_tasks_id IN (45, 46, 47) AND status = 'COMPLETED'");
                // $stmt -> bind_param('i', $_POST['contact_id']);
                // $stmt -> execute();
                // $result_spec = $stmt->get_result();

                // $temp_count = 0;
                // while($row_spec = $result_spec -> fetch_assoc())
                // {
                //     $temp_count ++;
                // }
                // if($temp_count == 3)
                // {
                //     $stmt = $con -> prepare('INSERT INTO project_tasks_lines (contact_id, projects_tasks_id, started_at, status) VALUES (?, ?, ?, "PROCESSING")');
                
                //     $date = date_create();
                //     $dt = $date->format("Y-m-d");
                //     $pt_id = 48;
                //     $stmt -> bind_param('iis', $_POST['contact_id'], $pt_id, $dt);
                //     $stmt -> execute();
                //     $spec_task_id = $stmt -> insert_id;
                //     $stmt = $con -> prepare("INSERT INTO project_tasks_special_lines (contact_id, projects_tasks_id) VALUES (?, ?)");
                //     $stmt -> bind_param('ii', $_POST['contact_id'], $pt_id);
                //     $stmt -> execute();
                // }
                
            }

        }
        */
        

        // $stmt = $con -> prepare('SELECT * FROM project_tasks_lines WHERE contact_id = ? AND projects_tasks_id IN (1, 14, 15, 16)');
        // $stmt -> bind_param('i', $_POST['contact_id']);
        // $stmt -> execute();
        // $result_temp = $stmt->get_result();
        // $temp_row_count = 0;
        // while($row_temp = $result_temp -> fetch_assoc())
        // {
        //     if($row_temp['status'] == 'COMPLETED')
        //         $temp_row_count ++;
        // }

        // if($temp_row_count == 4)
        // {
        //     $stmt = $con -> prepare('UPDATE projects SET p_status = 1, m_status1 = "NO", m_status2 = "NO", m_status3 = "NO" WHERE contact_id = ? AND p_status != 1');
        //     $stmt -> bind_param('i', $_POST['contact_id']);
        //     $stmt -> execute();
        // }
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
        $result_array['message'] = 'Taak afgerond!';
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

            if($row['isspecial_task'] && ($row['is_invoice'] == 0))
            {
                $stmt_invoice = $con -> prepare("SELECT * FROM project_tasks_special_lines WHERE contact_id = ? AND projects_tasks_id = ?");
                $stmt_invoice -> bind_param('ss', $_POST['contact_id'], $row['invoice_id']);
                $stmt_invoice -> execute();
                $result_invoice = $stmt_invoice->get_result();
                while($row_invoice = $result_invoice -> fetch_assoc())
                {
                    if($row_invoice['price_inc'] != $row['price_inc'])
                        $mode = 1;
                }
            }
            // if($row['id'] >= 45 && $row['id'] <= 47 && $mode == 2)
            // {
            //     $invoice_task_id = 1;
            //     if($row['id'] == 46)
            //         $invoice_task_id = 2;
            //     if($row['id'] == 47)
            //         $invoice_task_id = 3;
            //     $stmt_invoice = $con -> prepare("SELECT * FROM project_tasks_special_lines WHERE contact_id = ? AND projects_tasks_id = ?");
            //     $stmt_invoice -> bind_param('ss', $_POST['contact_id'], $invoice_task_id);
            //     $stmt_invoice -> execute();
            //     $result_invoice = $stmt_invoice->get_result();
            //     while($row_invoice = $result_invoice -> fetch_assoc())
            //     {
            //         if($row_invoice['price_inc'] != $row['price_inc'])
            //             $mode = 1;
            //     }
            // }

            $row['timer_widget'] = getTaskTimer($row['started_at'],  $row['timer'], $mode);


            $stmt_new = $con->prepare('SELECT COUNT(id) AS note_count FROM projects_tasks_notes WHERE contact_id = ? AND project_tasks_id=?');
            $stmt_new->bind_param('ii', $_POST['contact_id'], $row['id']);
            $stmt_new->execute();
            $result_new =  $stmt_new->get_result();
            while($row_new = $result_new -> fetch_assoc())
            {
                $row['note_count'] = $row_new['note_count'];
            }


            $result_array['completed_task'] = $row;
        }

        ////////////////////////////


        // $stmt = $con -> prepare('SELECT PT.*,  PTL.id AS line_id, PTL.status, PTL.contact_id, PTL.started_at, PTL.status, PTL.besteldatum, PTL.leverdatum, PTL.supplier_id, PTL.user_id, A.username FROM projects_tasks PT LEFT JOIN project_tasks_lines PTL ON PT.id = PTL.projects_tasks_id AND PTL.contact_id = ? LEFT JOIN accounts A ON PTL.user_id = A.id WHERE PT.chapter = ? AND PT.sort_order > ? AND (PT.id < 45 OR PT.id > 48) ORDER BY PT.sort_order ASC LIMIT 1');
        $stmt = $con -> prepare('SELECT PT.*,  PTL.id AS line_id, PTL.status, PTL.contact_id, PTL.started_at, PTL.status, PTL.besteldatum, PTL.leverdatum, PTL.supplier_id, PTL.user_id, A.username FROM projects_tasks PT LEFT JOIN project_tasks_lines PTL ON PT.id = PTL.projects_tasks_id AND PTL.contact_id = ? LEFT JOIN accounts A ON PTL.user_id = A.id WHERE PT.chapter = ? AND PT.sort_order > ? ORDER BY PT.sort_order ASC LIMIT 1');

        $stmt -> bind_param('iii', $_POST['contact_id'], $task_line['chapter'], $task_line['sort_order']);
        $stmt -> execute();
        $result = $stmt->get_result();
        

        while ($row = $result->fetch_assoc()) {
            if($row['line_id'] == null)
            {
                


                $stmt1 = $con -> prepare('INSERT INTO project_tasks_lines (contact_id, projects_tasks_id, started_at, status, besteldatum, leverdatum, supplier_id, special_jaarplanning) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
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

                $special_jaarplanning = '';
                if($row['name'] == 'Afspraak architect' ||                   
                  $row['name'] == 'Betonplaat' || 
                  $row['name'] == 'Kozijnen' || 
                  $row['name'] == 'Gevelbekleding'
                )
                  $special_jaarplanning = 'YES';

                $stmt1 -> bind_param('iissssss',$_POST['contact_id'], $row['id'], $dtt, $status, $bdate, $ldate, $sid, $special_jaarplanning );
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
        if($spec_task_id != '')
        {
            $stmt1 = $con -> prepare('SELECT PT.*, PTL.id AS line_id, PTL.contact_id, PTL.started_at, PTL.status, PTL.besteldatum, PTL.leverdatum, PTL.supplier_id, PTL.user_id, PTL.special_jaarplanning, A.username FROM projects_tasks PT LEFT JOIN project_tasks_lines PTL ON PT.id = PTL.projects_tasks_id AND PTL.contact_id = ? LEFT JOIN accounts A ON PTL.user_id = A.id WHERE PTL.id = ?');
                $stmt1->bind_param('ii', $_POST['contact_id'], $spec_task_id);
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
                    // if(isset($row1['status']) && $row1['isspecial_task'] == 1 && $row1['id'] == 48)
                    //     $mode = 3;
                    
                    
                        
                    $row1['timer_widget'] = getTaskTimer($row1['started_at'],  $row1['timer'], $mode);


                    $stmt_new = $con->prepare('SELECT COUNT(id) AS note_count FROM projects_tasks_notes WHERE contact_id = ? AND project_tasks_id=?');
                    $stmt_new->bind_param('ii', $_POST['contact_id'], $row1['id']);
                    $stmt_new->execute();
                    $result_new =  $stmt_new->get_result();
                    while($row_new = $result_new -> fetch_assoc())
                    {
                        $row1['note_count'] = $row_new['note_count'];
                    }




                    $result_array['created_special_task'] = $row1;
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