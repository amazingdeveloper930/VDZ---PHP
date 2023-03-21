<?php

session_start();
require( '../../common/connection.php');


function deleteFile($file_id)
{
    global $con;
    if($stmt_sub = $con -> prepare("SELECT * FROM projects_file WHERE id = ?"))
    {
        $stmt_sub -> bind_param('i', $file_id);
        $stmt_sub -> execute();
        $result = $stmt_sub -> get_result();
        $file_name = '';
        while ($row = $result->fetch_assoc()) {
            $file_name = $row['file_path'];
        }
        unlink('../../upload/' . $file_name);
        $stmt_sub = $con -> prepare('DELETE FROM projects_file WHERE id = ?');
        $stmt_sub -> bind_param('i', $file_id);
        $stmt_sub -> execute();
    }
    return;
}

function deleteFolder($folder_id)
{
    global $con;
    $stmt_sub = $con -> prepare("SELECT * FROM projects_file WHERE folder_id = ?");
    $stmt_sub -> bind_param('i', $folder_id);
    $stmt_sub -> execute();
    $result = $stmt_sub -> get_result();
    while ($row = $result->fetch_assoc()) {
        deleteFile($row['id']);
    }

    $stmt_sub = $con -> prepare("SELECT * FROM file_folder WHERE parent = ?");
    $stmt_sub -> bind_param('i', $folder_id);
    $stmt_sub -> execute();
    $result = $stmt_sub -> get_result();
    while ($row = $result->fetch_assoc()) {
        deleteFolder($row['id']);
    }

    $stmt_sub = $con -> prepare("DELETE FROM file_folder WHERE id = ?");
    $stmt_sub -> bind_param('i', $folder_id);
    $stmt_sub -> execute();
    return;
}


$choosed_files = $_POST['choosed_files'];
foreach($choosed_files as $choosed)
{
    if($choosed['type'] == 'folder')
        deleteFolder($choosed['id']);
    elseif($choosed['type'] == 'file')
        deleteFile($choosed['id']);
}

echo json_encode(['message' => 'Bestand verwijderd']);
