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

  $checkingEmailExistSql="select * from user where email='$email'";
  $checkingEmailExistResult = mysqli_fetch_assoc(mysqli_query($conn, $checkingEmailExistSql));
  if(empty($checkingEmailExistResult)==true) { //null이라면
    throw new exception('login unstable', 401);
  }

  $userType=$getUserResult['user_type'];

  if($userType!="admin"){ 
    throw new exception('not admin.', 401);
  }  
  
  $db_password = $checkingEmailExistResult["password"];

  if(password_verify($password, $db_password)) { // 비밀번호 일치
    $encrypted_password = password_hash($changePassword, PASSWORD_DEFAULT); //password 암호화
    $updateUserSql = "update user set password ='$encrypted_password' where email='$email'";
    $updateResult = mysqli_query($conn, $updateUserSql);
    mysqli_close($conn);
  }
  else { // 비밀번호 불일치
    throw new exception('incorrect password entry', 401);
    exit();
  }

  if($updateUserSql){
    $stat = "success";
  }
  else{
    throw new exception('cant update user', 400);
  }
}catch(exception $e) {
  $stat   = "error";
  $error = ['errorMsg'   => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
  $data =  json_encode(['result_code' => $stat, 'error'=>$error]);
  header('Content-type: application/json'); 
  echo $data;
}
?>
