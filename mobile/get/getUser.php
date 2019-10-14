<?php

require_once '/homepages/40/d762919221/htdocs/mobile/function_mobile.php';
$db = new function_mobile();


if (isset($_GET['user_id'])) {

    $unique_id = $_GET['user_id'];

        $user = $db->getUserByEmail($unique_id);

    if ($user) {
        // user is found
        $response["error"] = FALSE;
        $response["uid"] = $user["unique_id"];
        $response["user"]["name"] = $user["fullnames"];
        $response["user"]["lastname"] = $user["lastname"];
        $response["user"]["email"] = $user["email"];
     	$response["user"]["birthday"] = $user["birthday"];        
        $response["user"]["genre"] = $user["genre"];
        $response["user"]["country"] = $user["country"];
        $response["user"]["phonecode"] = $user["phonecode"];         
        $response["user"]["phone"] = $user["phone"];       
        $response["user"]["avatar"] = $user["avatar"];           
        $response["user"]["created_at"] = $user["created_at"];
        $response["user"]["updated_at"] = $user["updated_at"];
        echo json_encode($response);
    } else {
        // user is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "Tu cuenta tiene problemas";
        echo json_encode($response);
    }
    

} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Completa todos los campos";
    echo json_encode($response);
}
?>