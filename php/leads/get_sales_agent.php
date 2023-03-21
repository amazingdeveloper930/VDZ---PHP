<?php

require( '../../common/connection.php');
require('../../common/global.php');


if ($stmt = $con->prepare('SELECT sales_agents.id, sales_agents.name, sales_agents.color FROM sales_agents')) {

$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();

}
$result_array = [];
while ($row = $result->fetch_assoc()) {

    $result_array[] = $row;
  
  }


echo json_encode($result_array);

?>