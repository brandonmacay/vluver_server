<?php
/**
 * Created by PhpStorm.
 * User: Brandon
 * Date: 05/12/2018
 * Time: 15:19
 */
require_once '/homepages/40/d762919221/htdocs/mobile/function_mobile.php';
$db = new function_mobile();
if (isset($_POST['user_sender']) && isset($_POST['user_receiver']) && isset($_POST['accepted']) ) {

    $user_sender = $_POST['user_sender'];
    $user_receiver = $_POST['user_receiver'];
    $accepted = $_POST['accepted'];
    $namefollower = $_POST['username'];
    $follower = $db->followUser($user_sender, $user_receiver, 1);

    if ($follower == null) {
        $response["error"] = TRUE;
        $response["error_msg"] = "Accion ya usada";
        echo json_encode($response);
    } else if ($follower) {
        $response["error"] = FALSE;
        echo json_encode($response,JSON_PRETTY_PRINT);

        require_once  '/homepages/40/d762919221/htdocs/mobile/notificationpush/notification.php';
        $notification = new Notification();

        $title = 'Tienes un nuevo seguidor';
        $message = $namefollower.' comenzo a seguirte';
        $imageUrl = '';
        $action = '';

        $actionDestination = '';

        if($actionDestination ==''){
            $action = '';
        }
        $notification->setTypenotification('following_me');
        $notification->setTitle($title);
        $notification->setMessage($message);
        $notification->setImage($imageUrl);
        $notification->setAction($action);
        $notification->setActionDestination($actionDestination);

        $firebase_api = 'AAAAIeBmZ08:APA91bEP8dedDFbD95G51SMRERtFlf2DjYgCFqISbYosimkwJQT2fHWZEddqDJX5hbmvCbCRLmT4Q_jVSn_8OL6XltRRNMCyZqEfFjr56t4Cs7Bp86UcGE6E1myKyVagdAov1Pj8-U4T';

        $topic = $user_receiver;

        $requestData = $notification->getNotificatin();

        if('topic'=='topic'){
            $fields = array(
                'to' => '/topics/' . $topic,
                'data' => $requestData,
            );

        }

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . $firebase_api,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarily
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if($result === FALSE){
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        echo '<h2>Resultado</h2><hr/><h3>Solicitud </h3><p><pre>';
        echo json_encode($fields,JSON_PRETTY_PRINT);
        echo '</pre></p><h3>Respuesta </h3><p><pre>';
        echo $result;
        echo '</pre></p>';

    }else if (!$follower){
        $response["error"] = TRUE;
        $response["error_msg"] = "Error al publicar, intentalo mas tarde";
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Error vluver";
    echo json_encode($response);
}



?>