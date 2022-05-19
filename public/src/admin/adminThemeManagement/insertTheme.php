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
    $insertThemeCode = $json['theme_code'];

    $insertThemeSql = "insert into theme(theme_name, user_type) values('$email', '$encrypted_password', '$user_type');";
    $insertThemeResult = mysqli_query($conn, $deleteThemeSql);

    if($deleteThemeResult){
        $stat = "success";
    }
    else{
        throw new exception('DB Fail - Can Not Delete Theme', 422);
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
