<?php

require( '../../common/connection.php');
require('../../common/global.php');


$stmt1 = $con -> prepare('SELECT text FROM quote_text_default WHERE meta like ?');
$mode = 'QUOTE_INTRO';
$stmt1 -> bind_param('s', $mode);
$stmt1->execute();
$result1 = $stmt1->get_result();
$intro_text = '';
while ($row = $result1->fetch_assoc()) {

  $intro_text = $row['text'];

}


if ($stmt = $con->prepare('SELECT quotes.*, accounts.username FROM quotes LEFT JOIN accounts  ON quotes.account_id = accounts.id WHERE quotes.id = ?')) {

$stmt->bind_param('s', $_POST['quote_id']);
$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();

}
while ($row = $result->fetch_assoc()) {

    $dt = new DateTime($row['quote_date']);
    $row['qdate']   =   $dt->format('d-m-Y');  
    $row['qtime']   =   $dt->format('H:i'); 
    if($row['intro'] == null)
      $row['intro'] = $intro_text;
    $result_array[] = $row;
  
  }





echo json_encode($result_array);

?>