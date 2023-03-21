<?php 

require( '../../common/connection.php');

if ($stmt = $con->prepare('DELETE FROM materiaal WHERE id = ?')) {
    $stmt -> bind_param('i', $_POST['materiaalid']);
    $stmt -> execute();
    $stmt -> close();

	

	echo "Materiaal verwijderd.";
}