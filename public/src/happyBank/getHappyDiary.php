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

    if(empty($selectHappyDiaryResult)==true) {
        throw new exception('해피 저금통이 비었습니다.', 404);
    }
    else {

        $selectEqulDiaryCodeSql = "select * from diary join happy_diary on diary.diary_code=happy_diary.diary_code;";
        $selectEqulDiaryCodeResult = mysqli_query($conn, $selectEqulDiaryCodeSql);

        if(empty($selectEqulDiaryCodeResult)==true) {
            throw new exception('해당 일기는 해피 저금통에 저금되지 않았습니다.', 409);
        } 
        
        else {
            $codeArr=array();
            $colorArr=array();
            $keywordArr=array();
    
            while ($happyDiaryRecord = mysqli_fetch_assoc($selectEqulDiaryCodeResult)){ //해피저금통 리스트 diary_code, color, keyword 반환
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
