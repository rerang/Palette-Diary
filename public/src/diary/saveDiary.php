<?php
//to solve cors issue
header("Access-Control-Allow-Origin: ");

//db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);


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
    
    if($todayDiaryCode == "") {// 다이어리 코드가 없다==새로 쓰는 일기
        $insertDiarySql = "insert into diary(email, d_date, color, main_pic, keyword) values('$email','$todayDiaryDate','$todayColor','$todayMainPicture','$todayKeyword');";
        $insertDiaryResult = mysqli_query($conn, $insertDiarySql);
        
        if($insertDiaryResult == false) {
            throw new exception('DB Fail - Can Not Insert Diary', 422);
        }

        $insertDiaryCode = $conn->insert_id;

        $insertDiaryDetailSql = "insert into diary_detail(diary_code, diary_body, sub_pic1, sub_pic_2) values('$insertDiaryCode', '$todayDiaryBody','$todaySubPicture1','$todaySubPicture2');";
        $insertDiaryDetailResult = mysqli_query($conn, $insertDiaryDetailSql);

        if($insertDiaryDetailResult == false) {
            throw new exception('DB Fail - Can Not Insert User', 422);
        }
 
        $stat = "success";
    }

    else {// 기존 일기 수정 시

        $updateDiarySql = "update diary set color ='$todayColor', keyword='$todayKeyword', main_pic='$todayMainPicture' where diary_code='$todayDiaryCode';";
        $updateDiaryResult = mysqli_query($conn, $updateDiarySql);

        if($updateDiaryResult == false) {
            throw new exception('DB Fail - Can Not Update Diary', 423);
        }

        $updateDiaryDetailSql = "update diary_detail set diary_body ='$todayDiaryBody', sub_pic1='$todaySubPicture1', sub_pic_2='$todaySubPicture2' where diary_code='$todayDiaryCode';";
        $updateDiaryDetailResult = mysqli_query($conn, $updateDiaryDetailSql);

        if($updateDiaryDetailResult == false) {
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
