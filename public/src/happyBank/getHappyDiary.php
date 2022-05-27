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
    $selectHappyDiaryResult = mysqli_fetch_assoc(mysqli_query($conn, $selectHappyDiarySql));

    $codeArr=array();
    $colorArr=array();
    $keywordArr=array();

    if(empty($selectHappyDiaryResult)==true) {
        throw new exception('해피 저금통이 비었습니다.', 404);
    }
    else {
        $selectHappyDiaryInfoSql = "select * from happy_diary left join diary on happy_diary.diary_code = diary.diary_code where happy_diary.email='$email'";
        $selectHappyDiaryInfoResult = mysqli_query($conn, $selectHappyDiaryInfoSql);

        if(empty($selectHappyDiaryInfoResult)==true) {
            throw new exception('저금된 일기가 없습니다.', 409);
        } 
        else {    
            while ($happyDiaryRecord = mysqli_fetch_assoc($selectHappyDiaryInfoResult)){ //해피저금통 리스트 diary_code, color, keyword 반환
                array_push($codeArr, $happyDiaryRecord['diary_code']);
                array_push($colorArr, $happyDiaryRecord['color']);
                array_push($keywordArr, $happyDiaryRecord['keyword']);
            }
    
            $stat="success";
        }
    }
    mysqli_close($conn);

}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data =  json_encode(['diary_code' => $codeArr, 'color' => $colorArr, 'keyword' => $keywordArr, 'result_code' => $stat, 'error'=>$error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>