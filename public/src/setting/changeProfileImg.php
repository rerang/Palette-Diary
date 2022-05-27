<?php
//to solve cors issue
header("Access-Control-Allow-Origin: *");

$ftp_server = "125.140.42.36";
$ftp_port = 21;
$ftp_user_name = "paletteDiary";
$ftp_user_pass = "paletteDiary";

try{
    //db connect
    $host = "localhost";
    $s_username = "db";
    $s_password = "dbpassword";
    $dbname = "palette_diary";
    $conn = mysqli_connect($host, $s_username, $s_password, $dbname);

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];
    
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
    
    $file_name = explode(".", $email)[0].time();
    $ftp_connection = ftp_connect($ftp_server, $ftp_port);
    $login_result = ftp_login($ftp_connection, $ftp_user_name, $ftp_user_pass);
    ftp_pasv($ftp_connection, true);//수동오픈
    $imgurl = "./Palette-Diary/userProfile/".$file_name;
    ftp_put($ftp_connection, $imgurl.".".$fileExt, $_FILES['file']['tmp_name'], FTP_BINARY);
    
    $changeProfileImgSql = "update user set profile_pic='$file_name'.'$fileExt' where email='$email';";
    $changeProfileImgResult = mysqli_query($conn, $changeProfileImgSql);

    ftp_close($ftp_connection);
    mysqli_close($conn);
}catch(exception $e) {
    $stat = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data = json_encode(['imgPath' => $file_name.".".$fileExt, 'result_code' => $stat, 'error'=> $error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
