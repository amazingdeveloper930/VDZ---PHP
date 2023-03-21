<?php 

require( '../../common/connection.php');

if ($stmt = $con->prepare('DELETE FROM vehicle WHERE id = ?')) {
    $stmt -> bind_param('i', $_POST['vehicleid']);
    $stmt -> execute();
    $stmt -> close();

	

	echo "Vehicle verwijderd.";
}