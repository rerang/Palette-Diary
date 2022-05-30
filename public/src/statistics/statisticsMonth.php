<?php
//to solve cors issue
header("Access-Control-Allow-Origin: *");

 //db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

$user_type = "user";
date_default_timezone_set('Asia/Seoul');    

try{
    $json = json_decode(file_get_contents('php://input'), TRUE);
    $error = "none";
    $stat = "none";

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];

    $checkingDiaryExistSql="select * from diary where email='$email'";
    $checkingDiaryExistResult = mysqli_fetch_assoc(mysqli_query($conn, $checkingDiaryExistSql));
    
    $colorArr=array();
    $keywordArr=array();
    $month = date("m",time());
    
    
    if(empty($checkingDiaryExistResult)==true) { 
        throw new exception('다이어리가 비었습니다.', 404);
    }else {
        $checkSql="select d_date,color,keyword from diary where USER_email='$email' AND substr(diary.d_date,6,2)='$month'";
        $monthCheckResult = mysqli_query($conn, $checkSql);

        if(empty($monthCheckResult)==true) { 
            throw new exception('지정한 월의 저장된 날짜 데이터가 없습니다.', 409);
        }
        else {  
            $todayMonth = date("m",$time);
            $checkSqlMonth = "select color,keyword from happy_diary left join diary on happy_diary.email='$email' where substr(diary.d_date,6,2)='$todayMonth'";
            $monthCheckResult = mysqli_query($conn, $checkSqlMonth);

            if(empty($monthCheckResult)==true){
                throw new exception('이번달 저장된 데이터가 없습니다.', 409);  
            }else{
                while($weekRecord = mysqli_fetch_assoc($monthCheckResult)){
                array_push($colorArr,$monthCheckResult['color']);
                array_push($keywordArr,$monthCheckResult['keyword']);
                }

        }
        }
    mysqli_close($conn);
    $stat="success";
    }
}
}
catch(exception $e) {
$stat = "error";
$error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
$data = json_encode(['color' => $colorArr, 'keyword' => $keywordArr , 'result_code' => $stat, 'error'=> $error ]);
header('Content-type: application/json'); 
echo $data;
}

mysqli_close($conn);
?>
