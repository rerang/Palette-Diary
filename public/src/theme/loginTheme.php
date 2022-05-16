<?php
//to solve cors issue
header("Access-Control-Allow-Origin: ");

 //db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

// 1번째 테마 코드와 사진
$themeSql1 = "select theme_code,background_pic from theme where theme_code = '1';";
$themeResult1 = mysqli_query($conn,$themeSql1);
$themeRow1 = mysqli_fetch_assoc($themeResult1); 
$userThemeCode1 = $themeRow1["theme_code"];
$RecordBackPic1 = $themeRow1["background_pic"]; 

// 2번째 테마 코드와 사진
$themeSql2 = "select theme_code,background_pic from theme where theme_code = '2';";
$themeResult2 = mysqli_query($conn,$themeSql2);
$themeRow2 = mysqli_fetch_assoc($themeResult2); 
$userThemeCode2 = $themeRow2["theme_code"];
$RecordBackPic2 = $themeRow2["background_pic"]; 

// 3번째 테마 코드와 사진
$themeSql3 = "select theme_code,background_pic from theme where theme_code = '3';";
$themeResult3 = mysqli_query($conn,$themeSql3);
$themeRow3 = mysqli_fetch_assoc($themeResult3); 
$userThemeCode3 = $themeRow3["theme_code"];
$RecordBackPic3 = $themeRow3["background_pic"]; 

$data1 =  json_encode(['theme_code' => $userThemeCode1, 'background_pic'=>$RecordBackPic1]);
echo $data1; 

$data2 = json_encode(['theme_code' => $userThemeCode2, 'background_pic'=>$RecordBackPic2]);
echo $data2;

$data3 = json_encode(['theme_code' => $userThemeCode3, 'background_pic'=>$RecordBackPic3]);
echo $data3;

header('Content-type: application/json'); 
mysqli_close($conn);
?>
