<?php

require( '../../common/connection.php');
require('../../common/global.php');


if ($stmt = $con->prepare('SELECT C.name, C.city, C.address, C.email, C.phone, C.source, C.c_status, CL.entry_date, P.project_number, P.convert_date, P.startdatum, P.opleverdatum, P.plaatsing FROM contacts C LEFT JOIN contact_log CL ON C.id = CL.contact_id LEFT JOIN projects P ON C.id = P.contact_id Where C.id = ? ORDER BY CL.entry_date DESC LIMIT 1')) {

$stmt->bind_param('s', $_POST['contactid']);
$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();

}


while ($row = $result->fetch_assoc()) {

  $date = $row['entry_date'];
  $timer = getTimer($date);
  $row += ['timer' => $timer];
  $result_array[] = $row;

}
echo json_encode($result_array);

?>