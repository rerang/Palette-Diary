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
        $checkSql="select * from diary where email='$email' AND substr(diary.d_date,6,2)='$month'";
        $monthCheckResult = mysqli_query($conn, $checkSql);

        if(empty($monthCheckResult)==true) { 
            throw new exception('지정한 월의 저장된 날짜 데이터가 없습니다.', 409);
        }
        else {  
            
            $today = date("w",time());
            switch($today){
                case "0":
                    $sunday = date("m-d", strtotime("Now"));
                    $monday = date("m-d", strtotime("+1 days"));
                    $tuesday = date("m-d", strtotime("+2 days"));
                    $wednesday = date("m-d", strtotime("+3 days"));
                    $thursday = date("m-d", strtotime("+4 days"));
                    $friday = date("m-d", strtotime("+5 days"));
                    $saturday = date("m-d", strtotime("+6 days"));
                    break;
                case "1":
                    $sunday = date("m-d", strtotime("-1 days"));
                    $monday = date("m-d", strtotime("Now"));
                    $tuesday = date("m-d", strtotime("+1 days"));
                    $wednesday = date("m-d", strtotime("+2 days"));
                    $thursday = date("m-d", strtotime("+3 days"));
                    $friday = date("m-d", strtotime("+4 days"));
                    $saturday = date("m-d", strtotime("+5 days"));
                    break;
                case "2":
                    $sunday = date("m-d", strtotime("-2 days"));
                    $monday = date("m-d", strtotime("-1 days"));
                    $tuesday = date("m-d", strtotime("Now"));
                    $wednesday = date("m-d", strtotime("+1 days"));
                    $thursday = date("m-d", strtotime("+2 days"));
                    $friday = date("m-d", strtotime("+3 days"));
                    $saturday = date("m-d", strtotime("+4 days"));
                    break;
                case "3":
                    $sunday = date("m-d", strtotime("-3 days"));
                    $monday = date("m-d", strtotime("-2 days"));
                    $tuesday = date("m-d", strtotime("-1 days"));
                    $wednesday = date("m-d", strtotime("Now"));
                    $thursday = date("m-d", strtotime("+1 days"));
                    $friday = date("m-d", strtotime("+2 days"));
                    $saturday = date("m-d", strtotime("+3 days"));
                    break;
                case "4":
                    $sunday = date("m-d", strtotime("-1 days"));
                    $monday = date("m-d", strtotime("-2 days"));
                    $tuesday = date("m-d", strtotime("-3 days"));
                    $wednesday = date("m-d", strtotime("-4 days"));
                    $thursday = date("m-d", strtotime("Now"));
                    $friday = date("m-d", strtotime("+1 days"));
                    $saturday = date("m-d", strtotime("+2 days"));
                    break;
                case "5":
                    $sunday = date("m-d", strtotime("-1 days"));
                    $monday = date("m-d", strtotime("-2 days"));
                    $tuesday = date("m-d", strtotime("-3 days"));
                    $wednesday = date("m-d", strtotime("-4 days"));
                    $thursday = date("m-d", strtotime("-5 days"));
                    $friday = date("m-d", strtotime("+Now"));
                    $saturday = date("m-d", strtotime("+1 days"));
                    break;
                case "6":
                    $sunday = date("m-d", strtotime("-1 days"));
                    $monday = date("m-d", strtotime("-2 days"));
                    $tuesday = date("m-d", strtotime("-3 days"));
                    $wednesday = date("m-d", strtotime("-4 days"));
                    $thursday = date("m-d", strtotime("-5 days"));
                    $friday = date("m-d", strtotime("-6 days"));
                    $saturday = date("m-d", strtotime("Now"));
                    break;
            }

            $checkSqlWeek = "select color,keyword from happy_diary right join diary on happy_diary.email='$email' where substr(diary.d_date,6,5)='$sunday' or substr(diary.d_date,6,5)='$monday' or
            substr(diary.d_date,6,5)='$tuesday' or substr(diary.d_date,6,5)='$wednesday' or substr(diary.d_date,6,5)='$thursday' or substr(diary.d_date,6,5)='$friday' or substr(diary.d_date,6,5)='$saturday'";
            $weekCheckResult = mysqli_query($conn, $checkSqlWeek);

            if(empty($weekCheckResult)==true){
                throw new exception('이번주 저장된 데이터가 없습니다.', 409);  
            }else{
                while($weekRecord = mysqli_fetch_assoc($weekCheckResult)){
                    array_push($colorArr,$weekRecord['color']);
                    array_push($keywordArr,$weekRecord['keyword']);
                    }
                    $keydata=json_encode(['keyword' => $keywordArr]);
                    $command = escapeshellcmd("python.py " . escapeshellarg(json_encode($keydata)));
                    echo "<img src='wordcloud.png'/>";
                }
            }
        mysqli_close($conn);
        $stat="success";
        }
    }

catch(exception $e) {
    $stat = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data = json_encode(['color' => $colorArr, 'result_code' => $stat, 'error'=> $error ]);
    header('Content-type: application/json'); 
    echo $data;
}

mysqli_close($conn);
?>
