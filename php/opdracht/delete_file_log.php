<?php

session_start();
require( '../../common/connection.php');

if ($_POST['file_id'] == '') {
	die ('Er ging iets mis bij het verwijderen.');
}

if($stmt = $con -> prepare("SELECT * FROM projects_file WHERE id = ?"))
{
    $stmt -> bind_param('i', $_POST['file_id']);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $file_name = '';
    while ($row = $result->fetch_assoc()) {
        $file_name = $row['file_path'];
    }
    unlink('../../upload/' . $file_name);
    $stmt = $con -> prepare('DELETE FROM projects_file WHERE id = ?');
    $stmt -> bind_param('i', $_POST['file_id']);
    $stmt -> execute();
    echo 'Bestand verwijderd';
}
else
{
    echo 'Database fout!';
}

