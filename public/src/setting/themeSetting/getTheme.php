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

    $selectThemeSql = "select * from theme;";
    $selectThemeResult=mysqli_query($conn, $selectThemeSql);
    if(empty($selectThemeResult)==true) { //null이라면
        throw new exception('계정이 없습니다.', 404);
    }
    $themeData="";
    while ($test = mysqli_fetch_assoc($selectThemeResult)){
        $themeData.= 'theme_code : '.$test['theme_code'].', background_pic : '.$test['background_pic'].', color_palette : '.$test['color_palette'].'<br>';
    }

    mysqli_close($conn);

}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data =  json_encode(['themeData' => $themeData, 'result_code' => $stat, 'error'=>$error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
