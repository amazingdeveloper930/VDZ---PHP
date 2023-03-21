<?php

require( '../../common/connection.php');
require( '../../common/global.php');

if($_POST['custom_contact_id'])
{
    $result_array = [];

    $sort_order = 0;
    $stmt = $con -> prepare('SELECT MAX(sort_order) AS MAXORDER FROM projects_tasks WHERE chapter = ?');
    $stmt -> bind_param('i', $_POST['chapter']);
    $stmt -> execute();
    $result = $stmt -> get_result();
    while($row = $result -> fetch_assoc()){
        $sort_order = $row['MAXORDER'];
    }
    $sort_order ++;



    if($_POST['isspecial_task'])
    {
        
        $name = $_POST['name'] . " verstuurd";
        $is_invoice = 1;
        $stmt = $con -> prepare('INSERT INTO projects_tasks (name, sort_order, chapter, not_necessary, supplier, order_date, supply_date, isspecial_task, timer, custom_contact_id, is_invoice) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        
        $stmt -> bind_param('siissssiiii', $name, $sort_order, $_POST['chapter'], $_POST['not_necessary'], $_POST['supplier'], $_POST['order_date'], $_POST['supply_date'], $_POST['isspecial_task'],$_POST['timer'], $_POST['custom_contact_id'], $is_invoice);
        $stmt -> execute();
        $sort_order ++;
        $inserted_ids = [];
        $invoice_id = $stmt -> insert_id;
        $inserted_ids []= $invoice_id;
        $is_invoice = 0;
        $name = $_POST['name'] . " betaald";
        $stmt = $con -> prepare('INSERT INTO projects_tasks (name, sort_order, chapter, not_necessary, supplier, order_date, supply_date, isspecial_task, timer, custom_contact_id, is_invoice, invoice_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt -> bind_param('siissssiiiii', $name, $sort_order, $_POST['chapter'], $_POST['not_necessary'], $_POST['supplier'], $_POST['order_date'], $_POST['supply_date'], $_POST['isspecial_task'],$_POST['timer'], $_POST['custom_contact_id'], $is_invoice, $invoice_id);
        $stmt -> execute();
        $pay_id = $stmt -> insert_id;
        $inserted_ids []= $pay_id;
        $stmt = $con -> prepare('SELECT * FROM projects WHERE contact_id = ?');
        $stmt -> bind_param('i', $_POST['custom_contact_id']);
        $stmt -> execute();
        $result = $stmt -> get_result();
        while($row = $result -> fetch_assoc()){
            if( $row['convert_date'] != null &&        
                $row['startdatum'] != null &&
                $row['opleverdatum'] != null &&        
                $row['plaatsing'] != null)
            {
                $stmt = $con -> prepare('INSERT INTO project_tasks_lines (contact_id, projects_tasks_id, started_at, status) VALUES (?, ?, ?, ?)');
                $date = date_create();
                $dt = $date->format("Y-m-d");
                $statue =  'PROCESSING';
                $stmt -> bind_param('iiss', $_POST['custom_contact_id'], $invoice_id, $dt, $statue);
                $stmt -> execute();

                $stmt = $con -> prepare('INSERT INTO project_tasks_lines (contact_id, projects_tasks_id, started_at, status) VALUES (?, ?, ?, ?)');
                $stmt -> bind_param('iiss', $_POST['custom_contact_id'], $pay_id, $dt, $statue);
                $stmt -> execute();

                addNewLog($_POST['custom_contact_id'], $_POST['userid'], 'Taak toegevoegd: ' . $_POST['name']);

            }
        }

        $result_array['created_task'] = [];
        foreach($inserted_ids as $inserted_id)
        {
            $stmt1 = $con -> prepare('SELECT PT.*, PTL.id AS line_id, PTL.contact_id, PTL.started_at, PTL.status, PTL.besteldatum, PTL.leverdatum, PTL.supplier_id, PTL.user_id, PTL.special_jaarplanning, A.username FROM projects_tasks PT LEFT JOIN project_tasks_lines PTL ON PT.id = PTL.projects_tasks_id AND PTL.contact_id = ? LEFT JOIN accounts A ON PTL.user_id = A.id WHERE PT.id = ?');
            $stmt1->bind_param('ii', $_POST['custom_contact_id'], $inserted_id);
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

                $result_array['created_task'] []= $row1;
            }
        }
        $result_array['message'] = 'new task created';


    }
    else{
        $date = date_create();
        $dt = $date->format("Y-m-d");
        
        $stmt = $con -> prepare('INSERT INTO projects_tasks (name, sort_order, chapter, not_necessary, supplier, order_date, supply_date, isspecial_task, timer, custom_contact_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt -> bind_param('siissssiii', $_POST['name'], $sort_order, $_POST['chapter'], $_POST['not_necessary'], $_POST['supplier'], $_POST['order_date'], $_POST['supply_date'], $_POST['isspecial_task'],$_POST['timer'], $_POST['custom_contact_id']);
        $stmt -> execute();
        $insert_id = $stmt -> insert_id;

        addNewLog($_POST['custom_contact_id'], $_POST['userid'], 'Taak toegevoegd: ' . $_POST['name']);


        $stmt = $con -> prepare('SELECT * FROM projects WHERE contact_id = ?');
        $stmt -> bind_param('i', $_POST['custom_contact_id']);
        $stmt -> execute();
        $result = $stmt -> get_result();
        while($row = $result -> fetch_assoc()){
            if( $row['convert_date'] != null &&        
                $row['startdatum'] != null &&
                $row['opleverdatum'] != null &&        
                $row['plaatsing'] != null)
            {
                $stmt = $con -> prepare('INSERT INTO project_tasks_lines (contact_id, projects_tasks_id, started_at, status) VALUES (?, ?, ?, ?)');
                $date = date_create();
                $dt = $date->format("Y-m-d");
                $statue =  'PROCESSING';
                $stmt -> bind_param('iiss', $_POST['custom_contact_id'], $insert_id, $dt, $statue);
                $stmt -> execute();
                $inserted_line_id = $stmt -> insert_id;           

            }
        }
        $stmt1 = $con -> prepare('SELECT PT.*, PTL.id AS line_id, PTL.contact_id, PTL.started_at, PTL.status, PTL.besteldatum, PTL.leverdatum, PTL.supplier_id, PTL.user_id, PTL.special_jaarplanning, A.username FROM projects_tasks PT LEFT JOIN project_tasks_lines PTL ON PT.id = PTL.projects_tasks_id AND PTL.contact_id = ? LEFT JOIN accounts A ON PTL.user_id = A.id WHERE PT.id = ?');
                $stmt1->bind_param('ii', $_POST['custom_contact_id'], $insert_id);
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

                $result_array['message'] = 'new task created';
        
    }

    if ($stmt = $con->prepare('SELECT * FROM suppliers ORDER BY name ASC')) {

        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $result = $stmt->get_result();
        
    }
    $task_suppliers = [];
    while ($row = $result->fetch_assoc()) {
        $task_suppliers[] = $row;
    }
    $result_array['supplier'] = $task_suppliers;
    echo json_encode($result_array);
    
}