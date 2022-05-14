<?php
//to solve cors issue
header("Access-Control-Allow-Origin: ");

 //db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

$json = json_decode(file_get_contents('php://input'), TRUE);

$email = $json['email'];
//$email = "sp@naver.com";

$deleteSql =  "delete from user where email = '$email';";
mysqli_query($conn, $deleteSql);

header('Content-type: application/json'); 

mysqli_close($conn);
?>
