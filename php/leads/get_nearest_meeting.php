<?php

require( '../../common/connection.php');

$query = "SELECT * 
            FROM sales_meeting 
            WHERE date > NOW() AND contact = ?
            ORDER BY date, time_from DESC LIMIT 1
        ";
if($stmt = $con->prepare($query)){
    $stmt->bind_param('i', $_POST['leadid']);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();
    $result_array = [];
    while ($row = $result->fetch_assoc())
    {
        $row += ['date_formated' => ( (new DateTime($row['date'])) -> format('d-m-Y'))];
        $result_array[] = $row;
    }
    echo json_encode($result_array);
}

