<?php

require( '../../common/connection.php');
require('../../common/global.php');

if ($stmt = $con->prepare('SELECT * FROM contact_log WHERE entry_title = "lead" AND contact_id = ? ORDER BY entry_date DESC LIMIT 1')) {
  $stmt->bind_param('i', $_POST['contactid']);
  $stmt->execute();
  // Store the result so we can check if the account exists in the database.
  $result = $stmt->get_result();
}
$result_array = [];
while ($row = $result->fetch_assoc()) {
  $date = $row['entry_date'];
  $timer = getTimer($date);
  $row += ['timer' => $timer];
  $result_array[] = $row;
}

echo json_encode($result_array);


?>