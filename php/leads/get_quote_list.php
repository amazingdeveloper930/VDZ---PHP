<?php

require( '../../common/connection.php');

require('../../common/global.php');

if ($stmt = $con->prepare( 'SELECT quotes.*, accounts.username, contacts.name, contacts.city FROM quotes 
                            LEFT JOIN accounts
                            ON quotes.account_id = accounts.id
                            LEFT JOIN contacts
                            ON quotes.contact_id = contacts.id
                            WHERE quotes.contact_id = ?
                            ORDER BY quotes.quote_date DESC' ) ) {

$stmt->bind_param('i', $_POST['contactid']);
$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();
$stmt->close();
}
$result_array = [];
while ($row = $result->fetch_assoc()) {


    $dt = new DateTime($row['quote_date']);
    $row['qdate']   =   $dt->format('d-m-Y');  
    $row['qtime']   =   $dt->format('H:i');  
    $result_array[] = $row;
    
}

if ($stmt = $con->prepare( 'SELECT C.name, C.email, C.phone, CL.entry_title, CL.entry_date FROM contacts as C
                            LEFT JOIN contact_log CL
                            ON C.id = CL.contact_id 
                            Where C.id = ?
                            ORDER BY CL.entry_date DESC LIMIT 1
                            ' )) {
                                
    $stmt->bind_param('s', $_POST['contactid']);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $result = $stmt->get_result();

}

$contact_timer = [];
while ($row = $result->fetch_assoc()) {
    $timer = null;
    // if($row['entry_title'] == "lead"){
    //     $date = $row['entry_date'];
    //     $timer = getTimer($date);
    // }
    // else{
    //     $timer = false;
    // }
    $date = $row['entry_date'];
        $timer = getTimer($date);
    $row += ['timer' => $timer];
    $contact_timer[] = $row;
    
}
$contacts = [];
$stmt = $con -> prepare("SELECT C.id, C.name, C.address FROM contacts C ORDER BY C.address");
$stmt -> execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $contacts []= $row;
}


    
echo json_encode(['result' => $result_array, 'timer' => $contact_timer, 'contacts' => $contacts]);
$con->close();

?>