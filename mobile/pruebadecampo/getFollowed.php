<?php
/**
 * Created by PhpStorm.
 * User: Brandon
 * Date: 06/12/2018
 * Time: 20:51
 */
require_once '/homepages/40/d762919221/htdocs/mobile/function_mobile.php';
$db = new function_mobile();
if (isset($_GET['user_id'])) {

    $user_id = $_GET['user_id'];
    $from=$_GET['from'];
    $casetype = $_GET['casetype'];

    $posts = $db->getLastPostByUser($user_id,$from,$casetype,$from);

    if ($posts) {
        // user is found
        $response["error"] = FALSE;
        $num = 99999999999;
        $nmayor=0;
        for ($ii = 0; $ii < count($posts); $ii++) {
            if ($posts[$ii]["id"]< $num){
                $num = $posts[$ii]["id"];
            }
            if ($posts[$ii]["id"]> $nmayor){
                $nmayor = $posts[$ii]["id"];
            }
            $response["post"]["pid"][] = $posts[$ii]["id"];
            //$response["post"]["unique_id"][] = $posts[$ii]["creator"];
            $fecha = $db->time_passed($posts[$ii]["date"]);
            $user = $db->getUserByUniqueId($posts[$ii]["creator"]);

            if ($user != false) {
                $response["post"]["name"][] = $user["fullnames"];
                $response["post"]["avatar"][] = $user["avatar"];
                $response["post"]["unique_id"][] = $user["unique_id"];
                //$response["post"]["start"][] = $user["start"];
            }

            $conteolikes = $db->getLikesByPost($posts[$ii]["id"]);
            $response["post"]["likes"][] = $conteolikes['likes'];
            $conteoComments = $db->getNumCommentByPost($posts[$ii]["id"]);
            $response["post"]["numcomments"][] = $conteoComments['numcomments'];
            $mylikes = $db->getLike($posts[$ii]["unique_id"],$posts[$ii]["id"]);

            if ($mylikes > 0) {
                $response["post"]["estadolike"][]="true";
            }
            else{
                $response["post"]["estadolike"][]= "false";
            }
            $response["post"]["image"][] = $posts[$ii]["image"];
            $response["post"]["content"][] = $posts[$ii]["description"];
            $response["post"]["fecha"][] = $fecha;
        }
        $response["nmen"] = $num;
        $response["nmay"] = $nmayor;
        echo json_encode($response);
    } else {

        $response["error"] = TRUE;
        $response["error_msg"] = "Ningun usuario encontrado";
        echo json_encode($response);
    }


} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Completa todos los campos";
    echo json_encode($response);
}
?>