<?php
//to solve cors issue
header("Access-Control-Allow-Origin: *");

//db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

//FTP connect
$ftp_server = "palettediary.dothome.co.kr";
$ftp_port = 21;
$ftp_user_name = "palettediary";
$ftp_user_pass = "dothomepassword22!";
$ftp_send_file = "./Palette-Diary/userProfile/";

function uploadedFile($ftp_send_file, $fileName) {
    return iconv("utf-8", "CP949", $ftp_send_file.basename2($fileName));
}
  
function basename2($filename) {
    return preg_replace( '/^.+[\\\\\\/]/', '', $filename);
}
  
try{
    
    $json = json_decode(file_get_contents('php://input'), TRUE);
    $error = "none";
    $stat = "none";

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];

    $ftp_server = "palettediary.dothome.co.kr";
    $ftp_port = 21;
    $ftp_user_name = "palettediary";
    $ftp_user_pass = "dothomepassword22!";
    $ftp_send_file = "./Palette-Diary/userProfile/";

    $fileName = $email.'_'.$_FILES["file"]["name"];
    $uploadFile = uploadedFile($ftp_send_file, $fileName);

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

    if($fileType == 'image'){	
	    if($extStatus){
		    move_uploaded_file($_FILES["file"]["tmp_name"], $uploadFile);
            $imgurl = "./Palette-Diary/userProfile/".$fileName;
            $conn_id = ftp_connect($ftp_server, $ftp_port);
            $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
            ftp_pasv($conn_id, true);
            ftp_put($conn_id,  $imgurl, $imgurl, FTP_BINARY);
            ftp_close();
	    }
	    else {
		    throw new exception('image type error', 422);
	    }	
    }
    else {
	    throw new exception('image type error', 422);
    }

    $updateImageSql = "update user set progile_pic='$imgurl' where email='$email';";
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
?>