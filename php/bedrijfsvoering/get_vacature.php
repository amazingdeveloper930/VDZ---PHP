<?php

require('../../common/connection.php');

if(isset($_POST['vacatureid'])){
   if($stmt = $con -> prepare('SELECT V.* FROM vacatures V WHERE V.id = ?'))
   {
        $stmt -> bind_param('i', $_POST['vacatureid']);
        $stmt -> execute();
        $result = $stmt -> get_result();
        
        $result_array = [];
        while($row = $result -> fetch_assoc())
        {
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