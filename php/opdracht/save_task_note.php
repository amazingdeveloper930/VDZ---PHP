<?php

require( '../../common/connection.php');
require( '../../common/global.php');

if($_POST['contact_id']) {
    if($stmt = $con -> prepare("INSERT INTO projects_tasks_notes (contact_id, project_tasks_id, created_at, data, user_id, file_exe, file_path) VALUES (?, ?, ?, ?, ?, ?, ?)"))
    {
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


        $date = date_create();
        $dtt = $date->format("Y-m-d H:i:s");
        $stmt -> bind_param('iississ', $_POST['contact_id'], $_POST['project_tasks_id'], $dtt, $_POST['data'], $_POST['user_id'], $fileEXE, $filename);
        $stmt -> execute();
        $id = $stmt -> insert_id;

        $stmt_task = $con -> prepare("SELECT * FROM projects_tasks WHERE id = ?");
        $stmt_task -> bind_param('i', $_POST['project_tasks_id']);
        $stmt_task -> execute();
        $result_task = $stmt_task -> get_result();
        while($task_line = $result_task -> fetch_assoc())
        {
            addNewLog($_POST['contact_id'], $_POST['user_id'], 'Taaknotitie: ' . $task_line['name'], $_POST['data']);
        }
        


        $stmt = $con -> prepare("SELECT PTN.*, A.username FROM projects_tasks_notes PTN LEFT JOIN accounts A ON PTN.user_id = A.id WHERE PTN.id = ?");
        $stmt -> bind_param('i', $id);
        $stmt -> execute();
        $result = $stmt->get_result();
        $result_array = [];
        while ($row = $result->fetch_assoc()) {
            $dt = new DateTime($row['created_at']);
            $row['created_at']  =   $dt->format('d-m-Y') . " " . $dt->format('H:i');  
            
            $result_array = $row;
        }
        echo json_encode(["message" => 'Notitie opgeslagen.', "data" => $result_array]);
        $stmt->close();
    }
    else{
        echo json_encode(["message" => 'Database fout!']);
    }
    $con -> close();
}
else{
    
}
?>
