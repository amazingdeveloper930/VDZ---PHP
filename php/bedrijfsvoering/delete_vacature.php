<?php 

require( '../../common/connection.php');

if ($stmt = $con->prepare('DELETE FROM vacatures WHERE ID = ?')) {
    $stmt -> bind_param('i', $_POST['vacatureid']);
    $stmt -> execute();
    $stmt -> close();

	echo "Vacature verwijderd.";
}