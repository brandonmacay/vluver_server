
<!DOCTYPE html>
<html>
<body>

<form action="api_post.php?apicall=uploadpic" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="text" name="userid" id="userid" placeholder="escribe el unique_id para subir esta imagen a ese usuario" required>
    <br/>

    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Subir imagen" name="submit">
</form>

</body>
</html>
