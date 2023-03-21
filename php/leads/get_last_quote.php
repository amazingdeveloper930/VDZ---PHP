<?php

require( '../../common/connection.php');
require('../../common/global.php');


if ($stmt = $con->prepare('SELECT quotes.*, accounts.username  FROM quotes LEFT JOIN accounts  ON quotes.account_id = accounts.id WHERE quotes.contact_id = ? ORDER BY quotes.id DESC LIMIT 1')) {

$stmt->bind_param('s', $_POST['contact_id']);
$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();

}
$result_array = [];
while ($row = $result->fetch_assoc()) {

    $dt = new DateTime($row['quote_date']);
    $row['qdate']   =   $dt->format('d-m-Y');  
    $row['qtime']   =   $dt->format('H:i');  
    $result_array[] = $row;
  
  }


echo json_encode($result_array);

?>