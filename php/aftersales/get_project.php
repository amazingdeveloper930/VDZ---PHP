<?php

require( '../../common/connection.php');
require('../../common/global.php');


if ($stmt = $con->prepare('SELECT P.contact_id, P.project_number, P.m_status4, P.m_status5, P.m_status6,  P.m_status7, P.sale_date, C.name, C.city, C.address, C.email, C.phone, PTC.ticket_count FROM projects P LEFT JOIN contacts C ON P.contact_id = C.id LEFT JOIN (SELECT count(id) AS ticket_count, contact_id FROM projects_tickets WHERE status = "OPENED" GROUP BY contact_id ) PTC ON C.id = PTC.contact_id Where P.as_status = 1 AND P.contact_id = ? LIMIT 1')) {

$stmt->bind_param('s', $_POST['contactid']);
$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();




while ($row = $result->fetch_assoc()) {

  $row['timer_ticket'] = '';
  $stmt_new = $con -> prepare('SELECT PT.datum FROM projects_tickets PT WHERE PT.contact_id = ? ORDER BY PT.datum ASC LIMIT 1');
  $stmt_new->bind_param('s', $_POST['contactid']);
  $stmt_new->execute();
  $result_new = $stmt_new->get_result();
  if($row_new = $result_new -> fetch_assoc()) {
    $row['timer_ticket'] = getTicketTimer('', 'CLOSED');
  }

  $stmt_new = $con -> prepare('SELECT PT.datum FROM projects_tickets PT WHERE PT.contact_id = ? AND PT.status="OPENED" ORDER BY PT.datum ASC LIMIT 1');
  $stmt_new->bind_param('s', $_POST['contactid']);
  $stmt_new->execute();
  $result_new = $stmt_new->get_result();
  while($row_new = $result_new -> fetch_assoc()) {
    $row['timer_ticket'] = getTicketTimer($row_new['datum'], 'OPENED');
  }
  

for($index = 4; $index <=7 ; $index ++)
  $row['timer_' . $index] = getSaleTimer($row['sale_date'], $row['m_status' . $index]);
  if(! isset($row['ticket_count']))
  {
    $row['ticket_count'] = 0;
  }
  $result_array[] = $row;

}
echo json_encode($result_array);
}

?>