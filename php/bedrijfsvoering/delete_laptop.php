<?php 

require( '../../common/connection.php');

if ($stmt = $con->prepare('DELETE FROM laptop WHERE id = ?')) {
    $stmt -> bind_param('i', $_POST['laptopid']);
    $stmt -> execute();
    $stmt -> close();

	

	echo "Laptop verwijderd.";
}