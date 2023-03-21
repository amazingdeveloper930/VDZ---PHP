<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require( '../../common/connection.php');
require( '../../common/global.php');

if ($stmt = $con->prepare('SELECT PT.*,  PTL.contact_id, PTL.started_at, PTL.status, PTL.besteldatum, PTL.leverdatum, PTL.supplier_id, PTL.user_id,  PTL.special_jaarplanning, A.username, PTSL.factuurnummer, PTSL.price, PTSL.price_inc, PTSL.betaaldatum FROM projects_tasks PT LEFT JOIN project_tasks_lines PTL ON PT.id = PTL.projects_tasks_id AND PTL.contact_id = ? LEFT JOIN accounts A ON PTL.user_id = A.id 
LEFT JOIN project_tasks_special_lines PTSL ON PTL.contact_id = PTSL.contact_id AND PT.id = PTSL.projects_tasks_id
WHERE PT.custom_contact_id is NULL OR PT.custom_contact_id = ?
ORDER BY PT.chapter ASC,  PT.sort_order ASC')) {
$stmt->bind_param('ii', $_POST['contactid'], $_POST['contactid']);
$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();

}
$tasks = [];
$temp = [];
$project_code = '';
while ($row = $result->fetch_assoc()) {
    $mode = null;
    
    if($row['status'] == 'COMPLETED')
        $mode = 2;
    if($row['status'] == 'PROCESSING')
        $mode = 1;
    if($row['status'] == 'SKIPPED')
        $mode = 0;
    // if(isset($row['status']) && $row['isspecial_task'] == 1 && $row['id'] == 48)
    //     $mode = 3;
    if($row['is_invoice'] == 1 && $row['isspecial_task'] == 1)
        $temp[$row['id']] = $row['price_inc'];
    // if($row['id'] >= 45 && $row['id'] < 48 && $row['price_inc'] != $temp[$row['id'] - 44])
    //     $mode = 1;
    if($row['is_invoice'] == 0 && $row['isspecial_task'] == 1)
    {
        if($row['price_inc'] != $temp[$row['invoice_id']])
        $mode = 1;
    }
    ///////////////////
    $stmt_new = $con->prepare('SELECT COUNT(id) AS note_count FROM projects_tasks_notes WHERE contact_id = ? AND project_tasks_id=?');
    $stmt_new->bind_param('ii', $_POST['contactid'], $row['id']);
	$stmt_new->execute();
    $result_new =  $stmt_new->get_result();
    while($row_new = $result_new -> fetch_assoc())
    {
        $row['note_count'] = $row_new['note_count'];
    }
    ///////////////////


    $row['timer_widget'] = getTaskTimer($row['started_at'],  $row['timer'], $mode);
    $difDay = dateDifferenceD($row['started_at']);
    // if($difDay < 0)
    //     $row['status'] = 'INIT';
    $tasks[] = $row;
}

if ($stmt = $con->prepare('SELECT PTC.* FROM projects_tasks_chapters PTC ORDER BY PTC.sort_order ASC')) {

    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();
    
}
$task_chapter = [];
while ($row = $result->fetch_assoc()) {
    $task_chapter[] = $row;
}

if ($stmt = $con->prepare('SELECT * FROM suppliers ORDER BY name ASC')) {

    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();
    
}
$task_suppliers = [];
while ($row = $result->fetch_assoc()) {
    $task_suppliers[] = $row;
}

if($stmt = $con -> prepare('SELECT * FROM special_tasks WHERE contact_id = ?')){
    $stmt -> bind_param('i', $_POST['contactid']);
    $stmt -> execute();
    $result = $stmt->get_result();
}

$task_special = [];
while ($row = $result->fetch_assoc()) {
    $task_special[] = $row;
}

$task_planning = [];
if($stmt = $con -> prepare('SELECT * FROM project_planning WHERE contact_id = ?'))
{
    $stmt -> bind_param('i', $_POST['contactid']);
    $stmt -> execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $row['color_widget'] = '<span class="new badge pt-' . $row['color']. '" data-badge-caption=""></span>';
        $task_planning[] = $row;
    }
}

$result_array = [];
$result_array['task_list'] = $tasks;
$result_array['task_chapter'] = $task_chapter;
$result_array['task_suppliers'] = $task_suppliers;
$result_array['task_special'] = $task_special;
$result_array['task_planning'] = $task_planning;
$result_array['klanten_widget'] = '';

$stmt = $con -> prepare('SELECT * FROM projects WHERE contact_id = ?');
$stmt -> bind_param("i", $_POST['contactid']);
$stmt -> execute();
$result = $stmt -> get_result();
$project_code = null;
while($row = $result->fetch_assoc())
{
    $project_code = $row['project_code'];
}
if($project_code != null)
{
    $result_array['klanten_widget'] = getKlantenWidget($project_code);
}
echo json_encode($result_array);


?>
