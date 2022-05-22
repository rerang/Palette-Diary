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

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];
    $error = "none";
    $stat = "none";

    $selectHappyDiarySql = "select * from happy_diary where email='$email';";
    $selectHappyDiaryResult = mysqli_num_rows(mysqli_query($conn, $selectHappyDiarySql)); // happy_diary 레코드 개수 반환

    if(!$selectHappyDiaryResult) {
        throw new exception('DB Fail - Can Not select happy_diary', 422);
    }
    else {
        $stat="success";
    }

}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data =  json_encode(['count' => $selectHappyDiaryResult, 'result_code' => $stat, 'error'=>$error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
