<?php

session_start();


require( '../../common/connection.php');


if ($_POST['contact'] == '') {

	die ('Er ging iets mis bij het verwijderen.');

}



// We need to check if the account with that username exists.
///////////////////////
$stmt1 = $con -> prepare('DELETE FROM contact_log WHERE contact_id = ?');
$stmt1->bind_param('s', $_POST['contact']);
$stmt1->execute();

$stmt1 = $con -> prepare('DELETE FROM sales_meeting WHERE contact = ?');
$stmt1->bind_param('s', $_POST['contact']);
$stmt1->execute();

$stmt1 = $con -> prepare('SELECT * FROM quotes WHERE contact_id = ?');
$stmt1->bind_param('s', $_POST['contact']);
$stmt1->execute();
$result1 = $stmt1 -> get_result();
while ($row1 = $result1->fetch_assoc()) {

	$quote_id = $row1['id'];

	$stmt2 = $con->prepare('DELETE FROM quote_lines WHERE quote_id = ?');

	$stmt2->bind_param('s', $quote_id);

		$stmt2->execute();	

	if ($stmt2 = $con->prepare('DELETE FROM quote_chapters WHERE quote_id = ?')) {
		$stmt2->bind_param('s', $quote_id);
		$stmt2->execute();

		$stmt2 = $con -> prepare("SELECT * FROM quotes WHERE id = ?");
		$stmt2->bind_param('s', $quote_id);
		$stmt2->execute();
		$result2 = $stmt2->get_result();
		while ($row2 = $result2->fetch_assoc()) {
			$pdf_file = $row2['pdf_file'];
			if(isset($pdf_file) && $pdf_file != '' && file_exists('../../pdf/' . $pdf_file))
			{
				unlink('../../pdf/' . $pdf_file);
			}
		}

		if ($stmt2 = $con->prepare('DELETE FROM quotes WHERE id = ?')){
			$stmt2->bind_param('s', $quote_id);
			$stmt2->execute();

		}
	}
	else{
		echo 'Database fout!';
	}
}





////////////////////////////////////////////////////////////////



if ($stmt = $con->prepare('DELETE FROM contacts WHERE id = ?')) {

	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.

	$stmt->bind_param('s', $_POST['contact']);

	$stmt->execute();	

	$stmt->close();

	

	echo "Lead verwijderd.";

	

} else {

	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.

	echo 'Database fout!';

}

$con->close();

?>