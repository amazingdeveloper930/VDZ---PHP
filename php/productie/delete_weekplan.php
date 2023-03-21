<?php

session_start();


require( '../../common/connection.php');


if ($_POST['plan_id'] == '') {

	die ('Er ging iets mis bij het verwijderen.');

}

if ($stmt = $con->prepare('DELETE FROM werkplanning_medewerker WHERE id = ?')) {
    $stmt->bind_param('s', $_POST['plan_id']);
	$stmt->execute();	
	$stmt->close();
    echo json_encode(['message' => 'Notitie item verwijderd.']);
}
else{
    echo json_encode(['message' => 'Database fout!']);
}