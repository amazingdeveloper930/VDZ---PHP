<?php

require( '../../common/connection.php');

$version = 1;
if(isset($_POST['version']))
    $version = $_POST['version'];
$version = (int)$version;

$other_version = 2;
if($version == 2)
    $other_version = 1;

$stmt = $con -> prepare('DELETE FROM offerte_materiaals_default WHERE chapter_id NOT IN (SELECT id FROM offerte_chapters_default WHERE version = ?)');
$stmt -> bind_param('i', $other_version);
$stmt->execute();

$stmt = $con -> prepare('DELETE FROM offerte_arbeids_default WHERE chapter_id NOT IN (SELECT id FROM offerte_chapters_default WHERE version = ?)');
$stmt -> bind_param('i', $other_version);
$stmt->execute();

$stmt = $con -> prepare('DELETE FROM offerte_default WHERE version != ?');
$stmt -> bind_param('i', $other_version);
$stmt->execute();

$index = 1;
$chapter_IDs = [];
$line_IDs = [];

$stmt = $con -> prepare('INSERT INTO offerte_default (factor, rate, inkoop, kosten, version) VALUES (?, ?, ?, ?, ?)'); 
$stmt -> bind_param('ssssi', $_POST['factor'], $_POST['rate'], $_POST['inkoop'],  $_POST['kosten'], $version);
$stmt -> execute();
$stmt -> close();

if(isset($_POST['chapterData']))
foreach($_POST['chapterData'] as $chapterData)
{
    $chapter_id = '';
    if(isset($chapterData['id']))
    {
        $stmt = $con -> prepare('UPDATE offerte_chapters_default SET chapter_title = ?, sort_order = ? WHERE id = ?');
        $stmt->bind_param('sii', $chapterData['chapter_title'], $chapterData['sort_order'], $chapterData['id']);
        $stmt->execute();
        $stmt -> close();
        $chapter_IDs []= $chapterData['id'];
        $chapter_id = $chapterData['id'];
    }
    else{
        if($stmt = $con->prepare('INSERT INTO offerte_chapters_default (chapter_title, version, sort_order) VALUES (?, ?, ?)'))
        {
            $stmt->bind_param('sii', $chapterData['chapter_title'], $version, $chapterData['sort_order']);
            $stmt->execute();
            $chapter_id = $stmt -> insert_id;
            $chapter_IDs []= $chapter_id;
            $stmt -> close();
        }
        else{
            echo 'error';
        }
        
        
        
    }


    if(isset($chapterData['line_data']))
    foreach($chapterData['line_data'] as $lineData)
    {
        
        $line_id = '';
        if($lineData['standard_tag_id'] == '')
            $lineData['standard_tag_id'] = NULL;
        if($lineData['fase_tag_id'] == '')
            $lineData['fase_tag_id'] = NULL;

        if(isset($lineData['id']))
        {
            
            $stmt = $con -> prepare('UPDATE offerte_lines_default SET line_title = ?, line_description = ?, line_am_option = ?, line_unit = ?, line_total = ?, 	arbeid = ?, 	materiaal_total = ?, chapter_id = ?, sort_order = ?, line_profit = ?, standard_tag_id = ?, fase_tag_id = ?  WHERE id = ?');
            $stmt -> bind_param('ssssssssisiii', $lineData['line_title'], $lineData['line_description'], $lineData['line_am_option'], $lineData['line_unit'], $lineData['line_total'], $lineData['arbeid'], $lineData['materiaal_total'], $chapter_id, $lineData['sort_order'], $lineData['line_profit'], $lineData['standard_tag_id'], $lineData['fase_tag_id'], $lineData['id']);
            $stmt -> execute();
            $stmt -> close();
            $line_IDs []= $lineData['id'];
            $line_id = $lineData['id'];
        }
        else{
            
            $stmt = $con -> prepare('INSERT INTO offerte_lines_default  (line_title, line_description, line_am_option, line_unit, line_total, arbeid, materiaal_total, chapter_id, sort_order,  standard_tag_id, fase_tag_id) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            
            $stmt -> bind_param('ssssssssiii', $lineData['line_title'], $lineData['line_description'], $lineData['line_am_option'], $lineData['line_unit'], $lineData['line_total'], $lineData['arbeid'], $lineData['materiaal_total'], $chapter_id, $lineData['sort_order'], $lineData['standard_tag_id'], $lineData['fase_tag_id']);
            $stmt -> execute();
            
            $line_id = $stmt -> insert_id;
            $line_IDs []= $line_id;
            $stmt -> close();
        }

        $index = 1;
        if(isset($lineData['materiaal_data']) && $lineData['line_am_option'] == 'ja')
        foreach($lineData['materiaal_data'] as $materiaalData)
        {
            $stmt = $con -> prepare('INSERT INTO offerte_materiaals_default (materiaal_title, stuks, price, chapter_id, line_id, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt -> bind_param('ssssss', $materiaalData['materiaal_title'],  $materiaalData['stuks'], $materiaalData['price'], $chapter_id, $line_id, $index);
            $stmt -> execute();
            $index ++;
            $stmt -> close();
        }


        $index = 1;
        if(isset($lineData['arbeid_data']) && $lineData['line_am_option'] == 'ja')
        foreach($lineData['arbeid_data'] as $arbeidData)
        {
            $stmt = $con -> prepare('INSERT INTO offerte_arbeids_default (arbeid_title, quantity, chapter_id, line_id, sort_order) VALUES (?, ?, ?, ?, ?)');
            $stmt -> bind_param('sssss', $arbeidData['arbeid_title'],  $arbeidData['quantity'], $chapter_id, $line_id, $index);
            $stmt -> execute();
            $index ++;
            $stmt -> close();
        }

    }

    
}


$stmt = $con -> prepare('DELETE FROM offerte_lines_default WHERE chapter_id NOT IN (' . join($chapter_IDs, ', ') . ') AND chapter_id IN (SELECT id FROM offerte_chapters_default WHERE version = ?)');
$stmt -> bind_param('i', $version);
$stmt -> execute();
$stmt -> close();

$stmt =  $con -> prepare('DELETE FROM offerte_lines_default WHERE id NOT IN (' . join($line_IDs, ', ') . ') AND chapter_id IN (SELECT id FROM offerte_chapters_default WHERE version = ?)');
$stmt -> bind_param('i', $version);
$stmt -> execute();
$stmt -> close();


$stmt = $con -> prepare('DELETE FROM offerte_chapters_default WHERE id NOT IN (' . join($chapter_IDs, ', ') . ') AND version = ?');
$stmt -> bind_param('i', $version);
$stmt -> execute();
$stmt -> close();

echo json_encode(['message' => 'Regels opgeslagen.']);

?>
