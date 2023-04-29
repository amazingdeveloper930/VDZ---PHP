<?php
if(! isset($_FILES['files']))
{
    echo json_encode(['message' => 'success', 'paths' =>  []]);
    return;
}
$fileNames = array_filter($_FILES['files']['name']); 

$targetDir = "../../upload/"; 
function generateRandomString($length = 10, $hasNumber = false, $hasLowercase = true, $hasUppercase = false): string
{
    $string = '';
    if ($hasNumber)
        $string .= '0123456789';
    if ($hasLowercase)
        $string .= 'abcdefghijklmnopqrstuvwxyz';
    if ($hasUppercase)
        $string .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle(str_repeat($x = $string, ceil($length / strlen($x)))), 1, $length);
}
$filePathArr = [];
if(!empty($fileNames)){ 
    foreach($_FILES['files']['name'] as $key=>$val){ 
       

      
         // Check whether file type is valid 
         $fileType = pathinfo($_FILES["files"]["name"][$key], PATHINFO_EXTENSION); 
           // File upload path 
        $fileName = generateRandomString() . "." . $fileType; 
        $targetFilePath = $targetDir . $fileName; 
         

        if(move_uploaded_file($_FILES["files"]["tmp_name"][$key], $targetFilePath)){ 
           $filePathArr []= $fileName;
        }else{ 
            
        } 
    } 
}

echo json_encode(['message' => 'success', 'paths' =>  $filePathArr]);
return;
     