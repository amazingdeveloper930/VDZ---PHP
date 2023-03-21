<?php

require('../../common/connection.php');

$vehicle_id = null;
if(!isset($_POST['vehicleid'])) {
    $stmt = $con -> prepare("INSERT INTO vehicle (kenteken, employee, merk, uitvoering, zitplaatsen, apkdatum, wegenbelasting_bedrag, verzekering_bedrag, lease_bedrag, lease_maatschappij, lease_start, lease_eind, restant_bedrag_slottijd, tankpas_nummer, pincode, track_jack) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt -> bind_param('sissssssssssssss', $_POST['kenteken'], $_POST['employee'], $_POST['merk'], $_POST['uitvoering'], $_POST['zitplaatsen'], $_POST['apkdatum'], $_POST['wegenbelasting_bedrag'], $_POST['verzekering_bedrag'], $_POST['lease_bedrag'], $_POST['lease_maatschappij'], $_POST['lease_start'], $_POST['lease_eind'], $_POST['restant_bedrag_slottijd'], $_POST['tankpas_nummer'],  $_POST['pincode'],  $_POST['track_jack']);
    $stmt -> execute();
    $vehicle_id = $stmt -> insert_id;
}
else{
    $stmt = $con -> prepare("UPDATE vehicle SET kenteken = ?, employee = ?, merk = ?, uitvoering = ?, zitplaatsen = ?, apkdatum = ?, wegenbelasting_bedrag = ?, verzekering_bedrag = ?, lease_bedrag = ?, lease_maatschappij = ?, lease_start = ?, lease_eind = ?, restant_bedrag_slottijd = ?, tankpas_nummer = ?, pincode = ?, track_jack = ? WHERE id = ?");
    $stmt -> bind_param("sissssssssssssssi", $_POST['kenteken'], $_POST['employee'], $_POST['merk'], $_POST['uitvoering'], $_POST['zitplaatsen'], $_POST['apkdatum'], $_POST['wegenbelasting_bedrag'], $_POST['verzekering_bedrag'], $_POST['lease_bedrag'], $_POST['lease_maatschappij'], $_POST['lease_start'], $_POST['lease_eind'], $_POST['restant_bedrag_slottijd'], $_POST['tankpas_nummer'],  $_POST['pincode'],  $_POST['track_jack'],  $_POST['vehicleid']);
    $stmt -> execute();
    $vehicle_id = $_POST['vehicleid'];
}



if($vehicle_id != null && count($_FILES) > 0){
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
    $stmt = $con -> prepare('UPDATE vehicle SET file_path = ? WHERE id = ?');
    $stmt -> bind_param('si', $filename, $vehicle_id);
    $stmt -> execute();
}



$stmt = $con -> prepare('SELECT V.*, E.name AS employee_name FROM vehicle V LEFT JOIN employees E ON V.employee = E.id WHERE V.id = ?');
$stmt -> bind_param('i', $vehicle_id);
$stmt -> execute();
$result = $stmt -> get_result();

$result_array = [];
while($row = $result -> fetch_assoc())
{
    if($row['employee'] == 0)
        $row['employee_name'] = 'Algemeen';
    $result_array = $row;
}
echo json_encode(['message' => 'Vehicle opgeslagen.', 'item' => $result_array]);