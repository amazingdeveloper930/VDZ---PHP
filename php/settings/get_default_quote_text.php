<?php

require( '../../common/connection.php');
require('../../common/global.php');



if ($stmt = $con->prepare('SELECT * FROM quote_text_default WHERE meta LIKE ? LIMIT 1')) { 

    $meta = "_INTRO";
    if($_POST['mode'] == '_INTRO')
        $meta = 'QUOTE_INTRO';
    if($_POST['mode'] == '_VOOR')
        $meta = 'QUOTE_VOOR';
    $stmt-> bind_param('s', $meta);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();
    $text = '';
    while ($row = $result->fetch_assoc()) {
        $text = $row['text'];
    }
    $result_array = [];
    $result_array['message'] = 'Successfully selected';
    $result_array['text'] = $text;
    echo json_encode($result_array);

}
else{
    $result_array = [];
    $result_array['message'] = 'Database fout!';
    echo json_encode($result_array);
}