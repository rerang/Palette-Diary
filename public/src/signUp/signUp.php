<?php
//to solve cors issue
header("Access-Control-Allow-Origin: *");

 //db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

//var for func
$user_type = "user";

try{
  $json = json_decode(file_get_contents('php://input'), TRUE);

  $email = $json['email'];
  $password = $json['password'];
  $error = "none";
  $stat = "none";

  $checkingEmailExistSql="select * from user where email='$email'";
  $checkingEmailExistResult = mysqli_fetch_assoc(mysqli_query($conn, $checkingEmailExistSql));
  if(empty($checkingEmailExistResult) == false){
    throw new exception('이미 존재하는 계정입니다.', 409);
  }

  $encrypted_password = password_hash($password, PASSWORD_DEFAULT); //password 암호화

  $insertUserSql = "insert into user(email, password, user_type,profile_pic) values('$email', '$encrypted_password', '$user_type','NULL');";
  $insertResult = mysqli_query($conn, $insertUserSql);

  if($insertResult){
    $stat = "success";
  }
  else{
    throw new exception('DB Fail - Can Not Insert User', 422);
  }
}catch(exception $e) {
  $stat   = "error";
  $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
  $data =  json_encode(['result_code' => $stat, 'error' => $error]);
  header('Content-type: application/json'); 
  echo $data;
}
mysqli_close($conn);
?>
