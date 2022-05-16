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
  $password = $json['password'];
  $changePassword = $json['changePassword'];
  $error = "none";
  $stat = "none";

  $cookie = apache_request_headers()['Cookie'];
  $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];


  $getUserSql="select * from user where email='$email'";
  $getUserResult = mysqli_fetch_assoc(mysqli_query($conn, $getUserSql));


  $db_password = $getUserResult["password"];
  if(password_verify($password, $db_password)) { // 비밀번호 일치
    $encrypted_password = password_hash($changePassword, PASSWORD_DEFAULT); //password 암호화
    $updateUserSql = "update user set password ='$encrypted_password' where email='$email'";
    $updateResult = mysqli_query($conn, $updateUserSql);
    mysqli_close($conn);
  }
  else { // 비밀번호 불일치
    throw new exception('옳지 않은 비밀번호입니다.', 400);
  }

  if($updateUserSql){
    $stat = "success";
  }
  else{
    throw new exception('DB Fail - Can Not Update User', 422);
  }
}catch(exception $e) {
  $stat   = "error";
  $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
  $data =  json_encode(['result_code' => $stat, 'error'=>$error]);
  header('Content-type: application/json'); 
  echo $data;
}
?>
