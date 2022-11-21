<?php
//to solve cors issue
header("Access-Control-Allow-Origin: ");

//db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

//FTP connect
$ftp_server = "125.140.42.36";
$ftp_port = 21;
$ftp_user_name = "paletteDiary";
$ftp_user_pass = "paletteDiary";
$ftp_send_file = "./Palette-Diary/diaryPicture/";

try {
    $json = json_decode(file_get_contents('php://input'), TRUE);
    $error = "none";
    $stat = "none";

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];

    $todayDiaryCode = $json['diary_code'];
    $todayDiaryDate = $json['d_date']; // yyyy-mm-dd 형식
    $todayColor = $json['color'];
    $todayKeyword = $json['keyword'];
    $todayMainPicture = $json['mainPic'];
    $todayDiaryBody = $json['diary_body'];
    $todaySubPicture1 = $json['subPic1'];
    $todaySubPicture2 = $json['subPic2'];
    
    if(!$todayDiaryCode) {// 다이어리 코드가 없다==새로 쓰는 일기
        $insertDiarySql = "insert into diary(email, d_date, color, mainPic, keyword) values('$email','$todayDiaryDate','$todayColor','$todayMainPicture','$todayKeyword');";
        $insertDiaryResult = mysqli_fetch_assoc(mysqli_query($conn, $insertDiarySql));
        
        if(!$insertDiaryResult) {
            throw new exception('DB Fail - Can Not Insert Diary', 422);
        }

        $insertDiaryCode = $insertDiaryResult['diary_code'];

        $insertDiaryDetailSql = "insert into diary_detail (diary_code, diary_body, subPic1, subPic2) values('$insertDiaryCode', '$todayDiaryBody','$todaySubPicture1','$todaySubPicture2');";
        $insertDiaryDetailResult = mysqli_fetch_assoc(mysqli_query($conn, $insertDiaryDetailSql));

        if(!$insertDiaryDetailResult) {
            throw new exception('DB Fail - Can Not Insert User', 422);
        }
 
        $stat = "success";
    }

    else {// 기존 일기 수정 시

        $updateDiarySql = "update diary set color ='$todayColor', keyword='$todayKeyword', mainPic='$todayMainPicture' where diary_code='$todayDiaryCode';";
        $updateDiaryResult = mysqli_query($conn, $updateDiarySql);

        if(!$updateDiaryResult) {
            throw new exception('DB Fail - Can Not Update Diary', 423);
        }

        $updateDiaryDetailSql = "update diary_detail set diary_body ='$todayDiaryBody', subPic1='$todaySubPicture1', subPic2='$todaySubPicture2' where diary_code='$todayDiaryCode';";
        $updateDiaryDetailResult = mysqli_query($conn, $updateDiaryDetailSql);

        if(!$updateDiaryDetailResult) {
            throw new exception('DB Fail - Can Not Update Diary', 423);
        }

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
