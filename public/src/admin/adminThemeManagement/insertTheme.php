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
    $error = "none";
    $stat = "none";

    $theme_name = $json['theme_name'];
    $paletteArrString = $json['paletteArrString'];
    $backgroundImg = '/public/img/basicBackground.png';//파일처리 불가로 현재 임시 경로입니다.
    
    $insertThemeSql = "insert into theme(theme_name, background_pic, color_palette) values('$theme_name', '$backgroundImg', '$paletteArrString');";
    $insertThemeResult = mysqli_query($conn, $insertThemeSql);

    if($insertThemeResult){
        $stat = "success";
    }
    else{
        throw new exception('DB Fail - Can Not Insert Theme', 422);
    }

    mysqli_close($conn);
}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data =  json_encode(['result_code' => $stat, 'error'=>$error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
