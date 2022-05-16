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

$pickThemeCode = $json['theme_code'];

$updateSql = "update user set theme_code='$pickThemeCode' where email='$email';";
mysqli_query($conn, $updateSql); 

$themeCodeSql = "select * from theme where theme_code='$pickThemeCode';";
$result = mysqli_query($conn, $themeCodeSql); 
$row = mysqli_fetch_assoc($result); 
$RecordBackPic = $row["background_pic"]; 
$RecordPalette = $row["color_palette"];

$data =  json_encode(['theme_code' => $pickThemeCode, 'background_pic'=>$RecordBackPic,'color_palette' => $RecordPalette ]);
header('Content-type: application/json'); 

mysqli_close($conn);
?>
