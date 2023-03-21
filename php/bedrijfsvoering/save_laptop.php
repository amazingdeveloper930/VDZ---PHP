<?php

require('../../common/connection.php');

$laptop_id = null;
if(!isset($_POST['laptopid'])) {
    $stmt = $con -> prepare("INSERT INTO laptop (soort, merk, type, aanschafdatum, employee, 	abonnement_tot, abonnement_provider, maandprijs	) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt -> bind_param('ssssisss', $_POST['soort'], $_POST['merk'], $_POST['type'], $_POST['aanschafdatum'], $_POST['employee'], $_POST['abonnement_tot'], $_POST['abonnement_provider'], $_POST['maandprijs']);
    $stmt -> execute();
    $laptop_id = $stmt -> insert_id;
}
else{
    $stmt = $con -> prepare("UPDATE laptop SET soort = ?, merk = ?, type = ?, aanschafdatum = ?, employee = ?, 	abonnement_tot = ?, abonnement_provider = ?, maandprijs = ? WHERE id = ?");
    $stmt -> bind_param("ssssisssi", $_POST['soort'], $_POST['merk'], $_POST['type'], $_POST['aanschafdatum'], $_POST['employee'], $_POST['abonnement_tot'], $_POST['abonnement_provider'], $_POST['maandprijs'], $_POST['laptopid']);
    $stmt -> execute();
    $laptop_id = $_POST['laptopid'];
}


$stmt = $con -> prepare('SELECT L.*, E.name AS employee_name FROM laptop L LEFT JOIN employees E ON L.employee = E.id WHERE L.id = ?');
$stmt -> bind_param('i', $laptop_id);
$stmt -> execute();
$result = $stmt -> get_result();

$result_array = [];
while($row = $result -> fetch_assoc())
{
    if($row['employee'] == 0)
    $row['employee_name'] = 'Algemeen';
    $result_array = $row;
}
echo json_encode(['message' => 'Laptop opgeslagen.', 'item' => $result_array]);