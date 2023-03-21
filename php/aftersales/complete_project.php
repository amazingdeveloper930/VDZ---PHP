<?php

require( '../../common/connection.php');
require( '../../common/global.php');
require ('../../common/sessie_check.php');

if($_POST['contact_id']) {
    $sql = "";
    for($index = 4; $index <= 7; $index ++)
    {
        if($_POST['mode'] == 'MODE' . $index)
            
            {
                $sql = "UPDATE projects SET m_status" . $index . " = 'YES' WHERE contact_id = ? AND as_status = 1 AND m_status" . $index . " = 'NO'";

                if($index == 4)
                    $title = "Bloemen/kaart";
                if($index == 5)
                    $title = "Service opmerkingen";
                if($index == 6)
                    $title = "Review";
                if($index == 7)
                    $title = "Foto's op website, FB en Insta";   
                $title .= ": afgerond";
                addNewLog($_POST['contact_id'], $_SESSION['id'], $title);
            }
    }
    
    if($stmt = $con -> prepare($sql))
    {
        $stmt -> bind_param('i', $_POST['contact_id']);
        $stmt -> execute();
        echo json_encode(['message' => 'Project opgeslagen!']);
    }
    else
        {
            echo json_encode(['message' => 'Database fout!']);
        }

}
else{
    echo json_encode(['message' => 'Database fout!']);
}