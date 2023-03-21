<?php

require( '../../common/connection.php');
require( '../../common/global.php');

$tag_id = '';
$mode = '';
if(isset($_POST['tag_id']))
{
    $stmt = $con -> prepare('UPDATE tags SET name = ?, type = ? WHERE id = ?');
    $stmt -> bind_param("ssi", $_POST['name'], $_POST['type'], $_POST['tag_id'],);
    $stmt -> execute();
    $tag_id = $_POST['tag_id'];
    $mode = "UPDATE";
}
else{
    $stmt = $con -> prepare('SELECT sort_order FROM tags ORDER BY sort_order DESC LIMIT 1');
    $stmt -> execute();
    $result = $stmt -> get_result();
    $sort_order = 1;
    while ($row = $result->fetch_assoc()) {
        $sort_order = $row['sort_order'] + 1;
    }

    $stmt = $con -> prepare('INSERT INTO tags ( name, type, sort_order ) VALUES (?, ?, ?)');
    $stmt -> bind_param('ssi', $_POST['name'], $_POST['type'], $sort_order);
    $stmt -> execute();
    $tag_id = $stmt -> insert_id;
    $mode = "INSERT";
}

echo json_encode(['status' => 'success', 'item' => ['id' => $tag_id, 'type' => $_POST['type'], 'name' => $_POST['name'], 'mode' => $mode]]);