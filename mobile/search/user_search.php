<?php

require_once '/homepages/40/d762919221/htdocs/mobile/function_mobile.php';
$db = new function_mobile();

//DEBO PREPARAR LOS TEXTOS QUE VOY A BUSCAR si la cadena existe
if (isset($_GET['searchQuery'])) {
    $busqueda = $_GET['searchQuery'];
    $buscar = $db->searchUser($busqueda);
    if ($buscar){
        for ($i = 0; $i < count($buscar); $i++){
            $response["user"]["fullnames"][] = $buscar[$i]["fullnames"];
            $response["user"]["email"][] = $buscar[$i]["email"];
            $response["user"]["unique_id"][] = $buscar[$i]["unique_id"];
            $response["user"]["avatar"][] = $buscar[$i]["avatar"];
            $response["user"]["privacy"][] = $buscar[$i]["privacy"];
        }
        $response ["error"]= false;
        echo json_encode($response);
    }else{
        $response ["error"]= true;
        $response ["error_msg"]="ningun resultado encontrado";
        echo json_encode($response);
    }

}else {
    echo "campos vacios";
}

?>