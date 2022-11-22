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
    
    $selectDiaryCode = $json['diary_code']; // 저장할 일기의 diary_code 반환

    $selectHappyDiarySql = "select * from happy_diary where email='$email' and diary_code='$selectDiaryCode';";
    $selectHappyDiaryResult = mysqli_fetch_assoc(mysqli_query($conn, $selectHappyDiarySql));

    if(empty($selectHappyDiaryResult)==true) { // 해당 일기가 happy_diary에 저장된 적이 없다면
        $insertHappyDiarySql = "insert into happy_diary(diary_code, email) values('$selectDiaryCode', '$email');";
        $selectHappyDiaryResult = mysqli_query($conn, $insertHappyDiarySql);

        if(!$selectHappyDiaryResult) {
            throw new exception('DB Fail - Can Not Insert User', 422);  
        }
        else {
            $stat = "success";
        }
    }

    else { // 일기 저장 시 diary_code가 update가 되므로 happy_diary도 같이 수정된 내용으로 update 됨
        $stat = "success";
    }
    
    mysqli_close($conn);


}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data = json_encode(['result_code' => $stat, 'error'=> $error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
