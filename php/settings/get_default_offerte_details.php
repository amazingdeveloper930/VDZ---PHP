<?php

require( '../../common/connection.php');
require('../../common/global.php');

$factor = "0,0";
$rate = "0,00";
$inkoop = "0,00";
$kosten = "0,00";

$version = 1;
if(isset($_POST['version']))
  $version = $_POST['version'];


$stmt = $con -> prepare('SELECT * from offerte_default  WHERE version = ? limit 1');
$stmt -> bind_param('i', $version);
$stmt -> execute();
$result = $stmt -> get_result();
while ($row = $result->fetch_assoc()) {
  $factor = $row['factor'];
  $rate = $row['rate'];
  $inkoop = $row['inkoop'];
  $kosten = $row['kosten'];
}

if ($stmt = $con->prepare('SELECT id as chapter_id, chapter_title FROM offerte_chapters_default WHERE version = ? ORDER BY sort_order ASC')) {
    $stmt -> bind_param('i', $version);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();
    
    
    
    $result_array = [];
    $result1 = '';
    while ($row = $result->fetch_assoc()) {
    
      if($stmt1 = $con -> prepare('SELECT OLD.*, OLD.chapter_id  AS default_chapter_id FROM offerte_lines_default OLD WHERE chapter_id = ? ORDER BY sort_order ASC'))
      {
        $stmt1->bind_param('s', $row['chapter_id']);
        $stmt1->execute();
        // Store the result so we can check if the account exists in the database.
        $result1 = $stmt1->get_result();
      }
      $line_data = [];
      while ($row1 = $result1->fetch_assoc()) {
          $stmt2 = $con -> prepare('SELECT * FROM offerte_materiaals_default WHERE chapter_id = ? AND line_id = ? ORDER BY sort_order ASC');
          $stmt2->bind_param('ss', $row['chapter_id'], $row1['id']);
          $stmt2->execute();
          $result2 = $stmt2->get_result();
          $materiaal_data = [];
          while($row2 = $result2->fetch_assoc()){
            $materiaal_data []= $row2;
          }
          $row1['materiaal_data'] = $materiaal_data;



          $stmt2 = $con -> prepare('SELECT * FROM offerte_arbeids_default WHERE chapter_id = ? AND line_id = ? ORDER BY sort_order ASC');
          $stmt2->bind_param('ss', $row['chapter_id'], $row1['id']);
          $stmt2->execute();
          $result2 = $stmt2->get_result();
          $arbeid_data = [];
          while($row2 = $result2->fetch_assoc()){
            $arbeid_data []= $row2;
          }
          $row1['arbeid_data'] = $arbeid_data;


          $line_data []= $row1;
      }
      $row['line_data'] = $line_data;
      $result_array []= $row;

    }

    $result_item = [];
    $result_item['chapter_list'] = $result_array;
    $result_item['factor'] = $factor;
    $result_item['rate'] = $rate;
    $result_item['inkoop'] = $inkoop;
    $result_item['kosten'] = $kosten;



    $stmt1 = $con -> prepare('SELECT text FROM quote_text_default WHERE meta like ?');
    $mode = 'QUOTE_INTRO';
    $stmt1 -> bind_param('s', $mode);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $intro_text = '';
    while ($row = $result1->fetch_assoc()) {

      $intro_text = $row['text'];

    }

    $result_item['intro']= $intro_text;
    
    $tags = [];
    $stmt = $con -> prepare('SELECT * FROM tags ORDER BY sort_order ASC');
    $stmt -> execute();
    $result = $stmt -> get_result();
    while($row = $result -> fetch_assoc())
    {
       $tags []= $row;
    }
    $result_item['tags'] = $tags;
    echo json_encode($result_item);
  }
    ?>