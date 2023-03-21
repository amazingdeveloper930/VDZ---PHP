<?php

require( '../../common/connection.php');

$id = "";
$error = '';
if(isset($_POST['id']) && $_POST['id']!= '') {
    $id = $_POST['id'];
    if($stmt = $con -> prepare('UPDATE werkplanning SET name = ?, length = ?, start = ?, mode = ?, hour = ? WHERE id = ?'))
    {
        $stmt -> bind_param('siisii', $_POST['name'], $_POST['length'], $_POST['start'], $_POST['mode'], $_POST['hour'], $id);
        $stmt->execute();

        $stmt = $con -> prepare("DELETE FROM werkplanning_activity WHERE block_id in (SELECT id FROM werkplanning_block WHERE werkplanning_id = ? AND week > ?)");
        $stmt -> bind_param('ii', $id, $_POST['length']);
        $stmt->execute();

        $stmt = $con -> prepare("DELETE FROM werkplanning_block WHERE werkplanning_id = ? AND week > ?");
        $stmt -> bind_param('ii', $id, $_POST['length']);
        $stmt->execute();
    }
    else{
        $error = 'Database fout!';
    }
}
else{
    if($stmt = $con -> prepare('SELECT MAX(sort_order) AS x_sort_order FROM werkplanning'))
    {
        $stmt -> execute();
        $x_sort_order = 0;
        $result = $stmt -> get_result();
        while ($row = $result->fetch_assoc()) {
            $x_sort_order = $row['x_sort_order'];
        }
        $x_sort_order ++;
        if($stmt = $con -> prepare('INSERT INTO werkplanning (name, length, start, mode, hour, sort_order) VALUES (?, ?, ?, ?, ?, ?)'))
        {
            $stmt -> bind_param('siisii', $_POST['name'], $_POST['length'], $_POST['start'], $_POST['mode'], $_POST['hour'], $x_sort_order);
            $stmt -> execute();
            $id = $stmt -> insert_id;
        }
        else{
            $error = 'Database fout!';
        }
       
    }
    else{
        $error = 'Database fout!';
    }
    
}
if($error == '')
{
    echo json_encode(['status' => 'success', 'werkplanning_id' => $id]);
}
else{
    echo json_encode(['status' => 'error', 'message' => $error]);
}
