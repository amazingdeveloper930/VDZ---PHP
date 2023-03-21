<?php 

require( '../../common/connection.php');
$status =   'Afgewezen';
if($_POST['action'] == 0) {
    $status =   'In behandeling';
}
if($stmt = $con->prepare("UPDATE vacatures SET status = '$status' WHERE ID = ?")) {
    $stmt -> bind_param('i', $_POST['vacatureid']);

    $stmt -> execute();
    $stmt -> close();
    echo "status changed.";
}