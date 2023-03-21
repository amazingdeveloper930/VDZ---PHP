<?php 
require 'common/sessie_check.php';
require 'common/global.php';
//3962

require( 'common/connection.php');


$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'b1057_ordersvdz2';
$DATABASE_PASS = 'xgasVy38iy';
$DATABASE_NAME = 'b1057_ordersvdz2';

// $DATABASE_HOST = 'localhost';
// $DATABASE_USER = 'root';
// $DATABASE_PASS = '';
// $DATABASE_NAME = 'b1057_ordersvdz2';


$con2 = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

$stmt2 = $con2 -> prepare('SELECT * FROM offerte_default');
$stmt2 -> execute();

$result2 = $stmt2->get_result();
while($row = $result2 -> fetch_assoc())
{
    $version = 2;
    $stmt = $con -> prepare('INSERT INTO offerte_default (factor, rate, inkoop, kosten, version) VALUES (?, ?, ?, ?, ?)'); 
    $stmt -> bind_param('ssssi', $row['factor'], $row['rate'], $row['inkoop'],  $row['kosten'], $version);
    $stmt -> execute();
    $stmt -> close();
}

$stmt2 = $con2 -> prepare('SELECT * FROM offerte_chapters_default order by sort_order');
$stmt2 -> execute();

$result2 = $stmt2->get_result();
while($row = $result2 -> fetch_assoc())
{
    $version = 2;
    $chapter_id = null;
    if($stmt = $con->prepare('INSERT INTO offerte_chapters_default (chapter_title, version, sort_order) VALUES (?, ?, ?)'))
    {
        $stmt->bind_param('sii', $row['chapter_title'], $version, $row['sort_order']);
        $stmt->execute();
        $chapter_id = $stmt -> insert_id;
    }

    $stmt3 = $con2 -> prepare('SELECT * FROM offerte_lines_default WHERE chapter_id = ? ORDER BY sort_order');
    $stmt3 -> bind_param('i', $row['id']);
    $stmt3 -> execute();

    $result3 = $stmt3->get_result();
    while($row3 = $result3 -> fetch_assoc())
    {
        $stmt = $con -> prepare('INSERT INTO offerte_lines_default  (line_title, line_description, line_am_option, line_unit, line_total, arbeid, materiaal_total, chapter_id, sort_order) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            
        $stmt -> bind_param('ssssssssi', $row3['line_title'], $row3['line_description'], $row3['line_am_option'], $row3['line_unit'], $row3['line_total'], $row3['arbeid'], $row3['materiaal_total'], $chapter_id, $row3['sort_order']);
        $stmt -> execute();

        $line_id = $stmt -> insert_id;

        $stmt4 = $con2 -> prepare('SELECT * FROM offerte_arbeids_default WHERE line_id = ? ORDER BY sort_order');
        $stmt4 -> bind_param('i', $row3['id']);
        $stmt4 -> execute();
        $result4 = $stmt4 -> get_result();
        while($row4 = $result4 -> fetch_assoc())
        {
            $stmt = $con -> prepare('INSERT INTO offerte_arbeids_default (arbeid_title, quantity, chapter_id, line_id, sort_order) VALUES (?, ?, ?, ?, ?)');
            $stmt -> bind_param('sssss', $row4['arbeid_title'],  $row4['quantity'], $chapter_id, $line_id, $row4['sort_order']);
            $stmt -> execute();
        }

        $stmt4 = $con2 -> prepare('SELECT * FROM offerte_materiaals_default WHERE line_id = ? ORDER BY sort_order');
        $stmt4 -> bind_param('i', $row3['id']);
        $stmt4 -> execute();
        $result4 = $stmt4 -> get_result();
        while($row4 = $result4 -> fetch_assoc())
        {
            $stmt = $con -> prepare('INSERT INTO offerte_materiaals_default (materiaal_title, stuks, price, chapter_id, line_id, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt -> bind_param('ssssss', $row4['materiaal_title'],  $row4['stuks'], $row4['price'], $chapter_id, $line_id, $row4['sort_order']);
            $stmt -> execute();
        }

    }

}



