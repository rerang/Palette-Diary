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
  $checkingEmailExistResult = mysqli_query($conn, $checkingEmailExistSql);
  if($checkingEmailExistResult->lengths) { //null이아니라면
    throw new exception('email has exist', 401);
  }

  $insertUserSql = "insert into user(email, password, user_type) values('$email', '$password', '$user_type');";
  $insertResult = mysqli_query($conn, $insertUserSql);

  if($insertResult){
    $stat = "success";
  }
  else{
    throw new exception('cant insert user', 400);
  }
}catch(exception $e) {
  $stat	= "error";
  $error = ['errorMsg'	=> $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
  $data =  json_encode(['result_code' => $stat, 'error'=>$error]);
  header('Content-type: application/json'); 
  echo $data;
}
mysqli_close($conn);
?>