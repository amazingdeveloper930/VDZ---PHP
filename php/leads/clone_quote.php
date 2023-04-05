<?php

require( '../../common/connection.php');

$new_quote_id = "";
$quote_id = "";


if(isset($_POST['quoteid']) && $_POST['quoteid']!= '') {

    $stmt = $con -> prepare("SELECT * FROM quotes WHERE id = ?");
    $quote_id = $_POST['quoteid'];
    $stmt->bind_param('s', $quote_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {

        $date = date_create();
        $dt = $date->format("Y-m-d H:i:s");
        $file_name = null;
        if($row['file_path'] != NULL && $row['file_path'] != '')
        {
            $file_name = strtotime("now") . substr($row['file_path'], -5);
            copy( "../../upload/" . $row['file_path'], "../../upload/" . $file_name );
        }

        $stmt_new = $con -> prepare("INSERT INTO quotes (contact_id, quote_date, account_id, file_path, intro, reference, factor, rate, inkoop, kosten, arbeid_factor, arbeid_pdf, materiaal_pdf, version, quote_version) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_new -> bind_param('isissssssssiiii', $_POST['contact_id'], $dt, $_POST['userid'], $file_name, $row['intro'], $row['reference'], $row['factor'], $row['rate'], $row['inkoop'], $row['kosten'], $row['arbeid_factor'], $row['arbeid_pdf'], $row['materiaal_pdf'], $row['version'], $row['quote_version']);
        $stmt_new->execute();
        $new_quote_id = $stmt_new -> insert_id;
    }

}

if($new_quote_id != '')
{
    $stmt = $con -> prepare("SELECT * FROM quote_chapters WHERE quote_id = ?");
    $stmt -> bind_param('i', $quote_id);
    $stmt -> execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {

        $chapter_id = $row['id'];
        $stmt_new = $con -> prepare("INSERT INTO quote_chapters (chapter_name, quote_id, default_chapter_id, sort_order) VALUES (?, ?, ?, ?)");
        $stmt_new -> bind_param("siii", $row['chapter_name'], $new_quote_id, $row['default_chapter_id'], $row['sort_order']);
        $stmt_new->execute();
        $new_chapter_id = $stmt_new -> insert_id;

        $stmt_1 = $con -> prepare("SELECT * FROM quote_lines WHERE quote_id = ? AND chapter_id = ? ORDER BY sort_order");
        $stmt_1 -> bind_param("ii", $quote_id, $chapter_id);
        $stmt_1 -> execute();

        $result_1 = $stmt_1 -> get_result();
        while($row_1 = $result_1 -> fetch_assoc())
        {
            $stmt_new = $con -> prepare("INSERT INTO quote_lines (	line_title, line_descr, quanitty, unit, price_inc, vat, price, subtotal, quote_id, chapter_id, line_am_option, line_profit, 	line_total, arbeid, materiaal_total, line_edit_mode, standard_tag_id, fase_tag_id, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_new -> bind_param("sssssssssssssssiiii", 
                $row_1['line_title'], $row_1['line_descr'], $row_1['quanitty'], $row_1['unit'], $row_1['price_inc'], $row_1['vat'], $row_1['price'], $row_1['subtotal'], $new_quote_id, $new_chapter_id, $row_1['line_am_option'], $row_1['line_profit'], $row_1['line_total'], $row_1['arbeid'], $row_1['materiaal_total'], $row_1['line_edit_mode'], $row_1['standard_tag_id'], $row_1['fase_tag_id'], $row_1['sort_order']);
            $stmt_new->execute();
            $new_line_id = $stmt_new -> insert_id;
            $stmt_2 = $con -> prepare("SELECT * FROM quote_arbeids WHERE line_id = ? ORDER BY sort_order");
            $stmt_2 -> bind_param('i', $row_1['id']);
            $stmt_2 -> execute();

            $result_2 = $stmt_2 -> get_result();
            while($row_2 = $result_2 -> fetch_assoc())
            {
                $stmt_new = $con -> prepare("INSERT INTO quote_arbeids (arbeid_title, quantity, quote_id, chapter_id, line_id, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_new -> bind_param("ssiiii", $row_2['arbeid_title'], $row_2['quantity'], $new_quote_id, $new_chapter_id, $new_line_id, $row_2['sort_order']);
                $stmt_new -> execute();
            }

            $stmt_2 = $con -> prepare("SELECT * FROM quote_materiaals WHERE line_id = ? ORDER BY sort_order");
            $stmt_2 -> bind_param('i', $row_1['id']);
            $stmt_2 -> execute();

            $result_2 = $stmt_2 -> get_result();
            while($row_2 = $result_2 -> fetch_assoc())
            {
                $stmt_new = $con -> prepare("INSERT INTO quote_materiaals (materiaal_title, stuks, price, quote_id, chapter_id, line_id, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt_new -> bind_param("sssiiii", $row_2['materiaal_title'], $row_2['stuks'], $row_2['price'], $new_quote_id, $new_chapter_id, $new_line_id, $row_2['sort_order']);
                $stmt_new -> execute();
            }

        }
    }

    $QUOTE_ID =  $new_quote_id;
    include "save_quote_pdf.php";


    $result_array = [];
    if ($stmt_third = $con->prepare('SELECT quotes.*, accounts.username FROM quotes LEFT JOIN accounts  ON quotes.account_id = accounts.id WHERE quotes.id = ?')) {

            $stmt_third->bind_param('s',$new_quote_id);
            $stmt_third->execute();
            // Store the result so we can check if the account exists in the database.
            $result_third = $stmt_third->get_result();
            while ($row_third = $result_third->fetch_assoc()) {

                $dt = new DateTime($row_third['quote_date']);
                $row_third['qdate']   =   $dt->format('d-m-Y');  
                $row_third['qtime']   =   $dt->format('H:i');  
                $result_array[] = $row_third;
                
            }
            
            echo json_encode(['message' => 'offerte opgeslagen.', 'quote' => $result_array]);
            return;
    }
        
            

        
}