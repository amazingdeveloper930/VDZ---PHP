<?php

require( '../../common/connection.php');


$lineData = $_POST;

$chapter_id = $_POST['default_chapter_id'];
$sort_order = 1;

$stmt = $con -> prepare('SELECT sort_order FROM offerte_lines_default WHERE chapter_id = ? ORDER BY sort_order DESC LIMIT 1');
$stmt-> bind_param('i' , $chapter_id);
$stmt -> execute();
$result = $stmt -> get_result();

while($row = $result -> fetch_assoc())
{
    $sort_order = $row['sort_order'] + 1;
}


$stmt = $con -> prepare('INSERT INTO offerte_lines_default  (line_title, line_description, line_am_option, line_unit, line_total, arbeid, materiaal_total, chapter_id, sort_order,  standard_tag_id, fase_tag_id) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            
$stmt -> bind_param('ssssssssiii', $lineData['line_title'], $lineData['line_descr'], $lineData['line_am_option'], $lineData['unit'], $lineData['line_total'], $lineData['arbeid'], $lineData['materiaal_total'], $chapter_id, $sort_order, $lineData['standard_tag_id'], $lineData['fase_tag_id']);
$stmt -> execute();

$line_id = $stmt -> insert_id;
$stmt -> close();


$index = 0;
if(isset($lineData['materiaal_data']) && $lineData['line_am_option'] == 'ja')
foreach($lineData['materiaal_data'] as $materiaalData)
{
    $stmt = $con -> prepare('INSERT INTO offerte_materiaals_default (materiaal_title, stuks, price, chapter_id, line_id, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt -> bind_param('ssssss', $materiaalData['materiaal_title'],  $materiaalData['stuks'], $materiaalData['price'], $chapter_id, $line_id, $index);
    $stmt -> execute();
    $index ++;
    $stmt -> close();
}


$index = 0;
if(isset($lineData['arbeid_data']) && $lineData['line_am_option'] == 'ja')
foreach($lineData['arbeid_data'] as $arbeidData)
{
    $stmt = $con -> prepare('INSERT INTO offerte_arbeids_default (arbeid_title, quantity, chapter_id, line_id, sort_order) VALUES (?, ?, ?, ?, ?)');
    $stmt -> bind_param('sssss', $arbeidData['arbeid_title'],  $arbeidData['quantity'], $chapter_id, $line_id, $index);
    $stmt -> execute();
    $index ++;
    $stmt -> close();
}

echo json_encode(['message' => 'Regel toegevoegd aan standaard regels']);
