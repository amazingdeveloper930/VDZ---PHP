<?php 
require 'common/sessie_check.php';
require 'common/global.php';
//3962

require( 'common/connection.php');

if($stmt = $con -> prepare('SELECT * FROM projects where contact_id != 11'))
{
    $stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$result = $stmt->get_result();

    while($row = $result ->fetch_array())
    {
        if($row['convert_date'] != null &&        
        $row['startdatum'] != null &&
        $row['opleverdatum'] != null &&        
        $row['plaatsing'] != null)
        {
            $stmt_t = $con -> prepare('SELECT * FROM projects_tasks where id >= 1226 ');
            $stmt_t -> execute();
            $result_t = $stmt_t->get_result();
            while($row_t = $result_t -> fetch_array())
            {
                $stmt1 = $con -> prepare('INSERT INTO project_tasks_lines (contact_id, projects_tasks_id, started_at, status, besteldatum, leverdatum, supplier_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $date = date_create($row['startdatum']);
                $dtt = $date->format("Y-m-d H:i:s");
                $dt = $date->format("Y-m-d");
                $status = 'PROCESSING';
                $bdate = null;
                $ldate = null;
                $sid = null;
                $stmt1 -> bind_param('iisssss',$row['contact_id'], $row_t['id'], $dtt, $status, $bdate, $ldate, $sid );
                $stmt1 -> execute();
            }
            
            
        }
    }
}

// $stmt = $con -> prepare('SELECT * FROM offerte_lines_default');
// $stmt->execute();
// $result = $stmt -> get_result();

// while($row = $result -> fetch_assoc())
// {
//     if($row['line_am_option'] == 'ja')
//     {
//         $stmt_1 = $con -> prepare("INSERT INTO offerte_arbeids_default (arbeid_title, quantity, chapter_id, line_id, sort_order) VALUES (?, ?, ?, ?, 0) ");
//         $title = "";
//         $quantity = $row['arbeid'];
//         $stmt_1 -> bind_param('ssii', $title, $quantity, $row['chapter_id'], $row['id']);
//         $stmt_1 -> execute();
//     }
    
// }