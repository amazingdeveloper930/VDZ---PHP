<?php

require('../../common/connection.php');

if(isset($_POST['materiaalid'])){
   if($stmt = $con -> prepare('SELECT M.*, E.name AS employee_name FROM materiaal M LEFT JOIN employees E ON M.employee = E.id WHERE M.id = ?'))
   {
        $stmt -> bind_param('i', $_POST['materiaalid']);
        $stmt -> execute();
        $result = $stmt -> get_result();
        
        $result_array = [];
        while($row = $result -> fetch_assoc())
        {
            if($row['employee'] == 0)
                $row['employee_name'] = 'Algemeen';
            $result_array = $row;
        }
        echo json_encode($result_array);
   } 
   else{
    echo json_encode(['message' => 'Database fout!']);
   }
}
else{
    echo json_encode(['message' => 'Database fout!']);
}