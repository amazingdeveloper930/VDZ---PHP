<?php 
require( '../../common/connection.php');
if($stmt = $con -> prepare('DELETE FROM project_planning WHERE contact_id = ? AND id = ?')){
    $stmt -> bind_param('ii', $_POST['contact_id'], $_POST['plan_id']);
    $stmt -> execute();
    echo json_encode(['message' => 'Projectplanning verwijderd']);

}
else{
    echo json_encode(['message' => 'Database fout!']);
}