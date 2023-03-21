<?php

require( '../../common/connection.php');
require('../../common/global.php');


if ($stmt = $con->prepare('SELECT P.contact_id, P.m_status1, P.m_status2, P.m_status3, P.startdatum, C.name, C.city, C.address, C.email, C.phone FROM projects P LEFT JOIN contacts C ON P.contact_id = C.id  Where  P.contact_id = ? LIMIT 1')) {

$stmt->bind_param('s', $_POST['contactid']);
$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();




while ($row = $result->fetch_assoc()) {

//   $date = $row['entry_date'];
//   $timer = getTimer($date);
//   $row += ['timer' => $timer];

  $row['timer_1'] = getProjectTimer($row['startdatum'], $row['m_status1']);
  $row['timer_2'] = getProjectTimer($row['startdatum'], $row['m_status2']);
  $row['timer_3'] = getProjectTimer($row['startdatum'], $row['m_status3']);

  $result_array[] = $row;

}
echo json_encode($result_array);
}

?>