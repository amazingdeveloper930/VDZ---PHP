<?php

// Change this to your connection info.

require( '../../common/connection.php');



if ($stmt = $con->prepare('SELECT * FROM employees WHERE id = ?')) {



$stmt->bind_param('s', $_POST['employeeid']);

$stmt->execute();

// Store the result so we can check if the account exists in the database.

$result = $stmt->get_result();



}



while ($row = $result->fetch_assoc()) {





    $stmt = $con -> prepare("SELECT COUNT(id) as member_count FROM employees WHERE teamleader = ?");
    $stmt->bind_param('s', $_POST['employeeid']);

    $stmt->execute();

    // Store the result so we can check if the account exists in the database.

    $result_member = $stmt->get_result();
    while ($row_member = $result_member->fetch_assoc()) {
        $row['member_count'] = $row_member['member_count'];
    }
   

    $stmt = $con -> prepare('SELECT EWD.*, A.username FROM employee_working_schedule EWD LEFT JOIN accounts A ON EWD.user_id = A.id WHERE EWD.employee_id = ? ORDER BY EWD.date_from DESC');
    $stmt -> bind_param('i', $_POST['employeeid']);
    $stmt -> execute();
    $result_ewd = $stmt -> get_result();
    $row['working_schedule'] = [];
    while($row_ewd = $result_ewd -> fetch_assoc()) {
        $row['working_schedule'] []= $row_ewd;
    }

    $result_array[] = $row;

}

echo json_encode($result_array);





?>