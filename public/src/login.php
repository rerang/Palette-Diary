<?php
//to solve cors issue
header("Access-Control-Allow-Origin: ");

//db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

try{
  $json = json_decode(file_get_contents('php://input'), TRUE);
  $email = $json['email'];
  $password = $json['password'];
  $error = "none";
  $stat = "none";

  $checkingEmailExistSql="select * from user where email='$email'";
  $checkingEmailExistResult = mysqli_fetch_assoc(mysqli_query($conn, $checkingEmailExistSql));
  if(empty($checkingEmailExistResult)==true) {
    throw new exception('email has not exist', 401);
  }
  
  $db_password = $checkingEmailExistResult["password"];
  $db_userType = $checkingEmailExistResult["user_type"];

  mysqli_close($conn);

  if(password_verify($password, $db_password)) { // 비밀번호 일치

    $HEADER = '{"alg":"HS256","typ":"JWT"}';
    $PAYLOAD = '{"email":$email, "user_type":"$db_userType", "exp" => time() + (360 * 30)}';
    $SECRETKEY = 'your-256-bit-secret';

    $base64URLencodeHEADER = str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($HEADER));
    $base64URLencodePAYLOAD = str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($PAYLOAD));
  
    $data = $base64URLencodeHEADER.".".$base64URLencodePAYLOAD;
    $hashHmacData = hash_hmac('sha256', $data, $SECRETKEY, true);
  
    $signature =  str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($hashHmacData));
  
    $token = $base64URLencodeHEADER.".".$base64URLencodePAYLOAD.".".$signature;

    $stat="success";

    if($stat!="success") {
      throw new exception('cant login', 401);
    }
  }
  else { // 비밀번호 불일치
    throw new exception('password not equal', 401);
  }

}catch(exception $e) {
  $stat   = "error";
  $error = ['errorMsg'   => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
  $data =  json_encode(['token' => $token, 'result_code' => $stat, 'error'=>$error]);
  header('Content-type: application/json'); 
  echo $data;
}
?>
