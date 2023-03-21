<?php

require('../../common/connection.php');

if ($stmt = $con->prepare('DELETE FROM work_orders WHERE id = ?')) {
    $stmt->bind_param('s', $_POST['workorder_id']);
	$stmt->execute();	
	$stmt->close();
    echo json_encode(['message' => 'Werkorder regel verwijderd']);
}
else{
    echo json_encode(['message' => 'Database fout!']);
}