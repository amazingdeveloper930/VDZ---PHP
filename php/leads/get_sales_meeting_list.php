<?php

require( '../../common/connection.php');
require('../../common/global.php');


if ($stmt = $con->prepare('SELECT sales_meeting.id, sales_meeting.date, sales_meeting.time_from, sales_meeting.time_untill, sales_meeting.sales_agent, sales_agents.color FROM sales_meeting LEFT JOIN sales_agents ON sales_meeting.sales_agent = sales_agents.id')) {

$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();

}
$result_array = [];
while ($row = $result->fetch_assoc()) {
    $dt = strtotime($row['date']);
    $row['month'] = getdate($dt)['mon'];
    $row['day'] = getdate($dt)['mday'];
    $result_array[] = $row;
  
  }


echo json_encode($result_array);

?>