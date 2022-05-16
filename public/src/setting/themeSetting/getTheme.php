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
        throw new exception('theme 테이블 정보를 불러올 수 없음', 404);
    }
    else{
    $codeArr=array();
    $backgroundArr=array();

    while ($themeRecord = mysqli_fetch_assoc($selectThemeResult)){
        array_push($codeArr, $themeRecord['theme_code']);
        array_push($backgroundArr, $themeRecord['background_pic']);
    }

    $test1=json_encode($codeArr);
    $test2=json_encode($backgroundArr);

    mysqli_close($conn);
    $stat="success";
}

}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg'   => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data =  json_encode(['theme_code' => $test1, 'background_pic' => $test2, 'result_code' => $stat, 'error'=>$error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
