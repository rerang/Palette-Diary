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

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];

    $getUserInfoSql="select * from user where email='$email'";
    $getUserInfoResult = mysqli_fetch_assoc(mysqli_query($conn, $getUserInfoSql));

    if(!$getUserInfoResult) {
      throw new exception('계정이 없습니다.', 404);
    }
    
    $imgPath = $getUserInfoResult['profile_pic'];
  
    if($getUserInfoResult){
        $stat = "success";
    }
    else{
        throw new exception('DB Fail - Can Not Get Profile Pic', 422);
    }
}catch(exception $e) {
    $stat = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data = json_encode(['imgPath' => $imgPath, 'result_code' => $stat, 'error'=> $error]);
    header('Content-type: application/json'); 
    echo $data;
}
mysqli_close($conn);
?>
