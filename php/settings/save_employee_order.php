<?php

require('../../common/connection.php');

if(isset($_POST['employee_list']))
{
    foreach($_POST['employee_list'] as $employee){
        if($stmt = $con -> prepare("UPDATE employees SET sort_order = ? WHERE id = ?"))
        {
            $stmt -> bind_param('ii', $employee['sort_order'], $employee['employee']);
            $stmt -> execute();
        }
    }
    
    $result_array['message'] = 'Medewerker opgeslagen';
    echo json_encode($result_array);
}
else{
    echo json_encode(['message' => "Database fout!"]);
}

