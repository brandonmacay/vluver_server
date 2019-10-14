<?php
require_once "function_mobile.php";
$db = new function_mobile();

$UID = $_POST['UID'];
$email = $_POST['email'];
$fullnames = $_POST['fullnames'];
$avatar = $_POST['avatar'];
$gender = $_POST['gender'];
$phone = $_POST['phone'];
$user = $db->insertUserToDB($UID,$email,$fullnames,$avatar,$gender,$phone);
if ($user == "existed"){
    $response["error"] = true;
    $response["error_msg"] = "Este usuario ya existe: ".$email;
    echo json_encode($response);
}elseif ($user){
    $response["error"] = false;
    echo json_encode($response);
}elseif(!$user){
    $response["error"] = true;
    $response["error_msg"] = "Error en el registro!";
    echo json_encode($response);
}
?>
