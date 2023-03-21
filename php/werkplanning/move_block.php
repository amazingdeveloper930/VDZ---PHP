<?php 
require( '../../common/connection.php');

// $max_index = 0;

// $stmt = $con -> prepare('SELECT MAX(id) AS max_id FROM werkplanning_block');

// $stmt -> execute();

// $result = $stmt -> get_result();

// while($row = $result -> fetch_assoc())
// {
//     $max_index = $row['max_id'];
// }

// $max_index = $max_index + 1;

$stmt = $con -> prepare("SELECT * FROM werkplanning_block WHERE id = ?");
$stmt -> bind_param('i', $_POST['block_id']);
$stmt -> execute();
$result = $stmt -> get_result();
$datum_end = NULL;
$datum = NULL;

while($row = $result -> fetch_assoc())
{
    $datum_end = $row['datum_end'];
    $datum = $row['datum'];
}


if(isset($datum_end))
{
    $date1=date_create($datum);
    $date2=date_create($_POST['datum']);
    $diff=date_diff($date1, $date2);
    $day_count = $diff->format("%R%a");

    $datum_end = date('Y-m-d', strtotime($datum_end. ' ' . $day_count . ' days'));;
}

$stmt = $con -> prepare('UPDATE werkplanning_block SET  day = ?, datum = ?, datum_end = ? WHERE id = ?');
$stmt -> bind_param('issi', $_POST['day'], $_POST['datum'], $datum_end, $_POST['block_id']);
$stmt -> execute();

// $stmt = $con -> prepare('UPDATE werkplanning_activity SET block_id = ? WHERE block_id = ?');
// $stmt -> bind_param('ii', $max_index,  $_POST['block_id']);
// $stmt -> execute();

// $stmt = $con -> prepare('UPDATE werkplanning_medewerker SET block_id = ? WHERE block_id = ?');
// $stmt -> bind_param('ii', $max_index,  $_POST['block_id']);
// $stmt -> execute();

echo json_encode(['message' => 'success']);
