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

    $selectHappyDiaryCode = $json['diary_code'];

    $selectHappyDiaryInfoSql = "select * from diary left join diary_detail on diary.diary_code = diary_detail.diary_code where diary.diary_code='$selectHappyDiaryCode';";
    $selectHappyDiaryInfoResult = mysqli_fetch_assoc(mysqli_query($conn, $selectHappyDiaryInfoSql));

    if(empty($selectHappyDiaryInfoResult)==true) {
        throw new exception('해당 일기는 해피 저금통에 저금되어 있지 않습니다.', 404);
    }
    else {
        $dbDiaryDate = $selectHappyDiaryInfoResult['d_date'];
        $dbDiaryColor = $selectHappyDiaryInfoResult['color'];
        $dbDiaryKeyword = $selectHappyDiaryInfoResult['keyword'];
        $dbDiaryMainPic = $selectHappyDiaryInfoResult['main_pic'];
        $dbDiaryBody = $selectHappyDiaryInfoResult['diary_body'];
        $dbDiarySubPic1 = $selectHappyDiaryInfoResult['subPic1'];
        $dbDiarySubPic1 = $selectHappyDiaryInfoResult['subPic2'];

        $dbDiaryDate = substr($dbDiaryDate,0,9);
        $stat="success";
    }
    mysqli_close($conn);

}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data =  json_encode(['d_date' => $dbDiaryDate, 'color' => $dbDiaryColor, 'keyword' => $dbDiaryKeyword, 'mainPic' => $dbDiaryMainPic, 'diary_body' => $dbDiaryBody, 'subPic1' => $dbDiarySubPic1, 'subPic2' => $dbDiarySubPic2, 'result_code' => $stat, 'error'=>$error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
