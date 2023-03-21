<?php

require('../../common/connection.php');

if($stmt = $con -> prepare('UPDATE employees SET ' . $_POST['day']. ' = ? WHERE id = ?'))
{
    $value = 0;
    if($_POST['value'] == 'true')
        $value = 1;
    $stmt -> bind_param('ii', $value, $_POST['employee_id']);
    $stmt -> execute();
    
    echo 'Successfully updated';
}
else{
    echo 'Database fout!';
}
