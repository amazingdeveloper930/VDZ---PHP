<?php
session_start();
require( '../../common/connection.php');
require('../../common/global.php');



$stmt = $con -> prepare("SELECT email, password FROM accounts WHERE id = ?");
$stmt -> bind_param("i", $_SESSION['id']);
$stmt -> execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->bind_result($email, $password);
    $stmt->fetch();
    $time = strtotime("+1 minute",strtotime(date("Y-m-d H:i:s"))) ;
    echo json_encode(["status" => "success", "EL" => encrypt($email), "SS" => encrypt($password), "TL" => encrypt($time)]);
}
else{
    echo json_encode(["status" => 'error']);
}
