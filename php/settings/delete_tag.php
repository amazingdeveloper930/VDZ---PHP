<?php

session_start();


require( '../../common/connection.php');


if($stmt = $con -> prepare('DELETE FROM tags WHERE id = ?'))
{
    $stmt -> bind_param('i', $_POST['tag_id']);
    $stmt -> execute();
    echo json_encode(['status' => 'success', 'tag_id' =>  $_POST['tag_id']]);
}
else{
    echo json_encode(['status' => 'error', 'message' => 'Database fout!']);
}