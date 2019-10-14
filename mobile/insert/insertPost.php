<?php
require_once '/homepages/40/d762919221/htdocs/mobile/function_mobile.php';
$db = new function_mobile();


if (isset($_POST['creator']) && isset($_POST['namecode']) && isset($_POST['description']) ) {

    $creator = $_POST['creator'];
    $image = $_POST['namecode'];
    $description = $_POST['description'];

    $post = $db->insert_post($creator, $image, $description);

    if ($post) {
        $response["error"] = FALSE;
        /*$response["post"]["id"] = $post["_id"];
        $response["post"]["unique_id"] = $post["unique_id"];
        $response["post"]["image"] = $post["image"];
        $response["post"]["content"] = $post["content"];
        $response["post"]["datetime"] = $post["fecha"];*/
        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
        $response["error_msg"] = "Error al publicar, intentalo mas tarde";
        echo json_encode($response);
    }

} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Error vluver";
    echo json_encode($response);
}
?>