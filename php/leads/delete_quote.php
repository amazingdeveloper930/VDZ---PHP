<?php

session_start();


require( '../../common/connection.php');


if ($_POST['quote_id'] == '') {

	die ('Er ging iets mis bij het verwijderen.');

}



// We need to check if the account with that username exists.

if ($stmt = $con->prepare('DELETE FROM quote_lines WHERE quote_id = ?')) {

	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.

	$stmt->bind_param('s', $_POST['quote_id']);

	$stmt->execute();	


    $stmt = $con -> prepare('DELETE FROM quote_arbeids WHERE quote_id = ?');
    $stmt->bind_param('s', $_POST['quote_id']);

	$stmt->execute();

    $stmt = $con -> prepare('DELETE FROM quote_materiaals WHERE quote_id = ?');
    $stmt->bind_param('s', $_POST['quote_id']);

	$stmt->execute();



	if ($stmt = $con->prepare('DELETE FROM quote_chapters WHERE quote_id = ?')) {
        $stmt->bind_param('s', $_POST['quote_id']);
	    $stmt->execute();

        $stmt = $con -> prepare("SELECT * FROM quotes WHERE id = ?");
        $stmt->bind_param('s', $_POST['quote_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $pdf_file = $row['pdf_file'];
            if(isset($pdf_file) && $pdf_file != '' && file_exists('../../pdf/' . $pdf_file))
            {
                unlink('../../pdf/' . $pdf_file);
            }
            $file_name = $row['file_path'];
            if($file_name != '' && $file_name != null)
  	            unlink('../../upload/' . $file_name);
        }
        $stmt = $con -> prepare("DELETE From quote_arbeids WHERE quote_id = ?");
        $stmt -> bind_param('i', $_POST['quote_id']);
        $stmt -> execute();

        $stmt = $con -> prepare("DELETE From quote_materiaals WHERE quote_id = ?");
        $stmt -> bind_param('i', $_POST['quote_id']);
        $stmt -> execute();


        if ($stmt = $con->prepare('DELETE FROM quotes WHERE id = ?')){
            $stmt->bind_param('s', $_POST['quote_id']);
            $stmt->execute();
            echo "Offerte verwijderd.";

        }
    }
    else{
        echo 'Database fout!';
    }

} else {

	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.

	echo 'Database fout!';

}

$con->close();

?>