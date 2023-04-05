<?php

require( '../../common/connection.php');


$quote_id = "";
$quote_version = 1;
if(isset($_POST['version']))
  $quote_version = $_POST['version']; //important - quote version is different from version

if(isset($_POST['quoteid']) && $_POST['quoteid']!= '') {
    $quote_id = $_POST['quoteid'];
    if($stmt = $con ->prepare('UPDATE quotes SET quote_date = ?, account_id = ?, intro=?, reference = ?, factor = ?, rate = ?, inkoop = ?, materiaal_pdf = ?, arbeid_pdf = ?, file_path = ?, kosten = ?, arbeid_factor = ? WHERE id = ?')){
        $date = date_create();
        $dt = $date->format("Y-m-d H:i:s");
        $stmt->bind_param('sssssssiissss', $dt, $_POST['userid'], $_POST['intro'], $_POST['reference'], $_POST['factor'], $_POST['rate'], $_POST['inkoop'], $_POST['materiaal_pdf'], $_POST['arbeid_pdf'], $_POST['file_path'], $_POST['kosten'], $_POST['arbeid_factor'], $_POST['quoteid']);
        $stmt->execute();

        $stmt = $con -> prepare('DELETE FROM quote_chapters WHERE quote_id = ? ');
        $stmt->bind_param('i', $_POST['quoteid']);
        $stmt->execute();

        $stmt = $con -> prepare('DELETE FROM quote_lines WHERE quote_id = ? ');
        $stmt->bind_param('i', $_POST['quoteid']);
        $stmt->execute();

        $stmt = $con -> prepare('DELETE FROM quote_materiaals WHERE quote_id = ? ');
        $stmt->bind_param('i', $_POST['quoteid']);
        $stmt->execute();

        $stmt = $con -> prepare('DELETE FROM quote_arbeids WHERE quote_id = ? ');
        $stmt->bind_param('i', $_POST['quoteid']);
        $stmt->execute();

    }
    else {
        // Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
        echo json_encode(['message' => 'Database fout!']);
        return;
      }	

}
else{ //new quote

    if ($stmt = $con->prepare('INSERT INTO quotes (contact_id, quote_date, account_id, reference, intro, factor, rate, inkoop, materiaal_pdf, arbeid_pdf, file_path, kosten, arbeid_factor, quote_version) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')) {
        // We do not want to e  xpose passwords in our database, so hash the password and use password_verify when a user logs in.
        $date = date_create();
        $dt = $date->format("Y-m-d H:i:s");
        $stmt->bind_param('isisssssiisssi', $_POST['contact_id'], $dt, $_POST['userid'], $_POST['reference'], $_POST['intro'], $_POST['factor'], $_POST['rate'], $_POST['inkoop'],  $_POST['materiaal_pdf'], $_POST['arbeid_pdf'], $_POST['file_path'], $_POST['kosten'], $_POST['arbeid_factor'], $quote_version);
        $stmt->execute();
        $quote_id = $stmt -> insert_id;
        
        

      } else {
        // Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
        echo json_encode(['message' => 'Database fout!']);
        return;
      }	
}



$index = 0;
foreach($_POST['chapterData'] as $chapterData)
{
    if ($stmt = $con->prepare('INSERT INTO quote_chapters (chapter_name, quote_id, default_chapter_id, 	chapter_factor, sort_order) VALUES (?, ?, ?, ?, ?)')) {
        $stmt->bind_param('sissi', $chapterData['chapter_name'], $quote_id, $chapterData['default_chapter_id'], $chapterData['chapter_factor'], $index);
        
        $stmt->execute();
        $index++;

        $chapter_id = $stmt -> insert_id;
        if(isset($chapterData['line_data']))
        {
            for($jdex = 0; $jdex < count($chapterData['line_data']); $jdex ++)
                {

                    if($stmt = $con -> prepare('INSERT INTO quote_lines (line_title, line_descr, quanitty, unit, price, vat, subtotal, quote_id, chapter_id, sort_order, price_inc, line_am_option, line_total, arbeid, materiaal_total, line_edit_mode, line_profit, standard_tag_id, fase_tag_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')){
                        $line_data = $chapterData['line_data'][$jdex];
                        if($line_data['standard_tag_id'] == '')
                            $line_data['standard_tag_id'] = NULL;
                        if($line_data['fase_tag_id'] == '')
                            $line_data['fase_tag_id'] = NULL;

                        $stmt->bind_param('sssssssiiisssssisii', $line_data['line_title'], $line_data['line_descr'], $line_data['quanitty'], $line_data['unit'], $line_data['price'], $line_data['vat'], $line_data['subtotal'], $quote_id, $chapter_id, $jdex, $line_data['price_inc'], $line_data['line_am_option'], $line_data['line_total'], $line_data['arbeid'], $line_data['materiaal_total'], $line_data['line_edit_mode'], $line_data['line_profit'], $line_data['standard_tag_id'], $line_data['fase_tag_id']  );
                        $stmt->execute();

                        $line_id = $stmt -> insert_id;
                        if(isset($line_data['arbeid_data']) && is_array($line_data['arbeid_data']))
                        for($kdex = 0; $kdex < count($line_data['arbeid_data']); $kdex ++)
                        {
                            $arbeid_data = $line_data['arbeid_data'][$kdex];
                          if($stmt = $con -> prepare('INSERT INTO quote_arbeids (arbeid_title, quantity, quote_id, chapter_id, line_id, sort_order) VALUES (?,?, ?, ?, ?, ?)'))
                          {
                            $stmt->bind_param("ssiiii", $arbeid_data['arbeid_title'], $arbeid_data['quantity'],
                            $quote_id, $chapter_id, $line_id, $kdex);
                            $stmt->execute();
                          }  
                        }
                        if(isset($line_data['materiaal_data']) &&is_array($line_data['materiaal_data']))
                        for($kdex = 0; $kdex < count($line_data['materiaal_data']); $kdex ++)
                        {
                            $materiaal_data = $line_data['materiaal_data'][$kdex];
                          if($stmt = $con -> prepare('INSERT INTO quote_materiaals (materiaal_title, stuks, price, quote_id, chapter_id, line_id, sort_order) VALUES (?,?, ?, ?, ?, ?, ?)'))
                          {
                            $stmt->bind_param("sssiiii", $materiaal_data['materiaal_title'], $materiaal_data['stuks'],
                            $materiaal_data['price'],
                            $quote_id, $chapter_id, $line_id, $kdex);
                            $stmt->execute();
                          }  
                        }
                        
                    }
                    else{
                        echo json_encode(['message' => $con -> error]);
                        return;
                    }
                    
                } 
        }
        

    }

}


$QUOTE_ID =  $quote_id;
include "save_quote_pdf.php";
// include("save_quote_pdf.php?QUOTE_ID=".$QUOTE_ID);

$result_array = [];
if ($stmt = $con->prepare('SELECT quotes.id, quotes.quote_date, accounts.username, quotes.pdf_file, quotes.intro, quotes.reference FROM quotes LEFT JOIN accounts  ON quotes.account_id = accounts.id WHERE quotes.id = ?')) {

    $stmt->bind_param('s',$quote_id);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();
    
    }
    while ($row = $result->fetch_assoc()) {
    
        $dt = new DateTime($row['quote_date']);
        $row['qdate']   =   $dt->format('d-m-Y');  
        $row['qtime']   =   $dt->format('H:i');  
        $result_array[] = $row;
      
      }






echo json_encode(['message' => 'Offerte opgeslagen.', 'quote' => $result_array]);

?>