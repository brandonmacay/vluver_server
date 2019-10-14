<!DOCTYPE html>
<html lang="es">
	<head>
		<title>Firebase Push Notification | AndroidDeft.Com</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
	<body>

		<div class="container">
			<div class="row">
				<div class="col-lg-6">
					<h2>Enviar Firebase Push Notification</h2>
					<hr />
					<form action="" method="post">
						<div class="form-group">
							<label for="send_to">Enviar a:</label>
							<select name="send_to" id="send_to" class="form-control">
								<option value="sngle">Solo dispositivo</option>
								<option value="topic">Tema</option>
							</select>
						</div>

						<div class="form-group">
							<label for="firebase_api">Clave API del servidor Firebase:</label>
							<input type="text" required="" class="form-control" id="firebase_api" placeholder="Ingresa la clave del servidor Firebase" name="firebase_api">
						</div>
						<div class="form-group" id="firebase_token_group">
							<label for="firebase_token">Ficha de base de fuego:</label>
							<input type="text" required="" class="form-control" id="firebase_token" placeholder="Ingresa la ficha de firebase" name="firebase_token">
						</div>
						<div class="form-group" style="display: none" id="topic_group">
							<label for="topic">Nombre del tema:</label>
							<input type="text" class="form-control" id="topic" placeholder="Ingresa el nombre del tema:" name="topic">
						</div>
						<div class="form-group">
							<label for="title">Titulo:</label>
							<input type="text" required="" class="form-control" id="title" placeholder="Ingresa el titulo:" name="title">
						</div>
						<div class="form-group">
							<label for="message">Mensage:</label>
							<textarea required="" class="form-control" rows="5" id="message" placeholder="Ingresar mensage" name="message"></textarea>
						</div>
						<div class="checkbox">
							<label><input type="checkbox"id="include_image" name="include_image">Incluir imagen</label>
						</div>
						<div class="form-group" style="display: none" id="image_url_group">
							<label for="image_url">Imagen URL:</label>
							<input type="url" class="form-control" id="image_url" placeholder="Ingresa la URL" name="image_url">
						</div>
						<div class="checkbox">
							<label><input type="checkbox" id="include_action" name="include_action">Incluir accion</label>
						</div>
						<div class="form-group" style="display: none" id="action_group">
							<label for="action">Accion:</label>
							<select name="action" id="action" class="form-control">
								<option value="url">Abrir URL</option>
								<option value="activity">Abrir Activity</option>
							</select>
						</div>
						<div class="form-group" style="display: none" id="action_destination_group">
							<label for="action_destination">Destinatario:</label>
							<input type="text" class="form-control" id="action_destination" placeholder="Enter Destination URL or Activity name" name="action_destination">
						</div>

						<button type="submit" class="btn btn-info">Enviar</button>
					</form>
				
				</div>
				<div class="col-lg-6">
					<?php
					if(isset($_POST['title'])){
		
						require_once __DIR__ . '/notification.php';
						$notification = new Notification();
	
						$title = $_POST['title'];
						$message = isset($_POST['message'])?$_POST['message']:'';
						$imageUrl = isset($_POST['image_url'])?$_POST['image_url']:'';
						$action = isset($_POST['action'])?$_POST['action']:'';
						
						$actionDestination = isset($_POST['action_destination'])?$_POST['action_destination']:'';
	
						if($actionDestination ==''){
							$action = '';
						}
						$notification->setTitle($title);
						$notification->setMessage($message);
						$notification->setImage($imageUrl);
						$notification->setAction($action);
						$notification->setActionDestination($actionDestination);
						
						$firebase_token = $_POST['firebase_token'];
						$firebase_api = $_POST['firebase_api'];
						
						$topic = $_POST['topic'];
						
						$requestData = $notification->getNotificatin();
						
						if($_POST['send_to']=='topic'){
							$fields = array(
								'to' => '/topics/' . $topic,
								'data' => $requestData,
							);
							
						}else{
							
							$fields = array(
								'to' => $firebase_token,
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
						
						echo '<h2>Result</h2><hr/><h3>Request </h3><p><pre>';
						echo json_encode($fields,JSON_PRETTY_PRINT);
						echo '</pre></p><h3>Response </h3><p><pre>';
						echo $result;
						echo '</pre></p>';
					}
					?>
	
				</div>
			</div>
		</div>
		
		<script>
			$('#include_image').change(function(e){
					if($(this).prop("checked")==true){
						$('#image_url_group').show();
						$("#image_url").prop('required',true);
					}else{
						$('#image_url_group').hide();
						$("#image_url").prop('required',false);
					
					
					}
				});
			$('#include_action').change(function(e){
					if($(this).prop("checked")==true){
						$('#action_group').show();
						$('#action_destination_group').show();
						$("#action_destination").prop('required',true);
					}else{
						$('#action_group').hide();
						$('#action_destination_group').hide();
						$("#action_destination").prop('required',false);
					
					
					}
				});
				
			$('#send_to').change(function(e){
					var selectedVal = $("#send_to option:selected").val();
					if(selectedVal=='topic'){
						$('#topic_group').show();
						$("#topic").prop('required',true);
						$('#firebase_token_group').hide();
						$("#firebase_token").prop('required',false);
					}else{
						$('#topic_group').hide();
						$("#topic").prop('required',false);
						$('#firebase_token_group').show();
						$("#firebase_token").prop('required',true);
					}
				});
		</script>
	</body>

</html>
