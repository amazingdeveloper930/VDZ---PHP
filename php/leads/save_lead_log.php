<?php

require( '../../common/connection.php');
require( '../../common/global.php');

if($_POST['contactid']) {
	
  if ($stmt = $con->prepare('INSERT INTO contact_log (contact_id, entry_type, entry_title, entry_date, entry_description, account_id, file_exe, file_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')) {
    // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
    $date = date_create();
    //$dt = $date->format("Y-m-d H:i:s");
	$dt = $date->format("H:i:s");
	$newdate = $_POST['entrydate'] . " " . $_POST['entrytime'];
	
    $title = 'lead';

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
        
        if($fileEXE == 'jpg' || $fileEXE == 'jpeg' || $fileEXE == 'png'){
          $maxDim = 1920;
          $file_tmp_name = $_FILES['file']['tmp_name'];
          list($width, $height, $type, $attr) = getimagesize( $file_tmp_name );
  
  
              if ( $width > $maxDim || $height > $maxDim ) {
                  $target_filename = $file_tmp_name;
                  $ratio = $width/$height;
                  if( $ratio > 1) {
                      $new_width = $maxDim;
                      $new_height = $maxDim/$ratio;
                  } else {
                      $new_width = $maxDim*$ratio;
                      $new_height = $maxDim;
                  }
                  $src = imagecreatefromstring( file_get_contents( $file_tmp_name ) );
                  $dst = imagecreatetruecolor( $new_width, $new_height );
                  imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
                  imagedestroy( $src );
                  imagepng( $dst, $target_filename ); // adjust format as needed
                  imagedestroy( $dst );
              }
          }

	      move_uploaded_file($_FILES['file']['tmp_name'],$location);
	    }



    $stmt->bind_param('iisssiss', $_POST['contactid'], $_POST['type'], $title, $newdate , $_POST['desc'], $_POST['userid'], $fileEXE, $filename);
    $stmt->execute();  
    $inserted_id = $stmt -> insert_id;

    if($_POST['type'] == 1) // deal ?
    {
      $stmt_new = $con -> prepare("SELECT * FROM projects ORDER BY id DESC LIMIT 1");
      $stmt_new->execute();
// Store the result so we can check if the account exists in the database.
      $result = $stmt_new->get_result(); 
      $date = date_create();
      $year = $date->format("Y");
      $number = 1;
      while ($row = $result->fetch_assoc()) {

        $last_project_number = $row['project_number'];
        $last_year = explode("-",$last_project_number)[0];
        $last_number = explode("-",$last_project_number)[1];
        if($last_year == $year)
          $number = (int)$last_number + 1;
      }
      $dt = $date->format("Y-m-d");
      $stmt_new = $con -> prepare('INSERT INTO projects (contact_id, project_number, convert_date, project_code) VALUES (?, ?, ?, ?)');
      $new_projectnumber = $year . "-" . sprintf("%04d", $number);
      $new_projectcode = getRandomString();
      $stmt_new->bind_param('ssss', $_POST['contactid'], $new_projectnumber, $dt, $new_projectcode);
      $stmt_new->execute(); 

      $stmt_new = $con -> prepare('SELECT * FROM project_planning_default ORDER BY sort_order');
      $stmt_new -> execute();
      $result_planning = $stmt_new -> get_result();
      while ($row_planning = $result_planning->fetch_assoc()) {
        $stmt_new = $con -> prepare("INSERT INTO project_planning (contact_id, name, sort_order, week, color) VALUES (?, ?, ?, ?, ?)");
        $stmt_new->bind_param('isiis',  $_POST['contactid'], $row_planning['name'], $row_planning['sort_order'], $row_planning['week'], $row_planning['color']);
        $stmt_new->execute();
      }

    }
    

    $query = 'UPDATE contacts SET l_status = ? WHERE id = ?';		

    if ($stmt = $con->prepare($query)) {
    // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
      $stmt->bind_param('ii', $_POST['type'], $_POST['contactid']);
      $stmt->execute();
    } else {
    // Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
    // echo 'Database fout!';
    }	




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
