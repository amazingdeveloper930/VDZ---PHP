<?php

session_start();
require( '../../common/connection.php');


// We need to check if the account with that username exists.
    $stmt = $con -> prepare("SELECT * FROM employees_notes WHERE id = ?");
    $stmt -> bind_param('i', $_POST['employee_note_id']);
    $stmt -> execute();
    $result =  $stmt->get_result();
    $employee_id = null;
    while ($row = $result->fetch_assoc()) {
        $employee_id = $row['employee_id'];
        $file_name = $row['file_path'];
        if(isset($file_name))
            unlink('../../upload/' . $file_name);
    }

if ($stmt = $con->prepare('DELETE FROM employees_notes WHERE id = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('s', $_POST['employee_note_id']);
	$stmt->execute();	
	$stmt->close();

    $stmt = $con->prepare('SELECT COUNT(id) AS note_count FROM employees_notes WHERE employee_id = ?');
    $stmt->bind_param('i', $employee_id);
	$stmt->execute();
    $result =  $stmt->get_result();
    $result_array = [];
    $result_array['employee_id'] = $employee_id;

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
