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

$DBbtn_changepassword = $json['btn_changepassword'];
$DBbtn_delete = $json['btn_delete'];
$DBbtn_theme =$json['btn_theme'];


//$code = json['theme_code'];
$code = 1;

$sql = "select * from theme where theme_code='$code';";
$result = mysqli_query($conn, $sql); //명령실행

$row = mysqli_fetch_assoc($result); //레코드 가져오기
$RecordThemeName = $row["theme_name"]; // 레코드한 DB의 테마이름 
$RecordBackPic = $row["background_pic"]; // 레코드한 DB의 배경사진

//echo $RecordThemeName;
//echo $RecordBackPic;

$data =  json_encode(['theme_name' => $RecordThemeName, 'background_pic'=>$RecordBackPic]);

header('Content-type: application/json'); 

mysqli_close($conn);
?>
