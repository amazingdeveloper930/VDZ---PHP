<?php

require( '../../common/connection.php');


$stmt = $con -> prepare('DELETE FROM quote_chapters_default ');

$stmt->execute();

$stmt = $con -> prepare('DELETE FROM quote_lines_default  ');

$stmt->execute();




$index = 0;
foreach($_POST['chapterData'] as $chapterData)
{
    if ($stmt = $con->prepare('INSERT INTO quote_chapters_default (chapter_name, sort_order) VALUES (?, ?)')) {
        $stmt->bind_param('si', $chapterData['chapter_name'], $index);
        
        $stmt->execute();
        $index++;
        $chapter_id = $stmt -> insert_id;
        if(isset($chapterData['line_data']))
        {
            for($jdex = 0; $jdex < count($chapterData['line_data']); $jdex ++)
                {
                    if($stmt = $con -> prepare('INSERT INTO quote_lines_default (line_title, line_descr, quanitty, unit, price, vat, subtotal, chapter_id, sort_order, price_inc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')){
                        $line_data = $chapterData['line_data'][$jdex];
                        $stmt->bind_param('sssssssiis', $line_data['line_title'], $line_data['line_descr'], $line_data['quanitty'], $line_data['unit'], $line_data['price'], $line_data['vat'], $line_data['subtotal'], $chapter_id, $jdex, $line_data['price_inc']  );
                        $stmt->execute();
                        
                    }
                    else{
                        echo json_encode(['message' => $con -> error]);
                        return;
                    }
                    
                } 
        }
        

    }

}


echo json_encode(['message' => 'Regels opgeslagen.']);

?>
