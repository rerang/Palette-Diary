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

    $deleteDiaryCode = $json['diary_code']; // 일기 요약창에서 보여주고 있는 일기의 diary_code 반환
 
    $selectHappyDiarySql = "select * from happy_diary where diary_code='$deleteDiaryCode';";
    $selectHappyDiaryResult = mysqli_fetch_assoc(mysqli_query($conn, $selectHappyDiarySql));
    
    if(!empty($selectHappyDiaryResult)) { // 해피 다이어리에서 참조하고 있는 다이어리 코드가 있다면
        throw new exception('해피 저금통에 해당 일기가 저금 되어있으므로 삭제가 불가능합니다', 409);
    }
    
    $selectDiaryDetailSql = "select * from diary_detail where diary_code='$deleteDiaryCode';";
    $selectDiaryDetailResult = mysqli_fetch_assoc(mysqli_query($conn, $selectDiaryDetailSql));

    if(!empty($selectDiaryDetailResult)) {  // 다이어리 디테일에서 참조하고 있는 다이어리 코드가 있다면 
        $deleteDiaryDetailSql = "delete from diary_detail where diary_code='$deleteDiaryCode';";
        $deleteDiaryDetailResult = mysqli_query($conn, $deleteDiaryDetailSql);

        if(!$deleteDiaryDetailResult) {
            throw new exception('DB Fail - Can Not Delete diary_detail', 423);
        }
    }

    // diary 테이블에서 일기 삭제
    $deleteDiarySql = "delete from diary where diary_code='$deleteDiaryCode';";
    $deleteDiaryResult = mysqli_query($conn, $deleteDiarySql);

    if(!$deleteDiaryResult) {
        throw new exception('DB Fail - Can Not Delete diary', 422); //diary_detail이나 happy_diary에서 제대로 삭제가 진행되지 않았을 경우
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
