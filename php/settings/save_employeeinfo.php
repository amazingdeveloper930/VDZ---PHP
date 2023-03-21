<?php

require('../../common/connection.php');

$employee_id = null;

if($_POST['employeeid'] == '') { //New supplier

    if($stmt = $con -> prepare("INSERT INTO employees ( name, sort_order, type, teamleader, achternaam, specialisme, inweekplanning, woonadres_nl,overig, telefoonnummer1, telefoonnummer2, email, geboortedatum, aankomst_datum, vertrek_datum, aankomst_datum2, vertrek_datum2, computer_login, computer_wachtwoord, zakelijke_email, email_wachtwoord, ice_naam, ice_telefoon, visa, contact_id ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )"))
    {
        $stmt_new = $con -> prepare('SELECT MAX(sort_order) AS MAXORDER FROM employees');
        $stmt_new -> execute();
        $result_new = $stmt_new->get_result();
        $order = 0;
        while ($row_new = $result_new->fetch_assoc()) {
            $order = $row_new['MAXORDER'];    
        }  
        $order++;
        $stmt -> bind_param('sisisssssssssssssssssssss', $_POST['name'], $order, $_POST['type'], $_POST['teamleader'], $_POST['achternaam'], $_POST['specialisme'], $_POST['inweekplanning'], $_POST['woonadres_nl'], $_POST['overig'], $_POST['telefoonnummer1'], $_POST['telefoonnummer2'], $_POST['email'], $_POST['geboortedatum'], $_POST['aankomst_datum'], $_POST['vertrek_datum'], $_POST['aankomst_datum2'], $_POST['vertrek_datum2'], $_POST['computer_login'], $_POST['computer_wachtwoord'], $_POST['zakelijke_email'], $_POST['email_wachtwoord'], $_POST['ice_naam'], $_POST['ice_telefoon'], $_POST['visa'], $_POST['contact_id']);
        $stmt -> execute();
        $employee_id = $stmt -> insert_id;
       
        
    }
    else{
        echo json_encode(['message' => "Database fout!"]);
        return;
    }
}
else{
    if($stmt = $con -> prepare("UPDATE employees SET name = ?, type = ?, teamleader = ?, achternaam = ?, specialisme = ?, inweekplanning = ?, woonadres_nl = ?, overig = ?,  telefoonnummer1 = ?, telefoonnummer2 = ?, email = ?, geboortedatum = ?, aankomst_datum = ?, vertrek_datum = ?,  aankomst_datum2 = ?, vertrek_datum2 = ?, computer_login = ?, computer_wachtwoord = ?, zakelijke_email = ?, email_wachtwoord = ?, ice_naam = ?, ice_telefoon = ?, visa = ?, contact_id = ? WHERE id = ?"))
    {
        $stmt -> bind_param('ssisssssssssssssssssssssi', $_POST['name'], $_POST['type'],  $_POST['teamleader'], $_POST['achternaam'], $_POST['specialisme'], $_POST['inweekplanning'], $_POST['woonadres_nl'], $_POST['overig'], $_POST['telefoonnummer1'], $_POST['telefoonnummer2'], $_POST['email'], $_POST['geboortedatum'], $_POST['aankomst_datum'], $_POST['vertrek_datum'], $_POST['aankomst_datum2'], $_POST['vertrek_datum2'], $_POST['computer_login'], $_POST['computer_wachtwoord'], $_POST['zakelijke_email'], $_POST['email_wachtwoord'], $_POST['ice_naam'], $_POST['ice_telefoon'], $_POST['visa'], $_POST['contact_id'], $_POST['employeeid']);
        $stmt -> execute();
        
        $employee_id = $_POST['employeeid'];
    }
    else{
        echo json_encode(['message' => "Database fout!"]);
        return;
    }
}
if($employee_id != null && count($_FILES) > 0){

    $filename = $_FILES['file']['name'];
    $filename = str_replace(' ', '-', $filename);
    $filename =  preg_replace('/[^A-Za-z0-9.\-]/', '', $filename);
    $filename = strtotime("now") . $filename;
    $location = "../../upload/" . $filename;
    $maxDim = 300;
    $file_tmp_name = $_FILES['file']['tmp_name'];
    list($width, $height, $type, $attr) = getimagesize( $file_tmp_name );
    if ( $width > $maxDim || $height > $maxDim ) {
        $target_filename = $file_tmp_name;
        $ratio = $width/$height;
        if( $ratio > 1) {
            $new_width = $maxDim;
            $new_height = $maxDim/$ratio;
        } else {
            $new_width = $maxDim*$ratio;
            $new_height = $maxDim;
        }
        $src = imagecreatefromstring( file_get_contents( $file_tmp_name ) );
        $dst = imagecreatetruecolor( $new_width, $new_height );
        imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
        imagedestroy( $src );
        imagepng( $dst, $target_filename ); // adjust format as needed
        imagedestroy( $dst );
    }
    move_uploaded_file($_FILES['file']['tmp_name'],$location);
    $stmt = $con -> prepare('UPDATE employees SET file_path = ? WHERE id = ?');
    $stmt -> bind_param('si', $filename, $employee_id);
    $stmt -> execute();

}

$stmt = $con -> prepare("SELECT E.*, E1.name as teamleader_name FROM employees E LEFT JOIN (SELECT id, name FROM employees) E1 ON E.teamleader = E1.id WHERE E.id = ?");
$stmt -> bind_param('i', $employee_id);
$stmt -> execute();
$result = $stmt->get_result();
$result_array = [];
while ($row = $result->fetch_assoc()) {
    if($row['teamleader'] == 0)
        $row['teamleader_name'] = 'Is teamleider';
    $result_array['employee'] = $row;          
    }           
$result_array['message'] = 'Medewerker opgeslagen';
echo json_encode($result_array);


$con->close();

?>
