<?php

require( '../../common/connection.php');

$st_id = $_POST['st_id'];
if($_POST['st_id'] == 0)
{
    $stmt = $con -> prepare('INSERT INTO special_tasks (contact_id, text, price, vat, price_inc, option, date) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt -> bind_param('issssss', $_POST['contact_id'], $_POST['text'], $_POST['price'], $_POST['vat'], $_POST['price_inc'], $_POST['option'], $_POST['date']);
    $stmt -> execute();
    $st_id = $stmt -> insert_id;


}
else{
    $stmt = $con -> prepare('UPDATE special_tasks SET text = ?, price = ?, vat = ?, price_inc = ?, option = ?, date = ? WHERE id = ?');
    $stmt -> bind_param('ssssssi', $_POST['text'], $_POST['price'], $_POST['vat'], $_POST['price_inc'], $_POST['option'], $_POST['date'], $_POST['st_id']);
    $stmt -> execute();
}

$stmt = $con -> prepare('SELECT * FROM special_tasks WHERE id = ?');
$stmt -> bind_param('i', $st_id);
$stmt -> execute();
$result = $stmt->get_result();
$result_array = [];
while($row = $result -> fetch_assoc())
  {
      if($_POST['st_id'] == 0)
        $row['mode'] = 'CREATE';
      else 
        $row['mode'] = 'UPDATE';
    $result_array = $row;
    
  } 

  echo json_encode($result_array);
  $stmt -> close();
  $con -> close();