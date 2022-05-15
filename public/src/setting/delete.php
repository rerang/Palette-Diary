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

$deleteSql =  "delete from user where email = '$email';";
mysqli_query($conn, $deleteSql);

header('Content-type: application/json'); 

mysqli_close($conn);
?>
