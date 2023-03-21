<?php

require( '../../common/connection.php');
require( '../../common/global.php');

$result_array = [];
$result = [];
if($stmt = $con -> prepare('SELECT SF.*, A.username FROM suppliers_file SF LEFT JOIN accounts A ON SF.user_id = A.id WHERE supplier_id = ? ORDER BY SF.uploaded_date DESC'))
{
    $stmt -> bind_param('i', $_POST['supplier_id']);
    $stmt -> execute();
    $result = $stmt->get_result();
    while($row = $result -> fetch_assoc())
    {
        $result_array []= $row;
    }
    
}
echo json_encode($result_array);
