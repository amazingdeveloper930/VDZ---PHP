<?php

require('../../common/connection.php');

if($_POST['meetingdate'] == '')
{
    die ('Date data should be filled');
}
$meeting_id = '';
if($_POST['salesmeetingid'] == '') { // new sales-meeting

    $stmt = $con -> prepare("SELECT * FROM sales_meeting WHERE date = ? AND 
                            sales_agent = ? AND 
    ( 
                            (time_from <= ? AND time_untill > ?) OR 
                            (time_from < ? AND time_untill >= ?) OR 
                            (time_from >= ? AND time_from < ?) 
    )");

    $stmt -> bind_param('sissssss', $_POST['meetingdate'], $_POST['agentid'], $_POST['fromTime'], $_POST['fromTime'], $_POST['toTime'], $_POST['toTime'], $_POST['fromTime'], $_POST['toTime']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo json_encode(['message' => 'Er staat al een afspraak om die tijd.']);
        die('');
    }


    if ($stmt = $con -> prepare('INSERT INTO sales_meeting (date, contact, sales_agent, time_from, time_untill) VALUES (?, ?, ?, ?, ?)')) {
        // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
        $stmt->bind_param('siiss', $_POST['meetingdate'], $_POST['leadid'], $_POST['agentid'], $_POST['fromTime'], $_POST['toTime']);
        $stmt->execute();
        $meeting_id = $stmt -> insert_id;
        

        //////////////////

        if ($stmt = $con->prepare('INSERT INTO contact_log (contact_id, entry_type, entry_title, entry_date, entry_description, account_id) VALUES ( ?, ?, ?, ?, ?, ?)')) {
            // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
            $date = date_create();
            $dt = $date->format("Y-m-d H:i:s");
            $date = date_create($_POST['meetingdate']);
            $desc = "Afspraak gepland op " . $date -> format("d-m-Y");
            $title = 'lead';
            $type = 5;
            $stmt->bind_param('iisssi',$_POST['leadid'], $type, $title, $dt, $desc, $_POST['userid']);
            $stmt->execute(); 
            
            
        
            $query = 'UPDATE contacts SET l_status = ? WHERE id = ?';		
        
            if ($stmt = $con->prepare($query)) {
            // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
              $type = 5;
              $stmt->bind_param('ii', $type, $_POST['leadid']);
              $stmt->execute();
            } else {
                echo json_encode(['message' => 'Database fout!']);
                die('');
            }	
        
        }
        else{
            echo json_encode(['message' => 'Database fout!']);
                die('');
        }
        /////////////////



        
      } else {
        // Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
        echo json_encode(['message' => 'Database fout!']);
        die('');
      }		  

   
}
else{

    $stmt = $con -> prepare("SELECT * FROM sales_meeting WHERE date = ? AND 
                            sales_agent = ? AND id <> ? AND
    ( 
                            (time_from <= ? AND time_untill > ?) OR 
                            (time_from < ? AND time_untill >= ?) OR 
                            (time_from >= ? AND time_from < ?) 
    )");

    $stmt -> bind_param('siissssss', $_POST['meetingdate'], $_POST['agentid'], $_POST['salesmeetingid'], $_POST['fromTime'], $_POST['fromTime'], $_POST['toTime'], $_POST['toTime'], $_POST['fromTime'], $_POST['toTime']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo json_encode(['message' => 'Er staat al een afspraak om die tijd.']);
        die('');
    }


    if ($stmt = $con -> prepare('UPDATE sales_meeting SET date = ?, contact = ?, time_from = ?, time_untill = ? WHERE id = ?'))
    {
        $meeting_id = $_POST['salesmeetingid'];
        $stmt->bind_param('sissi', $_POST['meetingdate'], $_POST['leadid'], $_POST['fromTime'], $_POST['toTime'], $_POST['salesmeetingid']);
        $stmt->execute();
    }
    else{
        echo json_encode(['message' => 'Database fout!']);
        die('');
    }
}




if($stmt_new = $con -> prepare('SELECT sales_meeting.id, sales_meeting.date, sales_meeting.contact, sales_meeting.sales_agent, sales_meeting.time_from, sales_meeting.time_untill, sales_agents.color FROM sales_meeting LEFT JOIN sales_agents ON sales_meeting.sales_agent = sales_agents.id WHERE sales_meeting.id = ? LIMIT 1'))
{
    $stmt_new->bind_param('s', $meeting_id);
    $stmt_new->execute();
    $result = $stmt_new->get_result();
    while ($row = $result->fetch_assoc()) {
        $dt = strtotime($row['date']);
        $row['month'] = getdate($dt)['mon'];
        $row['day'] = getdate($dt)['mday'];
        $result_array[] = $row;
        }
    echo json_encode(['message' => 'Afspraak opgeslagen.', 'salesmeeting' => $result_array]);
}