<?php

require( '../../common/connection.php');
require( '../../common/global.php');

$result_array = [];
$result = [];
$folder_id = 0;
if(isset($_POST['folder_id']))
    $folder_id = (int)$_POST['folder_id'];

if($stmt = $con -> prepare('SELECT PF.*, A.username FROM projects_file PF LEFT JOIN accounts A ON PF.user_id = A.id WHERE PF.contact_id = ? AND PF.folder_id = ? ORDER BY PF.uploaded_date DESC'))
{
    $stmt -> bind_param('ii', $_POST['contact_id'], $folder_id);
    $stmt -> execute();
    $result = $stmt->get_result();
    while($row = $result -> fetch_assoc())
    {
        $result_array []= $row;
    }
    
}
if($stmt = $con -> prepare('SELECT  PTN.contact_id, PTN.created_at AS uploaded_date, PTN.file_path, PTN.file_exe, PTN.folder_id, A.username FROM projects_tasks_notes PTN LEFT JOIN accounts A ON PTN.user_id = A.id WHERE PTN.contact_id = ? AND PTN.file_path is not NULL AND PTN.folder_id = ? ORDER BY PTN.created_at DESC'))
{
    $stmt -> bind_param('ii', $_POST['contact_id'], $folder_id);
    $stmt -> execute();
    $result = $stmt->get_result();
    while($row = $result -> fetch_assoc())
    {
        $row['id'] = '';
        $row['name'] = 'Taak notitie';
        $row['file_type'] = 5;
        $result_array []= $row;
    }
    
}

if($stmt = $con -> prepare('SELECT PT.contact_id,   PTN.created_at AS uploaded_date, PTN.file_path, PTN.file_exe, PTN.folder_id,  A.username FROM projects_tickets PT LEFT JOIN projects_tickets_notes PTN ON PT.id = PTN.ticket_id LEFT JOIN accounts A ON PTN.user_id = A.id WHERE PT.contact_id = ? AND PTN.file_path is not NULL AND PTN.folder_id = ? ORDER BY PTN.created_at DESC'))
{
    $stmt -> bind_param('ii', $_POST['contact_id'], $folder_id);
    $stmt -> execute();
    $result = $stmt->get_result();
    while($row = $result -> fetch_assoc())
    {
        $row['id'] = '';
        $row['name'] = 'Ticket notitie';
        $row['file_type'] = 5;
        $result_array []= $row;
    }
    
}

if($stmt = $con -> prepare('SELECT CL.entry_title AS name, CL.entry_date AS uploaded_date, CL.contact_id, CL.file_exe, CL.file_path, CL.entry_type, CL.folder_id, A.username FROM contact_log CL LEFT JOIN accounts A ON CL.account_id = A.id WHERE CL.contact_id = ? AND CL.file_path is not NULL AND CL.folder_id = ?'))
{
    $stmt -> bind_param('ii', $_POST['contact_id'], $folder_id);
    $stmt -> execute();
    $result = $stmt->get_result();
    while($row = $result -> fetch_assoc())
    {
        $row['id'] = '';
        $row['file_type'] = 5;
        if($row['name'] == 'contact')
            $row['name'] = CONTACT_TYPE[ $row['entry_type']];
        else if($row['name'] == 'lead')
            $row['name'] = LEAD_TYPE[ $row['entry_type']];
        $result_array []= $row;
    }
    
}


usort($result_array, "cmpFiles");

$folder_array = [];

$stmt = $con -> prepare("SELECT FF.*, A.username FROM file_folder FF LEFT JOIN accounts A ON FF.account_id = A.id WHERE FF.contact_id = ? AND FF.parent = ? ORDER BY FF.created_at DESC");
$stmt -> bind_param("ii", $_POST['contact_id'], $folder_id);
$stmt -> execute();
$result = $stmt->get_result();
while($row = $result -> fetch_assoc())
{
    $folder_array []= $row;
}

$path_list = [];
$flag = true;
$folderid = $folder_id;
while($folder_id != 0 && $flag)
{
    $flag = false;
    $stmt = $con -> prepare("SELECT * FROM file_folder WHERE id = ? AND contact_id = ? LIMIT 1");
    $stmt -> bind_param('ii', $folder_id, $_POST['contact_id']);
    $stmt -> execute();
    $result = $stmt->get_result();
    while($row = $result -> fetch_assoc())
    {
        $folder_id = $row['parent'];
        if($row['id'] != $folder_id)
        {
            $flag = true;
            $path_list []= $row;
        }
    }
}
$path_list []= ['id'=> 0, 'name' => 'Bestanden'];
$path_list = array_reverse($path_list);

echo json_encode(['file_list' =>  $result_array, 'folder_list' => $folder_array, 'path_list' => $path_list, 'folder_id' => $folderid]);
