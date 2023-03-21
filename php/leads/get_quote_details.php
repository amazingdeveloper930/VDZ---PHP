<?php

require( '../../common/connection.php');
require('../../common/global.php');


$stmt1 = $con -> prepare('SELECT text FROM quote_text_default WHERE meta like ?');
$mode = 'QUOTE_INTRO';
$stmt1 -> bind_param('s', $mode);
$stmt1->execute();
$result1 = $stmt1->get_result();
$intro_text = '';
$file_path = '';
while ($row = $result1->fetch_assoc()) {

  $intro_text = $row['text'];

}

$reference = '';
$factor = '';
$rate = '';
$inkoop = '';
$kosten = '';
$arbeid_pdf = '';
$materiaal_pdf = '';
$version = 1;


$stmt1 = $con -> prepare('SELECT * FROM quotes WHERE id = ?');

$stmt1 -> bind_param('i', $_POST['quote_id']);
$stmt1->execute();
$result1 = $stmt1->get_result();

while ($row = $result1->fetch_assoc()) {

  if($row['intro'] != null)
    $intro_text = $row['intro'];
  if($row['reference'] != null)
    $reference = $row['reference'];
  if($row['factor'] != null)
    $factor = $row['factor'];
  if($row['rate'] != null)
    $rate = $row['rate'];
  if($row['inkoop'] != null)
    $inkoop = $row['inkoop'];
  if($row['kosten'] != null)
    $kosten = $row['kosten'];
  $version = $row['quote_version'];
  $arbeid_pdf = $row['arbeid_pdf'];
  $materiaal_pdf = $row['materiaal_pdf'];
  $file_path = $row['file_path'];
}



if ($stmt = $con->prepare('SELECT id as chapter_id, chapter_name, default_chapter_id, chapter_factor FROM quote_chapters WHERE quote_id = ? ORDER BY sort_order ASC')) {

    $stmt->bind_param('s', $_POST['quote_id']);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();
    
    }
    
    $result_array = [];
    while ($row = $result->fetch_assoc()) {
    
      if($stmt1 = $con -> prepare('SELECT QL.*, QL.unit AS line_unit, QC.default_chapter_id  FROM quote_lines QL LEFT JOIN quote_chapters QC ON QL.chapter_id = QC.id WHERE QL.quote_id = ? AND QL.chapter_id = ? ORDER BY QL.sort_order ASC'))
      {
        $stmt1->bind_param('ss', $_POST['quote_id'], $row['chapter_id']);
        $stmt1->execute();
        // Store the result so we can check if the account exists in the database.
        $result1 = $stmt1->get_result();
      }
      $line_data = [];
      while ($row1 = $result1->fetch_assoc()) {

        $stmt2 = $con -> prepare('SELECT * FROM quote_arbeids WHERE chapter_id = ? AND line_id = ? ORDER BY sort_order ASC');
        $stmt2->bind_param('ss', $row['chapter_id'], $row1['id']);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $arbeid_data = [];
        while($row2 = $result2->fetch_assoc()){
          $arbeid_data []= $row2;
        }

        $row1['arbeid_data'] = $arbeid_data;

        $stmt2 = $con -> prepare('SELECT * FROM quote_materiaals WHERE chapter_id = ? AND line_id = ? ORDER BY sort_order ASC');
        $stmt2->bind_param('ss', $row['chapter_id'], $row1['id']);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $materiaal_data = [];
        while($row2 = $result2->fetch_assoc()){
          $materiaal_data []= $row2;
        }
        $row1['materiaal_data'] = $materiaal_data;

        $line_data []= $row1;
      }
      $row += ['line_data' => $line_data];
      // $row += ['intro' => $intro_text];
      $result_array[] = $row;
    }
    echo json_encode(["result" =>$result_array, "intro" => $intro_text, "reference" => $reference, 'factor' => $factor, 'kosten' => $kosten, 'rate' => $rate, 'inkoop' => $inkoop, 'materiaal_pdf' => $materiaal_pdf, 'arbeid_pdf' => $arbeid_pdf, 'file_path' => $file_path, 'version' => $version]);
    
    ?>
