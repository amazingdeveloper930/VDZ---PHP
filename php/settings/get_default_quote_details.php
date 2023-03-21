<?php

require( '../../common/connection.php');
require('../../common/global.php');

$stmt1 = $con -> prepare('SELECT text FROM quote_text_default WHERE meta like ?');
$mode = 'QUOTE_INTRO';
$stmt1 -> bind_param('s', $mode);
$stmt1->execute();
$result1 = $stmt1->get_result();
$intro_text = '';
while ($row = $result1->fetch_assoc()) {

  $intro_text = $row['text'];

}

if ($stmt = $con->prepare('SELECT id as chapter_id, chapter_name FROM quote_chapters_default ORDER BY sort_order ASC')) {

    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();
    
    
    
    $result_array = [];
    $result1 = '';
    while ($row = $result->fetch_assoc()) {
    
      if($stmt1 = $con -> prepare('SELECT * FROM quote_lines_default WHERE chapter_id = ? ORDER BY sort_order ASC'))
      {
        $stmt1->bind_param('s', $row['chapter_id']);
        $stmt1->execute();
        // Store the result so we can check if the account exists in the database.
        $result1 = $stmt1->get_result();
      }
      $line_data = [];
      while ($row1 = $result1->fetch_assoc()) {
          $line_data []= $row1;
      }
      $row += ['line_data' => $line_data];
      $row += ['intro' => $intro_text];
      
      $result_array []= $row;

    }
    echo json_encode($result_array);
  }
    ?>