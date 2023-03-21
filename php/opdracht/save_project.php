<?php

require( '../../common/connection.php');

if($_POST['contactid']) {
	$stmt = $con -> prepare('SELECT * FROM projects WHERE contact_id = ?');
  $stmt->bind_param('i', $_POST['contactid']);
  $stmt->execute(); 
  $result = $stmt->get_result();
  $temp_item = null;
  while($row = $result -> fetch_assoc())
  {
    $temp_item = $row;
  } 
  if($temp_item != null && $temp_item['plaatsing'] == 'nee' && $_POST['plaatsing'] == 'ja')
  {
    $stmt = $con->prepare('UPDATE projects SET as_status = 1, m_status4 = "NO", m_status5 = "NO", m_status6 = "NO", m_status7 = "NO", sale_date = ? WHERE contact_id = ? AND as_status != 1');

    $date = date_create();
    $dt = $date->format("Y-m-d");


    $stmt -> bind_param('si', $dt, $_POST['contactid']);
    $stmt->execute();    
  }
  if ($stmt = $con->prepare('UPDATE projects SET convert_date = ?, startdatum = ?, opleverdatum = ?, plaatsing = ? WHERE contact_id = ?')) {
    // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.

    $stmt->bind_param('ssssi', $_POST['convert_date'], $_POST['startdatum'], $_POST['opleverdatum'], $_POST['plaatsing'], $_POST['contactid']);
    $stmt->execute();    

    if($_POST['plaatsing'])
    if( $_POST['convert_date'] != null &&        
        $_POST['startdatum'] != null &&
        $_POST['opleverdatum'] != null &&        
        $_POST['plaatsing'] != null
    )
    {
        $stmt = $con -> prepare('SELECT PT1.*, PTL.id as line_id FROM  projects_tasks PT1 INNER JOIN (SELECT MIN(PT.sort_order) max_sort, PT.chapter FROM projects_tasks PT WHERE (PT.custom_contact_id is NULL OR PT.custom_contact_id = ?) GROUP By chapter) PT2 ON PT1.chapter = PT2.chapter
        LEFT JOIN (SELECT * FROM project_tasks_lines WHERE contact_id = ?) PTL ON PT1.id = PTL.projects_tasks_id
        WHERE PT1.sort_order = PT2.max_sort AND PT1.timer != 0');
        $stmt->bind_param('ii', $_POST['contactid'], $_POST['contactid']);
        $stmt -> execute();
        $result = $stmt->get_result();

        $min_sid = null;

        $stmt_sup = $con -> prepare('SELECT * FROM suppliers ORDER BY name ASC Limit 1');
        $stmt_sup -> execute();
        $result_sup = $stmt_sup->get_result();
        while($row_sup = $result_sup -> fetch_assoc())
        {
            $min_sid = $row_sup['id'];
        }

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
                $special_jaarplanning = '';
                if($row['name'] == 'Afspraak architect' ||                   
                  $row['name'] == 'Betonplaat' || 
                  $row['name'] == 'Kozijnen' || 
                  $row['name'] == 'Gevelbekleding'
                )
                  $special_jaarplanning = 'YES';
                // if($row['order_date'] == 'true')
                //     $bdate = $dt;
                // if($row['supply_date'] == 'true')
                //     $ldate = $dt;
                // if($row['supply_date'] == 'true')
                //     $sid = $min_sid;
                $stmt1 -> bind_param('iissssss',$_POST['contactid'], $row['id'], $dtt, $status, $bdate, $ldate, $sid, $special_jaarplanning );
                $stmt1 -> execute();
            }
        }

        $stmt = $con -> prepare("SELECT PT1.*, PTL.id as line_id FROM projects_tasks PT1 
          LEFT JOIN (SELECT * FROM project_tasks_lines WHERE contact_id = ? ) PTL ON PT1.id = PTL.projects_tasks_id
          WHERE PT1.custom_contact_id is NULL OR PT1.custom_contact_id = ? ORDER BY PT1.id ASC
        ");
        $stmt->bind_param('ii', $_POST['contactid'], $_POST['contactid']);
        $stmt -> execute();
        $result = $stmt->get_result();


        while ($row = $result->fetch_assoc()) {
          if($row['line_id'] == null)
          {
              $stmt1 = $con -> prepare('INSERT INTO project_tasks_lines (contact_id, projects_tasks_id, started_at, status, besteldatum, leverdatum, supplier_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
              $date = date_create($_POST['startdatum']);
              $dtt = $date->format("Y-m-d H:i:s");
              $dt = $date->format("Y-m-d");
              $status = 'PROCESSING';
              $bdate = null;
              $ldate = null;
              $sid = null;
              $stmt1 -> bind_param('iisssss',$_POST['contactid'], $row['id'], $dtt, $status, $bdate, $ldate, $sid );
              $stmt1 -> execute();
          }
      }

    }





    echo "Opdracht opgeslagen.";
    $stmt->close();
  } else {
    // Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
    echo "Database fout!";
  }	
}
else{
    echo "Database fout!";
}

$con->close();

?>
