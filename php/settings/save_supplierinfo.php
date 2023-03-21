<?php

require('../../common/connection.php');


if($_POST['supplierid'] == '') { //New supplier

    if($stmt = $con -> prepare("INSERT INTO suppliers ( name, type, accountnumber, krediet, soort, email, phone, rating, login, wachtwoord, email2, mobile_phone ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )"))
    {
        $stmt -> bind_param('sssssssissss', $_POST['name'], $_POST['type'], $_POST['accountnumber'], $_POST['krediet'], $_POST['soort'], $_POST['email'], $_POST['phone'], $_POST['rating'], $_POST['login'], $_POST['wachtwoord'], $_POST['email2'], $_POST['mobile_phone']);
        $stmt -> execute();
        $id = $stmt -> insert_id;
        $stmt = $con -> prepare("SELECT * FROM suppliers WHERE id = ?");
        $stmt -> bind_param('i', $id);
        $stmt -> execute();
        $result = $stmt->get_result();
        $result_array = [];
        while ($row = $result->fetch_assoc()) {
            $result_array['supplier'] = $row;          
            }           
        $result_array['message'] = 'Leverancier opgeslagen';
        echo json_encode($result_array);
    }
    else{
        echo json_encode(['message' => "Database fout!"]);
    }
}
else{
    if($stmt = $con -> prepare("UPDATE suppliers SET name = ?,  type = ?, accountnumber = ?, krediet = ?, soort = ?, email = ?, phone = ?, rating = ?, login = ?, wachtwoord=?, email2=?, mobile_phone = ?  WHERE id = ?"))
    {
        $stmt -> bind_param('ssssssssssssi', $_POST['name'], $_POST['type'], $_POST['accountnumber'], $_POST['krediet'], $_POST['soort'], $_POST['email'], $_POST['phone'], $_POST['rating'], $_POST['login'], $_POST['wachtwoord'], $_POST['email2'], $_POST['mobile_phone'], $_POST['supplierid']);
        $stmt -> execute();
        $result_array = [];

        $stmt = $con -> prepare("SELECT * FROM suppliers WHERE id = ?");
        $stmt -> bind_param('i', $_POST['supplierid']);
        $stmt -> execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $result_array['supplier'] = $row;          
        }           


        $result_array['message'] = 'Leverancier opgeslagen';
        echo json_encode($result_array);
    }
    else{
        echo json_encode(['message' => "Database fout! " . $con -> error]);
    }
}


$con->close();

?>
