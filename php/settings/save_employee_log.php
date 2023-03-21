<?php

require( '../../common/connection.php');

if($_POST['employee_id'])
{
    $stmt = $con -> prepare('INSERT employee_working_schedule (option, date_from, date_to, text, user_id, employee_id) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt -> bind_param('ssssii', $_POST['option'], $_POST['date_from'], $_POST['date_to'], $_POST['text'], $_POST['user_id'], $_POST['employee_id']);
    $stmt -> execute();
    $insert_id = $stmt -> insert_id;
    
    $stmt = $con -> prepare('SELECT EWS.*, A.username FROM employee_working_schedule EWS LEFT JOIN accounts A ON EWS.user_id = A.id WHERE EWS.id = ?');
    $stmt -> bind_param('i', $insert_id);
    $stmt -> execute();

    $result_array = [];
    $result = $stmt -> get_result();
    while ($row = $result->fetch_assoc()) {
        $result_array['new_one'] = $row;
    }

    $stmt = $con -> prepare('SELECT * FROM employee_working_schedule WHERE date_from <= ? AND id != ? AND employee_id = ? ORDER BY date_from DESC LIMIT 1');
    $stmt -> bind_param('sii', $_POST['date_from'], $insert_id, $_POST['employee_id'] );
    $stmt -> execute();

    $result = $stmt -> get_result();
    $result_array['latest_one'] = [];
    while ($row = $result->fetch_assoc()) {
        $result_array['latest_one'] = $row;
    }
    $result_array['message'] = 'Logboek opgeslagen.';
    echo json_encode($result_array);
    

}
else{

}