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
  $json = json_decode(file_get_contents('php://input'), TRUE);
  $email = $json['email'];
  $password = $json['password'];
  $error = "none";
  $stat = "none";

  $checkingEmailExistSql="select * from user where email='$email'";
  $checkingEmailExistResult = mysqli_fetch_assoc(mysqli_query($conn, $checkingEmailExistSql));

  if(!$checkingEmailExistResult) {
    throw new exception('계정이 없습니다.', 404);
  }
  
  $db_password = $checkingEmailExistResult["password"];
  $db_userType = $checkingEmailExistResult["user_type"];
  $db_themeCode = $checkingEmailExistResult["theme_code"];

  $getThemeInfoSql="select * from theme where theme_code='$db_themeCode'";
  $getThemeInfoResult = mysqli_fetch_assoc(mysqli_query($conn, $getThemeInfoSql));
  $color_palette = $getThemeInfoResult['color_palette'];
  $background_pic = $getThemeInfoResult['background_pic'];

  if(password_verify($password, $db_password)) { // 비밀번호 일치
    $HEADER = json_encode(array('alg' => "HS256", 'typ' => "JWT"));
    $exp = time() + (360 * 30);
    $PAYLOAD = json_encode(array('email' => $email, 'user_type'=>$db_userType, 'exp' => $exp));
    $SECRETKEY = 'your-256-bit-secret';

    $base64URLencodeHEADER = str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($HEADER));
    $base64URLencodePAYLOAD = str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($PAYLOAD));
  
    $data = $base64URLencodeHEADER.".".$base64URLencodePAYLOAD;
    $hashHmacData = hash_hmac('sha256', $data, $SECRETKEY, true);
  
    $signature =  str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($hashHmacData));
  
    $token = $base64URLencodeHEADER.".".$base64URLencodePAYLOAD.".".$signature;

    $stat="success";

  }
  else { // 비밀번호 불일치
    throw new exception('password not correct', 405);
  }

}catch(exception $e) {
  $stat = "error";
  $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
  if($stat == "success"){
    $data = json_encode(['token' => $token, 'theme_code' => $db_themeCode, 'color_palette' => $color_palette, 'background_pic' => $background_pic, 'result_code' => $stat, 'error'=> $error]);
  }
  else{
    $data = json_encode(['result_code' => $stat, 'error'=> $error]);
  }
  header('Content-type: application/json'); 
  echo $data;
}
mysqli_close($conn);
?>
