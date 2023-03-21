<?php

require('../../common/connection.php');

if(isset($_POST['laptopid'])){
   if($stmt = $con -> prepare('SELECT L.*, E.name AS employee_name FROM laptop L LEFT JOIN employees E ON L.employee = E.id WHERE L.id = ?'))
   {
        $stmt -> bind_param('i', $_POST['laptopid']);
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