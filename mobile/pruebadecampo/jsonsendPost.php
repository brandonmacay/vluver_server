<?php
/**
 * Created by PhpStorm.
 * User: Brandon
 * Date: 05/04/2019
 * Time: 3:16
 */
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
    $calidad=100;
    imagejpeg($tmp,$ruta.$nombreN,$calidad);

}

    $json = json_decode(file_get_contents('php://input'),true);

    $name = $json["name"]; //within square bracket should be same as Utils.imageName & Utils.imageList
    $imageList = $json["imageList"];
    $i = 0;

    $response = array();

    if (isset($imageList)) {
        if (is_array($imageList)) {
            foreach($imageList as $image) {
                $decodedImage = base64_decode("$image");
                $return = file_put_contents("uploads/".$name."_".$i.".JPG", $decodedImage);
                if($return !== false){
                    $response['success'] = 1;
                    $response['message'] = "Image Uploaded Successfully";
                }else{
                    $response['success'] = 0;
                    $response['message'] = "Image Uploaded Failed";
                }
                $i++;
                $file_tname = $_FILES['photo']['tmp_name'];
                $base64 = chunk_split(base64_encode(file_get_contents($file_tname)));

                try{
                    $concatenate = 'uploads/' . $name. '/post/';
                    $IMAGE_NAME = mt_rand() . "uploads/".$name."_".$i.".JPG";
                    $url_server = "http://vluver.com/mobile/pruebadecampo/uploads" . UPLOAD_PATH . $IMAGE_NAME;
                    $extension = pathinfo($IMAGE_NAME, PATHINFO_EXTENSION);
                    define('UPLOAD_PATH', 'uploads/' . $name. '/post/');

                    if(!is_dir(UPLOAD_PATH)){
                        mkdir(UPLOAD_PATH, 0777, true);
                    }
                    // change size to whatever key you need - error, tmp_name etc
                    move_uploaded_file("uploads/".$name."_".$i.".JPG", UPLOAD_PATH . $IMAGE_NAME);

                    list($r_width, $r_height) = getimagesize(UPLOAD_PATH . $IMAGE_NAME);

                    $size_image = filesize(UPLOAD_PATH . $IMAGE_NAME);

                    if ($r_width > 1024 || $r_height > 768 ) {
                        resizeImagen($concatenate, $IMAGE_NAME, 768 , 1024,  "large_" . $IMAGE_NAME , $extension);
                        $newsize = filesize(UPLOAD_PATH . "large_" . $IMAGE_NAME);
                        if ($size_image < $newsize) {
                            unlink(UPLOAD_PATH . "large_" . $IMAGE_NAME);
                        }
                    }
                    if ($r_width > 640 || $r_height > 480 ) {
                        resizeImagen($concatenate, $IMAGE_NAME, 480, 640,  "medium_" . $IMAGE_NAME , $extension);
                        $newsize = filesize(UPLOAD_PATH . "medium_" . $IMAGE_NAME);
                        if ($size_image < $newsize) {
                            unlink(UPLOAD_PATH . "medium_" . $IMAGE_NAME);
                        }
                    }
                    if ($r_width > 320|| $r_height > 240 ) {
                        resizeImagen($concatenate, $IMAGE_NAME, 240, 320,  "small_" . $IMAGE_NAME , $extension);
                        $newsize = filesize(UPLOAD_PATH . "small_" . $IMAGE_NAME);
                        if ($size_image < $newsize) {
                            unlink(UPLOAD_PATH . "small_" . $IMAGE_NAME);
                        }
                    }


                    resizeImagen($concatenate, $IMAGE_NAME, 75 , 75,  "verysmall_" . $IMAGE_NAME , $extension);

                    $response['error'] = false;
                    $response['message'] = 'Imagen subida correctamente';
                    $response['url'] = $IMAGE_NAME;


                }catch(Exception $e){
                    $response['error'] = true;
                    $response['message'] = 'Could not upload file';
                }

            }
        }
    } else{
        $response['success'] = 0;
        $response['message'] = "List is empty.";
    }

    echo json_encode($response);
?>