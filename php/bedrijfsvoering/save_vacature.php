<?php

require('../../common/connection.php');

$_POST['title'] = "";

$vacature_id = "";
$cv_filename = NULL;
$mot_filename = NULL;

if(count($_FILES) > 0)
{
    if(isset($_FILES['cv']))
    {
        $cv_filename = $_FILES['cv']['name'];

        $fileEXE = pathinfo($cv_filename,PATHINFO_EXTENSION);
        $fileEXE = strtolower($fileEXE);
        $valid_extensions = array("jpg","jpeg","png", "pdf");
      
        $cv_filename = str_replace(' ', '-', $cv_filename);
        $cv_filename =  preg_replace('/[^A-Za-z0-9.\-]/', '', $cv_filename);
        $cv_filename = strtotime("now") . $cv_filename;
        $location = "../../upload/" . $cv_filename;
        move_uploaded_file($_FILES['cv']['tmp_name'],$location);
    }
    if(isset($_FILES['mot']))
    {
        $mot_filename = $_FILES['mot']['name'];

        $fileEXE = pathinfo($mot_filename,PATHINFO_EXTENSION);
        $fileEXE = strtolower($fileEXE);
        $valid_extensions = array("jpg","jpeg","png", "pdf");
      
        $mot_filename = str_replace(' ', '-', $mot_filename);
        $mot_filename =  preg_replace('/[^A-Za-z0-9.\-]/', '', $mot_filename);
        $mot_filename = strtotime("now") . $mot_filename;
        $location = "../../upload/" . $mot_filename;
        move_uploaded_file($_FILES['mot']['tmp_name'],$location);
    }
}



if(!(isset($_POST['vacature_id']) && $_POST['vacature_id'] != ""))
{
    $_POST['status'] = "In behandeling";
    $stmt = $con -> prepare("INSERT INTO vacatures (title, full_name, email, phone, message, status, cv, mot) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt -> bind_param("ssssssss", $_POST['title'], $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['message'], $_POST['status'], $cv_filename, $mot_filename);
    $stmt -> execute();
    $vacature_id = $stmt -> insert_id;
}
else{
    $stmt = $con -> prepare("UPDATE vacatures SET title = ?, full_name = ?, email = ?, phone = ?, message = ?  WHERE ID = ?");
    $stmt -> bind_param("sssssi", $_POST['title'], $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['message'],  $_POST['vacature_id']);
    $stmt -> execute();
    $vacature_id = $_POST['vacature_id'];

    $stmt = $con -> prepare("SELECT * FROM vacatures WHERE ID = ?");
    $stmt -> bind_param("i", $vacature_id);
    $stmt -> execute();
    $result = $stmt -> get_result();
    while($row = $result -> fetch_assoc())
    {
        if($cv_filename && $row['cv'])
        {
            unlink('../../upload/' . $row['cv']);
            
        }
        
        if($mot_filename && $row['mot'])
        {
            unlink('../../upload/' . $row['mot']);
        }
        
    }
    if($cv_filename)
    {
        $stmt = $con -> prepare("UPDATE vacatures SET cv = ? WHERE ID = ?");
        $stmt -> bind_param("si", $cv_filename,  $vacature_id);
        $stmt -> execute();
    }
    if($mot_filename)
    {
        $stmt = $con -> prepare("UPDATE vacatures SET mot = ? WHERE ID = ?");
        $stmt -> bind_param("si", $mot_filename,  $vacature_id);
        $stmt -> execute();
    }
    


}



$stmt = $con -> prepare("SELECT * FROM vacatures WHERE ID = ?");
$stmt -> bind_param("i", $vacature_id);
$stmt -> execute();
$result = $stmt -> get_result();
$result_array = [];
while($row = $result -> fetch_assoc())
{
    $result_array = $row;
}

echo json_encode(['message' => 'Vacature opgeslagen.', 'item' => $result_array]);