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
    $error = "none";
    $stat = "none";

    $deleteHappyDiaryCode = $json['diary_code']; // 해피저금통 리스트에 있는 일기의 diary_code 반환

        $deleteHappyDiarySql = "delete from happy_diary where diary_code='$deleteHappyDiaryCode';";
        $deleteHappyDiaryResult = mysqli_query($conn, $deleteHappyDiarySql);

        if(!$deleteHappyDiaryResult){
            throw new exception('DB Fail - Can Not Delete happy_diary', 422);
        }
        else{
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
