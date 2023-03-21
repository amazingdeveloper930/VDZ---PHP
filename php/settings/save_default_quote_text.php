<?php

require( '../../common/connection.php');

$mode = $_POST['mode'];


$stmt = $con -> prepare('DELETE FROM quote_text_default  WHERE meta LIKE ?');
$mode_text = '';
if($mode == '_INTRO')
{   
   $mode_text = 'QUOTE_INTRO';
}

if($mode == '_VOOR')
{   
    $mode_text = 'QUOTE_VOOR';
}


$stmt->bind_param('s', $mode_text);
$stmt->execute();


if($mode_text != '')
{
    $text = $_POST['text'];
    $stmt = $con -> prepare('INSERT INTO quote_text_default ( meta, text ) VALUES (?, ?)');
    $stmt->bind_param('ss', $mode_text, $text);
    $stmt->execute();
    echo 'default quote saved';
}
else
{
    echo 'Database fout!';
}