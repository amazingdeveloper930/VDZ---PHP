<?php

require('../../common/connection.php');

$materiaal_id = null;
if(!isset($_POST['materiaalid'])) {
    $stmt = $con -> prepare("INSERT INTO materiaal (soort, merk, name, aanschaf_datum, waarde, employee, nummer) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt -> bind_param('sssssis', $_POST['soort'], $_POST['merk'], $_POST['name'], $_POST['aanschaf_datum'], $_POST['waarde'], $_POST['employee'], $_POST['nummer']);
    $stmt -> execute();
    $materiaal_id = $stmt -> insert_id;
}
else{
    $stmt = $con -> prepare("UPDATE materiaal SET soort = ?, merk = ?, name = ?, aanschaf_datum = ?, waarde = ?, 	employee = ?, nummer = ? WHERE id = ?");
    $stmt -> bind_param("sssssisi", $_POST['soort'], $_POST['merk'], $_POST['name'], $_POST['aanschaf_datum'], $_POST['waarde'], $_POST['employee'], $_POST['nummer'],  $_POST['materiaalid']);
    $stmt -> execute();
    $materiaal_id = $_POST['materiaalid'];
}

if($materiaal_id != null && count($_FILES) > 0){
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
    $stmt = $con -> prepare('UPDATE materiaal SET file_path = ? WHERE id = ?');
    $stmt -> bind_param('si', $filename, $materiaal_id);
    $stmt -> execute();
}


$stmt = $con -> prepare('SELECT M.*, E.name AS employee_name FROM materiaal M LEFT JOIN employees E ON M.employee = E.id WHERE M.id = ?');
$stmt -> bind_param('i', $materiaal_id);
$stmt -> execute();
$result = $stmt -> get_result();

$result_array = [];
while($row = $result -> fetch_assoc())
{
    if($row['employee'] == 0)
        $row['employee_name'] = 'Algemeen';
    $result_array = $row;
}
echo json_encode(['message' => 'Materiaal opgeslagen.', 'item' => $result_array]);