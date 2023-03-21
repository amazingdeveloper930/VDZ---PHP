<?php

require( '../../common/connection.php');

if($_POST['contactid']) {
	
  if ($stmt = $con->prepare('INSERT INTO contact_log (contact_id, entry_type, entry_title, entry_date, entry_description, account_id, file_exe, file_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')) {
    // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
    $date = date_create();
    //$dt = $date->format("Y-m-d H:i:s");;
	  $newdate = $_POST['entrydate'] . " " . $_POST['entrytime'];
    $title = 'contact';
    ////////////////////
    $fileEXE = null;
    $filename = null;
    if(count($_FILES) > 0)
    {
      $filename = $_FILES['file']['name'];
    
      $fileEXE = pathinfo($filename,PATHINFO_EXTENSION);
      $fileEXE = strtolower($fileEXE);
      $valid_extensions = array("jpg","jpeg","png", "pdf");

      $filename = str_replace(' ', '-', $filename);
      $filename =  preg_replace('/[^A-Za-z0-9.\-]/', '', $filename);
      $filename = strtotime("now") . $filename;
      $location = "../../upload/" . $filename;
      move_uploaded_file($_FILES['file']['tmp_name'],$location);
    }
    

    /////////////////////



    $stmt->bind_param('iisssiss', $_POST['contactid'], $_POST['type'], $title, $newdate , $_POST['desc'], $_POST['userid'], $fileEXE, $filename);
    $stmt->execute();  
    $inserted_id = $stmt -> insert_id;
    $stmt = $con -> prepare('SELECT CL.*, A.username FROM contact_log CL LEFT JOIN accounts A ON CL.account_id = A.id WHERE CL.entry_date >= ? AND contact_id = ? ORDER BY CL.entry_date DESC' );
    $stmt -> bind_param('ss', $newdate, $_POST['contactid']);
    $stmt -> execute();
    $result = $stmt -> get_result();

    $result_array = [];
    $result_array['next_one'] = '';
    $result_array['new_one'] = '';
    while ($row = $result->fetch_assoc()) {
      if($row['id'] == $inserted_id)
      {
        $result_array['new_one'] = $row;
        break;
      }
      $result_array['next_one'] = $row;
    } 

    $result_array['message'] = 'Logboek opgeslagen.';
    echo json_encode($result_array);
    $stmt->close();
  } else {
    // Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
    echo json_encode(['message' => 'Database fout!']);
  }	
}

$con->close();

?>
