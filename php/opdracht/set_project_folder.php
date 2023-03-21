<?php

require( '../../common/connection.php');
require( '../../common/global.php');

if($stmt = $con -> prepare('UPDATE file_folder SET name = ? WHERE id = ?'))
{
    $stmt -> bind_param('si', $_POST['title'], $_POST['id']);
    $stmt -> execute();
    echo json_encode(['message' => 'Bestanden opgeslagen']);
}
else{
    echo json_encode(['message' => 'error']);
}


