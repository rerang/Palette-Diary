<?php
//to solve cors issue
header("Access-Control-Allow-Origin: ");

 //db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

try{
    $json = json_decode(file_get_contents('php://input'), TRUE);
    $changeThemeCode = $json['theme_code']; //사용자가 바꿀 테마 눌렀을 때 해당 테마에 대한 테마코드를 백으로 반환

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];
    $error = "none";
    $stat = "none";

    $checkingEmailExistSql="select * from user where email='$email'";
    $checkingEmailExistResult = mysqli_fetch_assoc(mysqli_query($conn, $checkingEmailExistSql));
    if(empty($checkingEmailExistResult)==true) { //null이라면
        throw new exception('login unstable', 402);
    }

    $db_themeCode = $checkingEmailExistResult["theme_code"];

    if($changeThemeCode==db_themeCode) { // 현재 설정된 테마를 바꿀 테마로 다시 선택했을 경우
        throw new exception('Current applied themes', 403);
    }
    else{
        $updateUserSql = "update user set theme_code='$changeThemeCode' where email='$email';";
        $updateResult = mysqli_query($conn, $updateUserSql);
        mysqli_close($conn);

        if($updateUserSql){
            $stat = "success";
        }
        else{
            throw new exception('cant update user', 400);
        }
        
        $selectThemeCodeSql = "select * from theme where theme_code='$changeThemeCode';";
        $selectThemeCodeResult =mysqli_fetch_assoc(ysqli_query($conn, selectThemeCodeSql));
        $db_themeName=$selectThemeCodeResult["theme_name"];
        $db_backgroundPic=$selectThemeCodeResult["background_pic"];
        $db_colorPalette=$selectThemeCodeResult["color_palette"];
        

    }
}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg'   => $e->getMessage(), 'errorCode' => $e->getCode()];
  }finally{
    $data =  json_encode(['theme_name' => $db_themeName, 'background_pic' => $db_backgroundPic, 'color_palette' => $db_colorPalette, 'result_code' => $stat, 'error'=>$error]);
    header('Content-type: application/json'); 
    echo $data;
  }
?>
