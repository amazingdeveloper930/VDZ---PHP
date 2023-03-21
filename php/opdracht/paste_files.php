<?php
require( '../../common/connection.php');
require('../../common/global.php');

$folder_id = (int)$_POST['folder_id'];

$selected_items = $_POST['selected_items'];
$selected_folders = [];
$selected_files = [];

foreach($selected_items as $selected_item)
{
    if($selected_item['type'] == 'folder')
        $selected_folders []= (int)$selected_item['id'];
    else 
        $selected_files []= (int)$selected_item['id'];
}


while($folder_id != 0)
{
    if(in_array($folder_id, $selected_folders))
    {
        echo json_encode(['status' => 'error']);
        return;
    }
    $stmt = $con -> prepare('SELECT * FROM file_folder WHERE id = ?');
    $stmt -> bind_param('i', $folder_id);
    $stmt -> execute();
    $result = $stmt -> get_result();
    while ($row = $result->fetch_assoc()) {
        $folder_id = (int)$row['parent'];
    }
}




$folder_id = (int)$_POST['folder_id'];
$date = date_create();
$dt = $date->format("Y-m-d H:i:s");

foreach($selected_folders as $selected_folder)
{
    $stmt = $con -> prepare('UPDATE file_folder SET parent = ?, created_at = ? WHERE id = ?');
    $stmt -> bind_param('isi', $folder_id, $dt, $selected_folder);
    $stmt -> execute();
}

foreach($selected_files as $selected_file)
{
    $stmt = $con -> prepare('UPDATE projects_file SET folder_id = ?, uploaded_date = ? WHERE id = ?');
    $stmt -> bind_param('isi', $folder_id, $dt, $selected_file);
    $stmt -> execute();
}
echo json_encode(['status' => 'success']);