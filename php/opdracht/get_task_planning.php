<?php

require( '../../common/connection.php');
require( '../../common/global.php');

$stmt = $con -> prepare('SELECT * FROM project_planning WHERE contact_id = ? AND id = ?');
$stmt -> bind_param('ii', $_POST['contact_id'], $_POST['plan_id']);
$stmt -> execute();
$result = $stmt -> get_result();
$result_array = [];
while ($row = $result->fetch_assoc()) {
    $row['color_widget'] = '<span class="new badge pt-' . $row['color']. '" data-badge-caption=""></span>';
    $result_array = $row;
}

echo json_encode( $result_array);