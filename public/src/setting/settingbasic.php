<?php
//to solve cors issue
header("Access-Control-Allow-Origin: ");

 //db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

$cookie = apache_request_headers()['Cookie'];
$email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];

$sql = "select * from user where email='$email';";
$result = mysqli_query($conn, $sql); //명령실행

$row = mysqli_fetch_assoc($result); //레코드 가져오기
$userEmail = $row['email'];
$profilPic = $row['profil_pic'];


$data =  json_encode(['email' => $userEmail,'profil_pic' => $profilPic]);
header('Content-type: application/json'); 


mysqli_close($conn);
?>
