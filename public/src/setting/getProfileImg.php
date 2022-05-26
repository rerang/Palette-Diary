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
$ftp_server = "125.140.42.36";
$ftp_port = 21;
$ftp_user_name = "paletteDiary";
$ftp_user_pass = "paletteDiary";

try{
    $json = json_decode(file_get_contents('php://input'), TRUE);
    $error = "none";
    $stat = "none";

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];

    $getUserInfoSql="select * from user where email='$email'";
    $getUserInfoResult = mysqli_fetch_assoc(mysqli_query($conn, $getUserInfoSql));

    if(!$getUserInfoResult) {
      throw new exception('계정이 없습니다.', 404);
    }
    
    $imgPath = $getUserInfoResult['profile_pic'];
  
    if($getUserInfoResult) { 

        $conn_id = ftp_connect($ftp_server, $ftp_port);
        ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
        ftp_pasv($conn_id, true);

        $remote_file = $imgPath;
        $local_file = "./userProfile/".$fileName;
        $fp = fopen($local_file, 'w+');
        ftp_fget($conn_id, $fp, $remote_file, FTP_BINARY, 0);
        ftp_close($conn_id);
        fclose($fp);

        $stat = "success";
    }
    else {
        throw new exception('DB Fail - Can Not Get Profile Pic', 422);
    }
}catch(exception $e) {
    $stat = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data = json_encode(['imgPath' => $local_file, 'result_code' => $stat, 'error'=> $error]);
    header('Content-type: application/json'); 
    echo $data;
}
mysqli_close($conn);
?>
