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

    $date = $json['date'];
    
    $getDateDiarySql = "select * from diary where email='$email' and d_date='$date';";
    $getDateDiaryResult = mysqli_query($conn, $getDateDiarySql);
    $getDateDiaryRecordCount = mysqli_num_rows($getDateDiaryResult);

    $dateInfo = array();
    
    if($getDateDiaryRecordCount>0){
        while ($diaryRecord = mysqli_fetch_assoc($getDateDiaryResult)){
            array_push($dateInfo, [$diaryRecord['color'], $diaryRecord['main_pic'], $diaryRecord['keyword'], $diaryRecord['diary_code']]);
        }
    }
    else{
        $dateInfo = "none";
    }

    $stat="success";

    mysqli_close($conn);

}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data =  json_encode(['dateInfo' => $dateInfo, 'result_code' => $stat, 'error'=>$error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>