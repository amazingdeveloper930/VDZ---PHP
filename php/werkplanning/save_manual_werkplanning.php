<?php

require( '../../common/connection.php');

$sort_order = 0;
$insert_id = NULL;
$stmt = $con -> prepare('SELECT MAX(sort_order) AS MAXORDER FROM werkplanning ');
// $stmt -> bind_param('i', $_POST['contact_id']);
$stmt -> execute();

$result = $stmt -> get_result();

while($row = $result -> fetch_assoc())
{
    $sort_order = $row['MAXORDER'];
}
$sort_order ++;

$flag = 0;
$stmt = $con -> prepare('SELECT * FROM werkplanning WHERE contact_id = ? ORDER BY certain_year, certain_week');
$stmt -> bind_param('i', $_POST['contact_id']);
$stmt -> execute();
$result = $stmt -> get_result();

while($row = $result -> fetch_assoc())
{
    if((($row['certain_year'] < $_POST['certain_year']) || (($row['certain_year'] == $_POST['certain_year']) && ($row['certain_week'] <= $_POST['certain_week']))) &&
    (empty($row['end_year']) || (($row['end_year'] > $_POST['certain_year']) || (($row['end_year'] == $_POST['certain_year']) && ($row['end_week'] > $_POST['certain_week']))))
    )
    {
        $flag = 1;
        $insert_id = $row['id'];
        break;
    }

    if(($row['certain_year'] > $_POST['certain_year']) || (($row['certain_year'] == $_POST['certain_year']) && ($row['certain_week'] > $_POST['certain_week'])))
    {
        $flag = 2;
        $insert_id = $row['id'];
        break;
    }
}


if($flag == 0)
{
    $name = "Handmatig";
    $start = 0;
    $mode = 'voor';
    $length = 1;
    $stmt = $con -> prepare('INSERT INTO werkplanning (name, contact_id,  certain_week, certain_year,  sort_order) VALUES (?, ?, ?, ?, ?)');
    $stmt -> bind_param('siiii', $name, $_POST['contact_id'], $_POST['certain_week'], $_POST['certain_year'], $sort_order);
    $stmt -> execute();
    
    $insert_id = $stmt -> insert_id;
}
else if($flag == 2)
{
    $stmt = $con -> prepare('UPDATE werkplanning SET certain_year = ?, certain_week = ? WHERE id = ?');
    $stmt -> bind_param('iii',  $_POST['certain_year'], $_POST['certain_week'], $insert_id);
    $stmt -> execute();
}


echo json_encode(['status' => 'success', 'werkplanning_id' => $insert_id]);