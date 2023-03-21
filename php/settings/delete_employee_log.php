<?php


require( '../../common/connection.php');

if($stmt = $con -> prepare('DELETE FROM employee_working_schedule WHERE employee_id = ? AND id = ?'))
{
    $stmt -> bind_param('ii', $_POST['employee_id'], $_POST['elog']);
    $stmt -> execute();

    echo "Logboek item verwijderd.";	

}
else{
    echo 'Database fout!';
}
