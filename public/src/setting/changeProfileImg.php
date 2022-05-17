<?php
//to solve cors issue
header("Access-Control-Allow-Origin: *");

//db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

try{
    $json = json_decode(file_get_contents('php://input'), TRUE);
    $error = "none";
    $stat = "none";
   
    $fileTypeExt = explode("/", $_FILES['file']['type']);
    $fileType = $fileTypeExt[0];
    $fileExt = $fileTypeExt[1];

    $extStatus = false;

    switch($fileExt){
	    case 'jpeg':
	    case 'jpg':
	    case 'gif':
	    case 'bmp':
	    case 'png':
		    $extStatus = true;
		    break;
	    default:
            throw new exception('image type error', 422);
		    break;
    }   
   

    // You should name it uniquely.
    // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
    // On this example, obtain safe unique name from its binary data.
    if (!move_uploaded_file(
        $_FILES['file']['tmp_name'],
        "./Palette-Diary/userProfile/{$_FILES['file']['name']}"
    )) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    echo 'File is uploaded successfully.';

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];
    
    $updateImageSql = "update user set progile_pic='$resFile' where email='$email';";
    $updateImageResult = mysqli_query($conn, $updateImageSql);
    mysqli_close($conn);

    if(!$updateImageResult) {
        throw new exception('cant update user', 400);
    }
    else{
        $stat = "success";
    }
}catch(exception $e) {
    $stat = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data = json_encode(['result_code' => $stat, 'error'=> $error]);
    header('Content-type: application/json'); 
    echo $data;
}
mysqli_close($conn);
?>
