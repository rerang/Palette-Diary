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

    $selectDiaryCode = $json['diary_code'];

    $selectHappyDiaryInfoSql = "select * from diary left join diary_detail on diary.diary_code = diary_detail.diary_code where diary.diary_code='$selectDiaryCode';";
    $selectHappyDiaryInfoResult = mysqli_fetch_assoc(mysqli_query($conn, $selectHappyDiaryInfoSql));

    if(empty($selectHappyDiaryInfoResult)==true) {
        throw new exception('해당 일기는 해피 저금통에 저금되어 있지 않습니다.', 404);
    }
    else {
        $dbDiaryCode = $selectHappyDiaryInfoResult['diary_code'];
        $dbDiaryDate = $selectHappyDiaryInfoResult['d_date'];
        $dbDiaryColor = $selectHappyDiaryInfoResult['color'];
        $dbDiaryKeyword = $selectHappyDiaryInfoResult['keyword'];
        $dbDiaryBody = $selectHappyDiaryInfoResult['diary_body'];

        if((empty($selectHappyDiaryInfoResult['main_pic'])==false)) {
            $dbDiaryMainPic = $selectHappyDiaryInfoResult['main_pic'];
        }

        $stat="success";
    }
    mysqli_close($conn);

}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    if(empty($dbDiaryMainPic)==true) {
        $data =  json_encode(['diary_code' => $dbDiaryCode, 'd_date' => $dbDiaryDate, 'color' => $dbDiaryColor, 'keyword' => $dbDiaryKeyword, 'diary_body' => $dbDiaryBody, 'result_code' => $stat, 'error'=>$error]);
        header('Content-type: application/json'); 
        echo $data;
    }
    else {
        $data =  json_encode(['diary_code' => $dbDiaryCode, 'd_date' => $dbDiaryDate, 'color' => $dbDiaryColor, 'keyword' => $dbDiaryKeyword, 'mainPic' => $dbDiaryMainPic, 'diary_body' => $dbDiaryBody, 'result_code' => $stat, 'error'=>$error]);
        header('Content-type: application/json'); 
        echo $data;
    }
}
?>
