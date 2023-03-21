<?php

require( '../../common/connection.php');
require( '../../common/global.php');

$result_array = [];
$result = [];
if($stmt = $con -> prepare('SELECT VF.*, A.username FROM vehicles_file VF LEFT JOIN accounts A ON VF.user_id = A.id WHERE vehicle_id = ? ORDER BY VF.uploaded_date DESC'))
{
    $stmt -> bind_param('i', $_POST['vehicle_id']);
    $stmt -> execute();
    $result = $stmt->get_result();
    while($row = $result -> fetch_assoc())
    {
        $result_array []= $row;
    }
    
}
echo json_encode($result_array);
