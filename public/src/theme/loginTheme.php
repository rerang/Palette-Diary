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

$EmailCheckSql = "select * from user where email='$email';";
$EmailCheckresult = mysqli_query($conn, $EmailCheckSql); 

$emailRow = mysqli_fetch_assoc($EmailCheckresult); 
$userThemeCode = $emailRow['theme_code'];

$themeCodeSql = "select * from theme where theme_code='$userThemeCode';";
$themeCodeResult = mysqli_query($conn,$themeCodeSql);

$themeCodeRow = mysqli_fetch_assoc($themeCodeResult); 

$RecordBackPic = $themeCodeRow["background_pic"]; 

$data =  json_encode(['theme_code' => $userThemeCode, 'background_pic'=>$RecordBackPic]);
header('Content-type: application/json'); 
echo $data;

mysqli_close($conn);
?>
