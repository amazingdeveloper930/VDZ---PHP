<?php

require( '../../common/connection.php');
require( '../../common/global.php');
require ('../../common/sessie_check.php');


// We need to check if the account with that username exists.
    $stmt = $con -> prepare("SELECT PTN.*, PT.name FROM projects_tasks_notes PTN LEFT JOIN projects_tasks PT ON PTN.project_tasks_id = PT.id WHERE PTN.id = ?");
    $stmt -> bind_param('i', $_POST['note_id']);
    $stmt -> execute();
    $result =  $stmt->get_result();
    $contact_id = null;
    $task_id = null;
    while ($row = $result->fetch_assoc()) {
        $contact_id = $row['contact_id'];
        $task_id = $row['project_tasks_id'];
        $file_name = $row['file_path'];
        if(isset($file_name))
            unlink('../../upload/' . $file_name);

            addNewLog($row['contact_id'], $_SESSION['id'], 'Notitie  verwijderd: ' . $row['name'], $row['data']);
    }

if ($stmt = $con->prepare('DELETE FROM projects_tasks_notes WHERE id = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('s', $_POST['note_id']);
	$stmt->execute();	
	$stmt->close();

    $stmt = $con->prepare('SELECT COUNT(id) AS note_count FROM projects_tasks_notes WHERE contact_id = ? AND project_tasks_id=?');
    $stmt->bind_param('ii', $contact_id, $task_id);
	$stmt->execute();
    $result =  $stmt->get_result();
    $result_array = [];
    $result_array['contact_id'] = $contact_id;
    $result_array['task_id'] = $task_id;

    while ($row = $result->fetch_assoc()) {
        $result_array['note_count'] = $row['note_count'];
    }
    $result_array['message'] = "Notitie item verwijderd.";
	echo json_encode($result_array);	

} else {
	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
    echo json_encode(['message' => 'Database fout!']);
}

$con->close();

?>
