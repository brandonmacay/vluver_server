<?php



class function_mobile {

    private $conn;

    function __construct() {
        require_once "accesodbvluver.php";

        
        $db = new connect_db();
        $this->conn = $db->connect();
    }


    function __destruct() {

    }

    /**
     * Storing new user
     * returns user details
     * fungsi untuk mendaftarkan user
     */
     
        public function delete_post ($_id) {
         
        // $datetime = date("Y-m-d h:i:s");
         
        $stmt = $this->conn->prepare("DELETE FROM posts WHERE _id = ?");

       
        $stmt->bind_param("s", $_id);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
       
        if ($result) {
            return true;
        }
            else {
                return false;
            }
            
         
     }


     
     function time_passed($get_timestamp) {
        $timestamp = strtotime($get_timestamp);
        $diff = time() - (int)$timestamp;
 
        if ($diff == 0) 
             return 'justo ahora';
 
        if ($diff > 604800)
            return date("d M Y  g:i A",$timestamp);
 
        $intervals = array
        (
           // $diff < 31622400    => array('año',    31556926),
            //$diff < 31556926    => array('mes',   2628000),
            //$diff < 2629744     => array('semana',    604800),
            $diff < 604800      => array('d',     86400),
            $diff < 86400       => array('h',    3600),
            $diff < 3600        => array('min',  60),
            $diff < 60          => array('s',  1)
        );
 
        $value = floor($diff/$intervals[1][1]);
        return ' '.$value.' '.$intervals[1][0];
}

function time_passed_comments($get_timestamp) {
        $timestamp = strtotime($get_timestamp);
        $diff = time() - (int)$timestamp;
 
        if ($diff == 0) 
             return 'justo ahora';
 
        if ($diff > 604800)
            return date("d M Y",$timestamp);
 
        $intervals = array
        (
           // $diff < 31622400    => array('año',    31556926),
            //$diff < 31556926    => array('mes',   2628000),
            //$diff < 2629744     => array('semana',    604800),
            $diff < 604800      => array('d',     86400),     
            $diff < 86400       => array('h',    3600),
            $diff < 3600        => array('min',  60),
            $diff < 60          => array('s',  1)
        );
 
        $value = floor($diff/$intervals[1][1]);
        return $value.' '.$intervals[1][0];
    
}

    public function insert_post ($creator, $codeimage, $description) {

        $stmt = $this->conn->prepare("INSERT INTO posts(creator, codigounicoimages, description) VALUES(? ,? , ?) ");
        $stmt->bind_param("sss", $creator, $codeimage, $description);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return true;
        }
        else {
            return false;
        }

    }
    public function  countposts($creator,$codeunique){
        $stmt = $this->conn->prepare("SELECT  creator, codigounicoimages  FROM posts WHERE creator = ?  AND codigounicoimages = ?");
        $stmt->bind_param("ss", $creator, $codeunique);
        if ($stmt->execute()){

            $count = array();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) {
                $count[] = $row;
            }
            $stmt->close();
            return $count;

        } else {
            return NULL;
        }

    }
    public function insertimages ($creator, $nameimage, $codeunique,$pathitem) {
        $stmt = $this->conn->prepare("INSERT INTO getimagesbycode(creator, nameimage, codigounico, path_item) VALUES(? ,? , ?, ?) ");
        $stmt->bind_param("ssss", $creator, $nameimage, $codeunique,$pathitem);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return true;
        }
        else {
            return false;
        }

    }

    public function insertUserToDB($uid, $email, $fullnames, $avatar, $gender,$phone){
        $stmt = $this->conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return "existed";

        } else {
            $stmt->close();
            $stmt = $this->conn->prepare("INSERT INTO users(unique_id, email, fullnames, avatar, genre, phone) VALUES(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss",$uid,$email,$fullnames,$avatar,$gender,$phone);
            $result = $stmt->execute();
            $stmt->store_result();
            if ($result){
                /*$stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();*/
                $stmt->close();
                return true;
            }else{
                $stmt->close();
                return false;
            }

        }
    }


    public function followUser ($user_sender, $user_receiver, $accepted) {
        $stmt = $this->conn->prepare("SELECT *from followers_users WHERE user_sender = ? AND user_receiver = ? ");
        $stmt->bind_param("ss", $user_sender,$user_receiver);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            return null;
        }else{
            $stmt = $this->conn->prepare("INSERT INTO followers_users(user_sender, user_receiver, accepted) VALUES(? ,? , ?) ");
            $stmt->bind_param("sss", $user_sender, $user_receiver, $accepted);
            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                return true;
            }
            else {
                return false;
            }
        }

    }


    public function getNumCommentByPost($post_id) {
        // memanggil data yang sesuai dengan email
    $stmt = $this->conn->prepare("SELECT count(*) as numcomments FROM comment_post WHERE post_id = ?");

        $stmt->bind_param("s", $post_id);

        if ($stmt->execute()){ 
            $comments = $stmt->get_result()->fetch_assoc();         
            $stmt->close();                                     
                                                              
            // verifying user password                                  
            // ferifikasi kecocokan password                            
           
                return $comments;
            
        } else {
            return NULL;
        }
    }
     
       public function insert_comment_post ($message, $user_id_comment,   $post_id) {
        $tgl  = date("d-m-Y");
        $jam  = date("H");
        
        $jamok = $jam;  
    
        
        $waktu = date("i:s");
        
        $datetime = $tgl." ".$jamok.":".$waktu;
        
        $stmt = $this->conn->prepare("INSERT INTO comment_post(message, unique_id, post_id, date) VALUES(?, ?, ?, ?)");

       
        $stmt->bind_param("ssss", $message, $user_id_comment, $post_id, $datetime);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
       
        if ($result) {
             $stmt = $this->conn->prepare("SELECT * FROM comment_post WHERE post_id = ? ");
            $stmt->bind_param("s", $post_id);
            $stmt->execute();
            $post = $stmt->get_result()->fetch_assoc();

            $stmt->close();
            return $post;
        }
            else {
                return false;
            }
            
         
     }
     
       public function insert_business ($unique_id, $name, $logo, $country, $state, $city, $address, $description, $link, $telephone, $cellphone, $category, $subcategory, $timework, $positionX, $positionY) {
         
         $datetime = date("Y-m-d h:i:s");
         
        $stmt = $this->conn->prepare("INSERT INTO negocio(email_user, name, logo, country, state, city, address, description, link, telephone, cellphone, category, subcategory, timework, positionX, positionY, created_at) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,? )");

       
        $stmt->bind_param("sssssssssssssssss", $unique_id, $name, $logo, $country, $state, $city, $address, $description, $link, $telephone, $cellphone, $category, $subcategory, $timework, $positionX, $positionY, $datetime);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
       
        if ($result) {
             $stmt = $this->conn->prepare("SELECT * FROM negocio WHERE unique_id = ? AND created_at = ?");
            $stmt->bind_param("ss", $unique_id, $datetime);
            $stmt->execute();
            $negocio = $stmt->get_result()->fetch_assoc();

            $stmt->close();
            return $negocio;
        }
            else {
                return false;
            }
            
         
     }
     
       public function insert_friendship_request ($user_send, $user_get,$accepted) {
         
         
         
        $stmt = $this->conn->prepare("INSERT INTO friendship_request(user_send, user_get, accepted) VALUES(?, ?, ?)");

       
        $stmt->bind_param("sss", $user_send, $user_get, $accepted);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
       
        if ($result) {
             $stmt = $this->conn->prepare("SELECT * FROM friendship_request WHERE user_send = ? AND user_get = ?");
            $stmt->bind_param("ss", $user_send, $user_get);
            $stmt->execute();
            $request = $stmt->get_result()->fetch_assoc();

            $stmt->close();
            return $request;
        }
            else {
                return false;
            }
            
         
     }
     
     /*public function updatepassword($email, $password) {
         $hash = $this->hashSSHA($password);
         $encrypted_password = $hash["encrypted"]; // encrypted password
         $salt = $hash["salt"]; // salt untuk menggadakan keamanan
         $changepassdate = date("YmdHidimsms:");
        //perintah memsaukkan ke table users dan row
               $stmt = $this->conn->prepare("UPDATE users SET encrypted_password = ?, salt = ?, changepassdate = ? WHERE email = ?");

        $stmt->bind_param("ssss", $encrypted_password, $salt,$changepassdate, $email);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        // memriksa apakah berhasil didaftarkan
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            $stmt->close();

            return $user; 
        } else {
            return false;
        }
    }*/


    /*public function storeUser($name, $lastname, $genre, $email, $password) {
       
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt untuk menggadakan keamanan

        $verified = "0";
        
        $date = date("d-m-Y H:i:s");
        $fullnames = $name." ".$lastname;
        $stmt = $this->conn->prepare("INSERT INTO users(unique_id, name, lastname, fullnames, genre, email, encrypted_password, salt, created_at, verified) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?,?)");

        $stmt->bind_param("ssssssssss", $uuid, $name, $lastname, $fullnames, $genre, $email, $encrypted_password, $salt, $date, $verified);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        // memriksa apakah berhasil didaftarkan
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            return true;
            $para = $email;

// título 
$título = 'SIGNUP | VERIFICACION';
// mensaje
$mensaje = '<html><head>
<title>Gracias por registrarte en Vlover</title></head><body>
<p>Te damos la bienvenida a tu nueva cuenta en Vlover. Para activar tu cuenta y verificar tu dirección de correo electrónico, haz clic en el siguiente enlace:</p>
<p><a href="http://myvlover.com/activate.php?email='.$email.'&unique_id='.$uuid.'">Haga clic aquí para activar su cuenta.</a></p>
				<p>Si tiene dudas o preguntas sobre la cuenta escribenos a soporte@myvlover.com.</p>				
				<p>Si has recibido este mensaje por error NO des click en en enlace e ignóralo</p>			
				<p>No respondas a este mensaje. Las respuestas de este mensaje no se supervisan.</p>	
				</body></html>';
// Para enviar un correo HTML, debe establecerse la cabecera Content-type
$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
// Cabeceras adicionales
$cabeceras .= 'From: vlover.com <noreply@vlover.com>' . "\r\n";
// Enviarlo
if (mail($para, $título, $mensaje, $cabeceras)) {
//return true;
}
else {
//return false;
}
            //$stmt->close();


            
        } else {
            return false;
        }
    }*/
    
public function updateUser($unique_id, $name, $country, $phonecode, $phone, $update) {
       
        //perintah memsaukkan ke table users dan row
               $stmt = $this->conn->prepare("UPDATE users SET name = ?, country = ?, phonecode = ?, phone = ?, updated_at = ? WHERE unique_id = ?");

        $stmt->bind_param("ssssss", $name,  $country, $phonecode, $phone, $update, $unique_id);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        // memriksa apakah berhasil didaftarkan
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE unique_id = ?");
            $stmt->bind_param("s", $unique_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            $stmt->close();

            return $user; 
        } else {
            return false;
        }
    }
    
    public function updateLike($unique_id, $idpost, $state){
       
        $stmt = $this->conn->prepare("DELETE FROM likes_posts WHERE unique_id = ? AND post_id = ? ");

        $stmt->bind_param("ss", $unique_id, $idpost);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        // memriksa apakah berhasil didaftarkan
        if ($result) {
            return true;
        }
            else {
                return false;
            }
    }
    public function insertLink($unique_id, $link) {
                
        $stmt = $this->conn->prepare("UPDATE negocio SET logo = ? WHERE unique_id = ?");

        $stmt->bind_param("ss", $link, $unique_id);

        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM negocio WHERE unique_id = ?");
            $stmt->bind_param("s", $unique_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            $stmt->close();

            return $user; 
        } else {
            return false;
        }
    }
    public function updateBusiness($unique_id, $name, $country, $state, $city, $address) {
       
        //perintah memsaukkan ke table users dan row
               $stmt = $this->conn->prepare("UPDATE negocio SET name = ?, country = ?, state = ?, city = ?, address = ? WHERE unique_id = ?");

        $stmt->bind_param("ssssss", $name,  $country, $state, $city, $address, $unique_id);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        // memriksa apakah berhasil didaftarkan
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM negocio WHERE email_user = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            $stmt->close();

            return $user; 
        } else {
            return false;
        }
    }
    
    
    public function friendship_request_reponse($user_get, $user_send, $responsex) {
       
        //perintah memsaukkan ke table users dan row
               $stmt = $this->conn->prepare("UPDATE friendship_request SET accepted = ? WHERE user_send = ? AND user_get = ?");

        $stmt->bind_param("sss", $responsex,  $user_send, $user_get);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        // memriksa apakah berhasil didaftarkan
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM friendship_request WHERE user_send = ? AND user_get = ?");
            $stmt->bind_param("ss", $user_send, $user_get);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            $stmt->close();

            return $result; 
        } else {
            return false;
        }
    }
    

        public function get_all_user_friendship_accepted($unique_id) {
       
        //perintah memsaukkan ke table users dan row
               $stmt = $this->conn->prepare("SELECT * FROM friendship_request WHERE accepted = 1 AND user_get = ?");

        $stmt->bind_param("s", $unique_id);

         if ($stmt->execute()){ 
            
            
            $friends = array();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $friends[] = $row;
        }
        $stmt->close();
            
            
            
           // $posts = $stmt->get_result()->fetch_assoc();         
          //  $stmt->close();           
            
            
                                                              
            // verifying user password                                  
            // ferifikasi kecocokan password                            
           

            return $friends; 
        } else {
            return false;
        }
    }
    public function getUserByEmail($unique_id) {
        // memanggil data yang sesuai dengan email
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");

        $stmt->bind_param("s", $unique_id);

        if ($stmt->execute()){ 
            $user = $stmt->get_result()->fetch_assoc();         
            $stmt->close();
                return $user;
            
        } else {
            return NULL;
        }
    }

    
      public function getUserById($unique_id) {
        // memanggil data yang sesuai dengan email
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE unique_id = ? LIMIT 1");

        $stmt->bind_param("s", $unique_id);

        if ($stmt->execute()){ 
            $user = $stmt->get_result()->fetch_assoc();         
            $stmt->close();                                     
                                                              
            // verifying user password                                  
            // ferifikasi kecocokan password                            
           
                return $user;
            
        } else {
            return NULL;
        }
    }
    
     public function getBusinessByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM negocio WHERE email_user = ?");

        $stmt->bind_param("s", $email);

        if ($stmt->execute()){ 
            $user = $stmt->get_result()->fetch_assoc();         
            $stmt->close();                                     
                return $user;
            
        } else {
            return NULL;
        }
    }
      public function getUserByUniqueId($unique_id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE unique_id = ?");

        $stmt->bind_param("s", $unique_id);

        if ($stmt->execute()){ 
            $user = $stmt->get_result()->fetch_assoc();         
            $stmt->close();                                     
                                                              
            // verifying user password                                  
            // ferifikasi kecocokan password                            
           
                return $user;
            
        } else {
            return NULL;
        }
    }



    public function searchUser($searchQuery){

        $search_quer = $searchQuery;

        $stmt = $this->conn->prepare("SELECT * from users where fullnames LIKE '%$search_quer%' OR email LIKE '%$search_quer%' ORDER BY '$search_quer' LIMIT 10 " );
        $stmt->bind_param("ss", $searchQuery,$search_query);

        if ($stmt->execute()){
            $buscar = array();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) {
                $buscar[] = $row;
            }
            $stmt->close();
            return $buscar;
        }

    }


    public function get_all_user_friendship_accept($unique_id) {

        $stmt = $this->conn->prepare("SELECT * FROM followers_users WHERE accepted = 1 AND user_sender = ? ");

        $stmt->bind_param("s", $unique_id);

        if ($stmt->execute()){

            $friends = array();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) {
                $friends[] = $row;
            }
            $stmt->close();
            return $friends;
        } else {
            return false;
        }
    }

    public function getLastPostByUser($creator,$from,$casetype) {

        switch ($casetype){
            //Inicio
            case 1:{
                $stmt = $this->conn->prepare("select P.creator,P.id, P.description, P.date,P.codigounicoimages from followers_users F  join posts P on P.creator = F.user_receiver join users U on P.creator = U.unique_id  where  F.user_sender = ? AND F.accepted = 1 AND DATE > F.date_following ORDER BY P.id DESC limit 10");
                //$stmt = $this->conn->prepare("SELECT id, creator, image, description, date FROM posts WHERE creator = ? AND date > ? ORDER BY RAND() ");
                $stmt->bind_param("s", $creator);
                break;
            }
            //Siguiente
            case 2:{
                $stmt = $this->conn->prepare("select P.creator,P.id, P.description, P.date,P.codigounicoimages from followers_users F  join posts P on P.creator = F.user_receiver join users U on P.creator = U.unique_id where F.user_sender = ? AND F.accepted = 1 AND  P.id BETWEEN P.id AND ?-1  AND DATE > F.date_following ORDER BY P.id DESC limit 10");
                //$stmt = $this->conn->prepare("SELECT id, creator, image, description, date FROM posts WHERE creator = ? AND date > ? AND  id BETWEEN id AND ?-1 ORDER BY RAND() ");
                $stmt->bind_param("ss", $creator,$from);
                break;
            }
            //nuevoInicio
            case 3:{
                $stmt = $this->conn->prepare("select P.creator,P.id, P.description, P.date,P.codigounicoimages from followers_users F  join posts P on P.creator = F.user_receiver join users U on P.creator = U.unique_id where F.user_sender = ? AND DATE > F.date_following AND F.accepted = 1  AND ? < P.id ORDER BY P.id DESC  limit 10");
                //$stmt = $this->conn->prepare("SELECT id, creator, image, description, date FROM posts WHERE creator = ?  AND date > ? AND ? < id ORDER BY RAND()  ");
                $stmt->bind_param("ss", $creator,$from);
                break;
            }
            default:{
                $stmt = $this->conn->prepare("select U.unique_id,P.creator,P.id, P.description, P.date,P.codigounicoimages from followers_users F  join posts P on P.creator = F.user_receiver join users U on P.creator = U.unique_id where F.user_sender = ? AND F.accepted = 1 AND P.date > F.date_following ORDER BY P.id DESC limit 10 ");
                //$stmt = $this->conn->prepare("SELECT id, creator, image, description, date FROM posts WHERE creator = ? AND date > ? ORDER BY RAND() ");
                $stmt->bind_param("s", $creator);
                break;
            }

        }
        if ($stmt->execute()){

            $posts = array();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
            $stmt->close();
            return $posts;

        } else {
            return NULL;
        }
    }

    public function getAllPostV2($creator) {

        //$stmt = $this->conn->prepare("SELECT * FROM posts JOIN followers_users ON (followers_users.user_sender = ? AND followers_users.accepted = 1) WHERE posts.date > followers_users.date_following");
        $stmt = $this->conn->prepare("select U.unique_id,P.creator,P.id, P.description, P.date, P.image from followers_users F  join posts P on P.creator = F.user_receiver join users U on P.creator = U.unique_id where F.user_sender = ? AND F.accepted = 1 ");
/*select P.id,P.creator,P.image,P.description, P.date, U.email
        from posts as P natural join users as U WHERE (P.creator="bY7vAEgsLxP4cvFHb4i4fzCusex1" or P.creator in
        (select distinct U.unique_id from users as U inner join followers_users as F where
        (U.unique_id=F.user_sender and F.user_sender="bY7vAEgsLxP4cvFHb4i4fzCusex1" and F.accepted=1) or
        (U.unique_id=F.user_sender and F.user_sender="bY7vAEgsLxP4cvFHb4i4fzCusex1" and F.accepted=1))) ORDER BY P.date DESC*/


        $stmt->bind_param("s", $creator);

        if ($stmt->execute()){

            $posts = array();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
            $stmt->close();
            return $posts;

        } else {
            return NULL;
        }
    }
    
         public function getCommentsByPost($post_id) {
        // memanggil data yang sesuai dengan email
        $stmt = $this->conn->prepare("SELECT id, message, user_id_comment, date  FROM comment_post WHERE post_id = ? ORDER BY date DESC  ");

        $stmt->bind_param("s", $post_id);

        if ($stmt->execute()){ 
            
            
            $posts = array();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        $stmt->close();
         return $posts;
            
        } else {
            return NULL;
        }
    }

    
    public function getOwnCommentsByPost($post_id, $unique_id) {
        // memanggil data yang sesuai dengan email
        $stmt = $this->conn->prepare("SELECT id, message, unique_id, post_id, date  FROM comment_post WHERE post_id = ?  AND unique_id = ? ORDER BY date DESC");

        $stmt->bind_param("ss", $post_id, $unique_id);

        if ($stmt->execute()){ 

            $posts = array();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        $stmt->close();
         return $posts;
            
        } else {
            return NULL;
        }
    }
    public function getLike($unique_id, $postid) {
        // memanggil data yang sesuai dengan email
        $stmt = $this->conn->prepare("SELECT * FROM likes_posts WHERE user_uid = ? AND post_id = ? LIMIT 1");

        $stmt->bind_param("ss", $unique_id ,$postid);

        if ($stmt->execute()){ 
            $user = $stmt->get_result()->fetch_assoc();         
            $stmt->close();                                     
                                                              
            // verifying user password                                  
            // ferifikasi kecocokan password                            
           
                return $user;
            
        } else {
            return NULL;
        }
    }
    public function getImagesbycode($creator, $codeunique) {

        $stmt = $this->conn->prepare("SELECT id, creator, nameimage, codigounico,path_item  FROM getimagesbycode WHERE creator = ?  AND codigounico = ? ");

        $stmt->bind_param("ss", $creator, $codeunique);

        if ($stmt->execute()){

            $posts = array();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
            $stmt->close();
            return $posts;

        } else {
            return NULL;
        }


    }
    
    public function getLikesByPost($post_id) {
        // memanggil data yang sesuai dengan email
    $stmt = $this->conn->prepare("SELECT count(*) as likes FROM likes_posts WHERE post_id = ? LIMIT 1");

        $stmt->bind_param("s", $post_id);

        if ($stmt->execute()){ 
            $likes = $stmt->get_result()->fetch_assoc();         
            $stmt->close();                                     
                                                              
            // verifying user password                                  
            // ferifikasi kecocokan password                            
           
                return $likes;
            
        } else {
            return NULL;
        }
    }
    
      public function getAllFriendsAccepted($unique_id) {
        // memanggil data yang sesuai dengan email
        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE unique_id = ? ORDER BY fecha DESC LIMIT 1 ");

        $stmt->bind_param("s", $unique_id);

        if ($stmt->execute()){ 
            
            
            $posts = array();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        $stmt->close();
            
            
            
           // $posts = $stmt->get_result()->fetch_assoc();         
          //  $stmt->close();           
            
            
                                                              
            // verifying user password                                  
            // ferifikasi kecocokan password                            
           
                return $posts;
            
        } else {
            return NULL;
        }
    }
    
     /**
     * Get user by email 
     */
    public function getPostByUser($unique_id) {
        // memanggil data yang sesuai dengan email
        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE unique_id = ?");

        $stmt->bind_param("s", $unique_id);

        if ($stmt->execute()){ 
            
            
            $posts = array();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        $stmt->close();
            
            
            
           // $posts = $stmt->get_result()->fetch_assoc();         
          //  $stmt->close();           
            
            
                                                              
            // verifying user password                                  
            // ferifikasi kecocokan password                            
           
                return $posts;
            
        } else {
            return NULL;
        }
    }
    
    public function getFriendshipRequestByUser($unique_id) {
        // memanggil data yang sesuai dengan email
        $stmt = $this->conn->prepare("SELECT * FROM friendship_request WHERE user_get = ? AND accepted = 0");

        $stmt->bind_param("s", $unique_id);

        if ($stmt->execute()){ 
            
            
            $request = array();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $request[] = $row;
        }
        $stmt->close();
            
            
            
           // $posts = $stmt->get_result()->fetch_assoc();         
          //  $stmt->close();           
            
            
                                                              
            // verifying user password                                  
            // ferifikasi kecocokan password                            
           
                return $request;
            
        } else {
            return NULL;
        }
    }


 public function update_start_state($unique_id) {
        $tgl  = date("d-m-Y");
        $jam  = date("H");
        $waktu = date("i:s");
        
        $start = $tgl." ".$jam.":".$waktu;
       
       $stmt = $this->conn->prepare("UPDATE users SET start = ? WHERE unique_id = ?");

        $stmt->bind_param("ss", $start, $unique_id);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        // memriksa apakah berhasil didaftarkan
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE unique_id = ?");
            $stmt->bind_param("s", $unique_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            $stmt->close();

            return $user; 
        } else {
            return false;
        }
    }
    public function set_like($unique_id, $idpost, $state) {
      
        $stmt = $this->conn->prepare("INSERT INTO likes_posts(unique_id, estado, post_id) VALUES(?, ?, ?)");

       
        $stmt->bind_param("sss", $unique_id,$state, $idpost);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
       
        if ($result) {
             $stmt = $this->conn->prepare("SELECT * FROM likes_posts WHERE unique_id = ? AND post_id = ? AND estado = ?");
            $stmt->bind_param("sss", $unique_id, $idpost,$state);
            $stmt->execute();
            $post = $stmt->get_result()->fetch_assoc();

            $stmt->close();
            return $post;
        }
            else {
                return false;
            }
            
         
     }
    public function update_change_pass($email) {
       
       $start = date("YYimsdHisH");
       
       $stmt = $this->conn->prepare("UPDATE users SET changepassdate = ? WHERE email = ?");

        $stmt->bind_param("ss", $start, $email);

        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        // memriksa apakah berhasil didaftarkan
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            $stmt->close();

            return $user; 
        } else {
            return false;
        }
    }
    
     public function getUserByEmailAndPassword($email, $password) {
        // memanggil data yang sesuai dengan email
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");

        $stmt->bind_param("s", $email);

        if ($stmt->execute()){ 
            //menyiapkan data yg diambil, fetch data
            $user = $stmt->get_result()->fetch_assoc();         
            $stmt->close();                                     
                                                              
            // verifying user password                                  
            // ferifikasi kecocokan password                            
            $salt = $user['salt'];                              
            $encrypted_password = $user['encrypted_password'];  
            $hash = $this->checkhashSSHA($salt, $password);     
                                                                      
            // check for password equality                              
            // jika password sesuai dengan database                     
            if ($encrypted_password == $hash) {                 
                // user authentication details are correct              
                // maka dapat diambil dari database         
                return $user;
            }
        } else {
            return NULL;
        }
    }

public function enviar_solicitud($email, $uuid){
      /* $tgl  = date("d-m-Y");
        $jam  = date("H");
        $waktu = date("i:s");
        
        $start = $tgl." ".$jam.":".$waktu;
       
       $stmt = $this->conn->prepare("UPDATE users SET start = ? WHERE email = ?");

        $stmt->bind_param("ss", $start, $email);

        $result = $stmt->execute();
        $stmt->close();*/

            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $strella = $user['changepassdate']; 

            $stmt->close();
        // check for successful store
        // memriksa apakah berhasil didaftarkan
       /* if ($result) {
            
            return $user; 
        } else {
            return false;
        }*/
        $para = $email;

// título 
$título = 'Vlover | Recuperacion';

// mensaje
$mensaje = '
<html>
<head>
  <title>Gracias por registrarte en Vlover</title>
</head>
<body>
  <p>Te ayudaremos a recuperar tu contraseña, solo tienes que crear una nueva: haz clic en el siguiente enlace:</p>
				
<p><a href="http://myvlover.com/resetpass_byemail.php?email='.$email.'&tokenaccess='.$uuid.'&recursoscoding='.$strella.'">Haga clic aquí para crear una nueva contraseña.</a></p>

	
				<p>Si tiene dudas o preguntas sobre la cuenta escribenos a soporte@vlover.com.</p>				
				<p>Si has recibido este mensaje por error o no solicitaste cambiar tu contraseña NO des click en en enlace e ignóralo</p>				
							
				<p>No respondas a este mensaje. Las respuestas de este mensaje no se supervisan.</p>	
</body>
</html>
';

$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";

// Cabeceras adicionales
//$cabeceras .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
$cabeceras .= 'From: vlover<noreply@vlover.com>' . "\r\n";
//$cabeceras .= 'Cc: noreply@lecheando.com' . "\r\n";
//$cabeceras .= 'Bcc: noreply@lecheando.com' . "\r\n";

// Enviarlo
if (mail($para, $título, $mensaje, $cabeceras)) {
    
return true;
}
else {
return false;
}
}
    
    public function resendEmail($email, $uuid){
        
    
        $para = $email;

// título 
$título = 'SIGNUP | VERIFICACION';

// mensaje
$mensaje = '
<html>
<head>
  <title>Gracias por registrarte en Vlover</title>
</head>
<body>
  <p>Te damos la bienvenida a tu nueva cuenta en Vlover. Para activar tu cuenta y verificar tu dirección de correo electrónico, haz clic en el siguiente enlace:</p>
				
<p><a href="http://myvlover.com/activate.php?email='.$email.'&unique_id='.$uuid.'">Haga clic aquí para activar su cuenta.</a></p>

	
				<p>Si tiene dudas o preguntas sobre la cuenta escribenos a soporte@vlover.com.</p>				
				<p>Si has recibido este mensaje por error NO des click en el enlace e ignóralo</p>				
							
				<p>No respondas a este mensaje. Las respuestas de este mensaje no se supervisan.</p>	
</body>
</html>
';

$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";

// Cabeceras adicionales
//$cabeceras .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
$cabeceras .= 'From: vlover<noreply@vlover.com>' . "\r\n";
//$cabeceras .= 'Cc: noreply@lecheando.com' . "\r\n";
//$cabeceras .= 'Bcc: noreply@lecheando.com' . "\r\n";

// Enviarlo
if (mail($para, $título, $mensaje, $cabeceras)) {
return true;
}
else {
return false;
}
    }

    /**
     * Check user is existed or not
     * fungsi untuk memeriksa user sudah terdaftar atau belum
     */
     
       public function isBusinessExisted($email_user) {
        
        $stmt = $this->conn->prepare("SELECT * from negocio WHERE email_user =  ?");

        $stmt->bind_param("s", $email_user);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed
            // jika user sudah terdaftar maka data yg dikembalikan true
            $stmt->close();
            return true;
        } else {
            // user not existed
            // jika user belum terdaftar maka data yg dikembalikan false
            $stmt->close();
            return false;
        }
    }
     
    public function isFriendshipRequestExisted($user_send, $user_get) {
        
        $stmt = $this->conn->prepare("SELECT * from friendship_request WHERE user_send = ? AND user_get =  ?");

        $stmt->bind_param("ss", $user_send, $user_get);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed
            // jika user sudah terdaftar maka data yg dikembalikan true
            $stmt->close();
            return true;
        } else {
            // user not existed
            // jika user belum terdaftar maka data yg dikembalikan false
            $stmt->close();
            return false;
        }
    }
    
        public function isUserExisted($email) {
        $stmt = $this->conn->prepare("SELECT *from users WHERE email = ?");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            //$stmt->close();
            return true;
        } else {
            return false;
        }
        $stmt->close();

    }
    public function isLikeExisted($unique_id ,$idpost) {
        $stmt = $this->conn->prepare("SELECT unique_id from likes_posts WHERE unique_id = ? AND post_id = ?");

        $stmt->bind_param("ss", $unique_id ,$idpost);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     * tambahan keamanan enkripsi password
     */
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     * fungsi untuk memeriksa enkripsi pada saat login
     */
    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }

}

?>
