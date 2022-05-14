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
$userThemeCode = $json['theme_code'];

$updateSql = "update user set theme_code='$themeCode' where email='$email';";
$result = mysqli_query($conn, $sql); 

$themeCodeSql = "select * from theme where theme_code='$userThemeCode';";
$row = mysqli_fetch_assoc($themeCodeSql); 
$RecordBackPic = $row["background_pic"]; 

$data =  json_encode(['theme_code' => $userThemeCode, 'background_pic'=>$RecordBackPic]);
header('Content-type: application/json'); 

mysqli_close($conn);
?>
