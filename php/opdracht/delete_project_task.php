<?php 
require( '../../common/connection.php');
require( '../../common/global.php');
require ('../../common/sessie_check.php');

$task_ids = [];
if(isset($_POST['task_id'])){
    $stmt = $con -> prepare('SELECT * FROM projects_tasks WHERE id = ? AND custom_contact_id = ?');
    $stmt -> bind_param('ii', $_POST['task_id'], $_POST['contact_id']);
    $stmt -> execute();

    $result_tasks = $stmt -> get_result();
    $task_ids []= $_POST['task_id'];
    while($row = $result_tasks -> fetch_assoc()){

        addNewLog($_POST['contact_id'], $_SESSION['id'], 'Taak verwijderd: ' . $row['name']);


        if($row['isspecial_task'])
        {
            if($row['is_invoice'] == 1){
                $stmt = $con -> prepare('SELECT * FROM projects_tasks WHERE invoice_id = ? AND custom_contact_id = ?');
                $stmt -> bind_param('ii', $_POST['task_id'] , $_POST['contact_id']);
                $stmt -> execute();
                $result_invoice = $stmt -> get_result();
                while($row_invoice = $result_invoice -> fetch_assoc()){
                    $task_ids []= $row_invoice['id'];
                }
            }
            else{
                $task_ids []= $row['invoice_id'];
            }
        }
        else{
            $task_ids []= 0;
        }
    }

    $stmt = $con -> prepare('DELETE FROM projects_tasks WHERE id = ? OR id = ?');
    $stmt -> bind_param('ii', $task_ids[0] , $task_ids[1]);
    $stmt -> execute();

    $stmt = $con -> prepare('DELETE FROM projects_tasks_notes WHERE project_tasks_id = ? OR project_tasks_id = ?');
    $stmt -> bind_param('ii', $task_ids[0] , $task_ids[1]);
    $stmt -> execute();

    $stmt = $con -> prepare('DELETE FROM project_tasks_lines WHERE projects_tasks_id = ? OR projects_tasks_id = ?');
    $stmt -> bind_param('ii', $task_ids[0] , $task_ids[1]);
    $stmt -> execute();

    $stmt = $con -> prepare('DELETE FROM project_tasks_special_lines WHERE projects_tasks_id = ? OR projects_tasks_id = ?');
    $stmt -> bind_param('ii', $task_ids[0] , $task_ids[1]);
    $stmt -> execute();

    echo json_encode(['message' => 'Notitie item verwijderd.', 'items' => $task_ids]);
}
