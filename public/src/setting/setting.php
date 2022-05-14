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
  $json = json_decode(file_get_contents('php://input'), TRUE);;
  $cookie = apache_request_headers()['Cookie'];
  $headers = apache_request_headers();
  foreach($headers as $header => $value){
      echo "$header : $value <br />";
  }
  echo "<br />";
  echo $cookie;
  echo $_COOKIE['email'];
  $error = "none";
  $stat = "none";

  $deleteUserSql="delete from user where email='$email'";
  $cdeleteUserResult = mysqli_fetch_assoc(mysqli_query($conn, $checkingEmailExistSql));
}catch(exception $e) {
  $stat = "error";
  $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data = json_encode(['token' => $token, 'result_code' => $stat, 'error'=> $error]);
    header('Content-type: application/json'); 
    echo $data;
}
mysqli_close($conn);
?>
