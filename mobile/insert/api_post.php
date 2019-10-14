<?php
function resizeImagen($ruta, $nombre, $alto, $ancho,$nombreN,$extension){
    $rutaImagenOriginal = $ruta.$nombre;
    if($extension == 'GIF' || $extension == 'gif'){
        $img_original = imagecreatefromgif($rutaImagenOriginal);
    }
    if($extension == 'jpg' || $extension == 'JPG'){
        $img_original = imagecreatefromjpeg($rutaImagenOriginal);
    }
    if($extension == 'jpeg' || $extension == 'JPEG'){
        $img_original = imagecreatefromjpeg($rutaImagenOriginal);
    }
    if($extension == 'png' || $extension == 'PNG'){
        $img_original = imagecreatefrompng($rutaImagenOriginal);
    }
    $max_ancho = $ancho;
    $max_alto = $alto;
    list($ancho,$alto)=getimagesize($rutaImagenOriginal);
    $x_ratio = $max_ancho / $ancho;
    $y_ratio = $max_alto / $alto;
    if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){//Si ancho
        $ancho_final = $ancho;
        $alto_final = $alto;
    } elseif (($x_ratio * $alto) < $max_alto){
        $alto_final = ceil($x_ratio * $alto);
        $ancho_final = $max_ancho;
    } else{
        $ancho_final = ceil($y_ratio * $ancho);
        $alto_final = $max_alto;
    }
    $tmp=imagecreatetruecolor($ancho_final,$alto_final);
    imagecopyresampled($tmp,$img_original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
    imagedestroy($img_original);
    $calidad=85;
    imagejpeg($tmp,$ruta.$nombreN,$calidad);

}




//An array to display the response
$response = array();

//if the call is an api call
if(isset($_GET['apicall'])){
    //switching the api call
    switch($_GET['apicall']){

        //if it is an upload call we will upload the image
        case 'uploadpic':

            require_once '/homepages/40/d762919221/htdocs/mobile/function_mobile.php';
            $db = new function_mobile();
            //first confirming that we have the image and tags in the request parameter
            if(isset($_FILES['fileToUpload']['name']) && isset($_POST['userid'])){

                try{
                    $myfile = $_FILES["fileToUpload"];
                    for ($i = 0; $i < count($myfile["name"]); $i++) {

                        $concatenate = 'uploads/' . $_POST['userid']. '/post/'.$_POST['foldername'].'/'.$_POST['itemfolder'].'/';

                        $IMAGE_NAME = mt_rand() . $_FILES['fileToUpload']['name'][$i];
                        $extension = pathinfo($IMAGE_NAME, PATHINFO_EXTENSION);
                        define('UPLOAD_PATH', 'uploads/' . $_POST['userid']. '/post/'.$_POST['foldername'].'/'.$_POST['itemfolder'].'/');

                        if(!is_dir(UPLOAD_PATH)){
                            mkdir(UPLOAD_PATH, 0777, true);
                        }

                        // change size to whatever key you need - error, tmp_name etc
                        move_uploaded_file($_FILES['fileToUpload']['tmp_name'][$i], UPLOAD_PATH . $IMAGE_NAME);

                        list($r_width, $r_height) = getimagesize(UPLOAD_PATH . $IMAGE_NAME);

                        $size_image = filesize(UPLOAD_PATH . $IMAGE_NAME);

                        if ($r_width > 1024 || $r_height > 768 ) {
                            resizeImagen($concatenate, $IMAGE_NAME, 768 , 1024,  "large_" . $IMAGE_NAME , $extension);
                            $newsize = filesize(UPLOAD_PATH . "large_" . $IMAGE_NAME);
                            if ($size_image < $newsize) {
                                //unlink(UPLOAD_PATH . "large_" . $IMAGE_NAME);
                            }
                        }
                        if ($r_width > 640 || $r_height > 480 ) {
                            resizeImagen($concatenate, $IMAGE_NAME, 480, 640,  "medium_" . $IMAGE_NAME , $extension);
                            $newsize = filesize(UPLOAD_PATH . "medium_" . $IMAGE_NAME);
                            if ($size_image < $newsize) {
                                //unlink(UPLOAD_PATH . "medium_" . $IMAGE_NAME);
                            }
                        }
                        if ($r_width > 320|| $r_height > 240 ) {
                            resizeImagen($concatenate, $IMAGE_NAME, 240, 320,  "small_" . $IMAGE_NAME , $extension);
                            $newsize = filesize(UPLOAD_PATH . "small_" . $IMAGE_NAME);
                            if ($size_image < $newsize) {
                                unlink(UPLOAD_PATH . "small_" . $IMAGE_NAME);
                            }
                        }
                        $post = $db->insertimages($_POST['userid'], $IMAGE_NAME, $_POST['foldername'],$_POST['itemfolder']);

                    }



                    //resizeImagen($concatenate, $IMAGE_NAME, 75 , 75,  "verysmall_" . $IMAGE_NAME , $extension);


                    $postmain = $db->insert_post($_POST['userid'], $_POST['foldername'], $_POST['description']);
                    if ($postmain){
                        $response['message'] = 'Post subida correctamente';
                    }else{
                        $response['message'] = 'Fallo al postear';
                    }

                    $response['error'] = false;
                    //$response['message'] = 'Imagen subida correctamente';
                    $response['url'] = $IMAGE_NAME;


                }catch(Exception $e){
                    $response['error'] = true;
                    $response['message'] = 'Could not upload file';
                }

            }else{
                $response['error'] = true;
                $response['message'] = "Required params not available";
            }

            break;

        //in this call we will fetch all the images

        default:
            $response['error'] = true;
            $response['message'] = 'Invalid api call';
    }

}else{
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
}

//displaying the response in json
header('Content-Type: application/json');
echo json_encode($response);
?>