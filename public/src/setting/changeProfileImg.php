<?php
//to solve cors issue
header("Access-Control-Allow-Origin: *");

//db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

$cookie = apache_request_headers()['Cookie'];
$email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];

$ftp_server = "125.140.42.36";
$ftp_port = 21;
$ftp_user_name = "paletteDiary";
$ftp_user_pass = "paletteDiary";

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

$file_name1 = $email.time();
$conn_id = ftp_connect($ftp_server, $ftp_port);
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
ftp_pasv($conn_id, true);
$imgurl = "./Palette-Diary/userProfile/".$file_name1;
ftp_put($conn_id, $imgurl, $_FILES["file"]["tmp_name"], FTP_ASCII);

mysqli_close($conn);
?>
