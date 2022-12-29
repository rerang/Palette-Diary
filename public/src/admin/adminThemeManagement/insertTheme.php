<?php
//to solve cors issue
header("Access-Control-Allow-Origin: ");

//db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

//imgur client id
$client_id="dcfa41bdc501573";

try {
    $json = json_decode(file_get_contents('php://input'), TRUE);
    $error = "none";
    $stat = "none";

    $theme_name = $json['theme_name'];
    $paletteArrString = $json['paletteArrString'];
    $theme = $json['theme'];

    $insertThemeSql = "insert into theme(theme_name, background_pic, color_palette) values('$theme_name', '$theme', '$paletteArrString');";
    $insertThemeResult = mysqli_query($conn, $insertThemeSql);

    if(!$insertThemeResult) {
        throw new exception('DB Fail - Can Not Insert Theme', 422);
    }
    else {
        $stat = "success";
    }

    mysqli_close($conn);
} catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
} finally{
    $data =  json_encode(['result_code' => $stat, 'error'=>$error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>