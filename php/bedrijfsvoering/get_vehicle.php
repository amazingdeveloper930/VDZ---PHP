<?php

require('../../common/connection.php');

if(isset($_POST['vehicleid'])){
   if($stmt = $con -> prepare('SELECT V.*, E.name AS employee_name FROM vehicle V LEFT JOIN employees E ON V.employee = E.id WHERE V.id = ?'))
   {
        $stmt -> bind_param('i', $_POST['vehicleid']);
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