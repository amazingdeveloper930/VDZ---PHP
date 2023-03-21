<?php

require( '../../common/connection.php');
require( '../../common/global.php');
require ('../../common/sessie_check.php');

if($_POST['contact_id']) {
    $sql = "";
    $title = "";
    if($_POST['mode'] == 'MODE1')
        {
            $sql = "UPDATE projects SET m_status1 = 'NO' WHERE contact_id = ?";
            $title = "Gevel binnen? Niet nodig.";
        }
    
    if($_POST['mode'] == 'MODE2')
        {
            $sql = "UPDATE projects SET m_status2 = 'NO' WHERE contact_id = ? ";
            $title = "Betonplaat binnen? Niet nodig.";
        }

    if($_POST['mode'] == 'MODE3')
        {
            $sql = "UPDATE projects SET m_status3 = 'NO' WHERE contact_id = ? ";
            $title = "Kozijnen binnen? Niet nodig.";
        }

    if($stmt = $con -> prepare($sql))
    {
        $stmt -> bind_param('i', $_POST['contact_id']);
        $stmt -> execute();
        addNewLog($_POST['contact_id'], $_SESSION['id'], $title);

        echo json_encode(['message' => 'Project gereset!']);
    }
    else
        {
            echo json_encode(['message' => 'Database fout!']);
        }

}
else{
    echo json_encode(['message' => 'Database fout!']);
}