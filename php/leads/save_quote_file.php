<?php

require( '../../common/connection.php');
require( '../../common/global.php');


if(count($_FILES) > 0)
{
    $filename = $_FILES['file']['name'];
    $filename = str_replace(' ', '-', $filename);
    $filename =  preg_replace('/[^A-Za-z0-9.\-]/', '', $filename);
    $filename = strtotime("now") . $filename;
    $location = "../../upload/" . $filename;
    $maxDim = 820;
    $file_tmp_name = $_FILES['file']['tmp_name'];
    list($width, $height, $type, $attr) = getimagesize( $file_tmp_name );
    
    $target_filename = $file_tmp_name;
        $ratio = $width/$height;
        $new_width = 820;
        $new_height = 566;

        $src = imagecreatefromstring( file_get_contents( $file_tmp_name ) );
        if($ratio > 820/566)
        {
            $n_width = 566/$height * $width;
            $n_height = 566;
            $src = imagescale($src, 566/$height * $width);
            
        }
        else{
            $n_width = 820;
            $n_height = $height * 820 / $width;
            $src = imagescale($src, 820);
            
        }
        $src = imagerotate($src, -3, 0xfff);
        
        $x_start = ($n_width - 820 ) / 2 + 20;
        $y_start =  ($n_height - 566 ) / 2 + 10;
        $x_start = $x_start < 0 ? 0 :$x_start;
        $y_start = $y_start < 0 ? 0 :$y_start;
        // $new_width = 410;
        // $new_height = 283;
        $dst = imagecreatetruecolor( $new_width, $new_height );
        imagecopyresampled( $dst, $src, 0, 0, $x_start, $y_start, $new_width, $new_height, 820, 566);
        imagedestroy( $src );
        imagepng( $dst, $target_filename ); // adjust format as needed
        imagedestroy( $dst );
        

    move_uploaded_file($_FILES['file']['tmp_name'],$location);
   echo json_encode(['message' => 'success', 'file_name' => $filename, 'x' => $x_start, 'y' => $y_start]);
}
else{
    if($_POST['cloned'] == 1 && $_POST['file_path'] != '')
    {
        $file_name = strtotime("now") . substr($_POST['file_path'], -5);
        copy( "../../upload/" . $_POST['file_path'], "../../upload/" . $file_name );
        echo json_encode(['message' => 'success', 'file_name' => $file_name]);
    }
    else{
        echo json_encode(['message' => 'error']);
    }
    
}