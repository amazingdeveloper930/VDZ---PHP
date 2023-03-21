<?php

require( '../../common/connection.php');

require( '../../common/global.php');

$FILE_NAME_LIST = ["Constructieberekening", "Architect tekening", "Kozijn tekening", "Foto van situatie"];

if($_POST['file_type'] == '')
{
    echo json_encode(["message" => "Please Select file type"]);
    die ();
}
$folder_id = 0;
if(isset($_POST['folder_id']))
    $folder_id = $_POST['folder_id'];

if($_POST['contact_id'] && isset($_FILES['file']['name'])) {
	
    $filename = $_FILES['file']['name'];
    
    $fileEXE = pathinfo($filename,PATHINFO_EXTENSION);
    $fileEXE = strtolower($fileEXE);
    $valid_extensions = array("jpg","jpeg","png", "pdf");

    $filename = str_replace(' ', '-', $filename);
    $filename =  preg_replace('/[^A-Za-z0-9.\-]/', '', $filename);
    $filename = strtotime("now") . $filename;
    $location = "../../upload/" . $filename;

    if(in_array(strtolower($fileEXE), $valid_extensions)) {

       
        if ($stmt = $con -> prepare('INSERT projects_file (contact_id, name, file_type, file_exe, uploaded_date, file_path, user_id, klantportaal, folder_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)')) {
            $name = "";
            $file_type = $_POST['file_type'];
            if($file_type != 5)
            {
                $name = $FILE_NAME_LIST[$file_type - 1] . " - " . $_POST['file_name'];
            }
            else
            {
                $name = $_POST['file_name'];
            }
            $date = date_create();
            $dt = $date->format("Y-m-d H:i:s");
            $file_path = $filename;
            $user_id = $_POST['user_id'];
            $klantportaal = 0;
            if($_POST['klantportaal'] == 'true')
                $klantportaal = 1;
            $stmt -> bind_param('isisssiii', $_POST['contact_id'], $name, $file_type, $fileEXE, $dt, $file_path, $user_id, $klantportaal, $folder_id );
            $stmt -> execute();
            $id = $stmt -> insert_id;

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
           addNewLog($_POST['contact_id'], $user_id, "Bestand toegevoegd: " . $name);
            $stmt = $con -> prepare("SELECT PF.*, A.username FROM projects_file PF LEFT JOIN accounts A ON PF.user_id = A.id WHERE PF.id = ?");
            $stmt -> bind_param("i", $id);
            $stmt -> execute();
            $result = $stmt -> get_result();
            
            $result_array = [];
            $result_array['message'] = 'Bestand opgeslagen';
            while($row = $result->fetch_assoc())
            {
                $result_array['inserted_file'] = $row;
            }


            echo json_encode($result_array);
        } else {
        // Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
        echo json_encode(["message" => "Database fout!"]);
        }	
    }
    else{
        echo json_encode(['message' => 'Verkeerd bestandsformaat']);
    }
  }

  $con->close();

?>
