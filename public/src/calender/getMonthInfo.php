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

    $yearMonth = $json['yearMonth'];
    $lastDay = DATE('t', strtotime($yearMonth.'-01'));
    $getMonthDiarySql = "select * from diary where email='$email' and d_date between '$yearMonth-01' and '$yearMonth-$lastDay';";
    $getMonthDiaryResult = mysqli_query($conn, $getMonthDiarySql);
    $getMonthDiaryRecordCount = mysqli_num_rows($getMonthDiaryResult);

    $colorArr = array();
    $dateArr = array();
    
    if($getMonthDiaryRecordCount>0){
        while ($diaryRecord = mysqli_fetch_assoc($getMonthDiaryResult)){
            array_push($colorArr, $diaryRecord['color']);
            array_push($dateArr, $diaryRecord['d_date']);
        }
    }
    else{
        $colorArr = "none";
        $dateArr = "none";
    }

    $stat="success";

    mysqli_close($conn);

}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data =  json_encode(['colorArr' => $colorArr, 'dateArr' => $dateArr, 'result_code' => $stat, 'error'=>$error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
