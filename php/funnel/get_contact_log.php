<?php

require( '../../common/connection.php');

require('../../common/global.php');

if ($stmt = $con->prepare( 'SELECT Temp.*, accounts.username
                            From (SELECT C.cid, C.name, C.city, C.address, C.email, C.phone, C.source, C.created_date, C.c_status, CL.klanten, CL.clid, CL.entry_type, CL.entry_date, CL.entry_description, CL.account_id, CL.file_exe, CL.file_path
                                  From ( SELECT *, id as cid FROM contacts WHERE contacts.id = ?) C
                                  LEFT JOIN ( SELECT *, id as clid FROM contact_log Where contact_log.contact_id = ?) CL 
                                  ON (C.id = CL.contact_id)) Temp
                            LEFT JOIN accounts
                            ON Temp.account_id = accounts.id
                            ORDER BY Temp.entry_date DESC') ) {

$stmt->bind_param('ii', $_POST['contactid'], $_POST['contactid']);
$stmt->execute();
// Store the result so we can check if the account exists in the database.
$result = $stmt->get_result();
$stmt->close();
}
$result_array = [];
while ($row = $result->fetch_assoc()) {
  $date = $row['entry_date'];
  $timer = getTimer($date);
  $row += ['timer' => $timer];
  $result_array[] = $row;
}

echo json_encode($result_array);
$con->close();

?>