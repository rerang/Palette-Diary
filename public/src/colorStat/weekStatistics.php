<?php
//to solve cors issue
header("Access-Control-Allow-Origin: *");

//db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

//imgur client id
$client_id="dcfa41bdc501573";

try {
    
    $json = json_decode(file_get_contents('php://input'), TRUE);
    $error = "none";
    $stat = "none";

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];

    $yoilArr = array("일","월","화","수","목","금","토");
    $nowData = $yoilArr[date('w', strtotime(date("Y-m-d")))];

    switch($nowData) {
        case "일":
            $sundayData = date("Y-m-d");
            $mondayData = date("Y-m-d",strtotime ("+1 days",strtotime(date("Y-m-d"))));
            $tuesdayData = date("Y-m-d",strtotime ("+2 days",strtotime(date("Y-m-d"))));
            $wednesdayData = date("Y-m-d",strtotime ("+3 days",strtotime(date("Y-m-d"))));
            $thursdayData = date("Y-m-d",strtotime ("+4 days",strtotime(date("Y-m-d"))));
            $fridayData = date("Y-m-d",strtotime ("+5 days",strtotime(date("Y-m-d"))));
            $saturdayData = date("Y-m-d",strtotime ("+6 days",strtotime(date("Y-m-d"))));
            break;
        case "월":
            $sundayData = date("Y-m-d",strtotime ("-1 days",strtotime(date("Y-m-d"))));
            $mondayData = date("Y-m-d");
            $tuesdayData = date("Y-m-d",strtotime ("+1 days",strtotime(date("Y-m-d"))));
            $wednesdayData = date("Y-m-d",strtotime ("+2 days",strtotime(date("Y-m-d"))));
            $thursdayData = date("Y-m-d",strtotime ("+3 days",strtotime(date("Y-m-d"))));
            $fridayData = date("Y-m-d",strtotime ("+4 days",strtotime(date("Y-m-d"))));
            $saturdayData = date("Y-m-d",strtotime ("+5 days",strtotime(date("Y-m-d"))));
            break;
        case "화":
            $sundayData = date("Y-m-d",strtotime ("-2 days",strtotime(date("Y-m-d"))));
            $mondayData = date("Y-m-d",strtotime ("-1 days",strtotime(date("Y-m-d"))));
            $tuesdayData = date("Y-m-d");
            $wednesdayData = date("Y-m-d",strtotime ("+1 days",strtotime(date("Y-m-d"))));
            $thursdayData = date("Y-m-d",strtotime ("+2 days",strtotime(date("Y-m-d"))));
            $fridayData = date("Y-m-d",strtotime ("+3 days",strtotime(date("Y-m-d"))));
            $saturdayData = date("Y-m-d",strtotime ("+4 days",strtotime(date("Y-m-d"))));
            break;
        case "수":
            $sundayData = date("Y-m-d",strtotime ("-3 days",strtotime(date("Y-m-d"))));
            $mondayData = date("Y-m-d",strtotime ("-2 days",strtotime(date("Y-m-d"))));
            $tuesdayData = date("Y-m-d",strtotime ("-1 days",strtotime(date("Y-m-d"))));
            $wednesdayData = date("Y-m-d");
            $thursdayData = date("Y-m-d",strtotime ("+1 days",strtotime(date("Y-m-d"))));
            $fridayData = date("Y-m-d",strtotime ("+2 days",strtotime(date("Y-m-d"))));
            $saturdayData = date("Y-m-d",strtotime ("+3 days",strtotime(date("Y-m-d"))));
            break;
        case "목":
            $sundayData = date("Y-m-d",strtotime ("-4 days",strtotime(date("Y-m-d"))));
            $mondayData = date("Y-m-d",strtotime ("-3 days",strtotime(date("Y-m-d"))));
            $tuesdayData = date("Y-m-d",strtotime ("-2 days",strtotime(date("Y-m-d"))));
            $wednesdayData = date("Y-m-d",strtotime ("-1 days",strtotime(date("Y-m-d"))));
            $thursdayData = date("Y-m-d");
            $fridayData = date("Y-m-d",strtotime ("+1 days",strtotime(date("Y-m-d"))));
            $saturdayData = date("Y-m-d",strtotime ("+2 days",strtotime(date("Y-m-d"))));
            break;
        case "금":  
            $sundayData = date("Y-m-d",strtotime ("-5 days",strtotime(date("Y-m-d"))));
            $mondayData = date("Y-m-d",strtotime ("-4 days",strtotime(date("Y-m-d"))));
            $tuesdayData = date("Y-m-d",strtotime ("-3 days",strtotime(date("Y-m-d"))));
            $wednesdayData = date("Y-m-d",strtotime ("-2 days",strtotime(date("Y-m-d"))));
            $thursdayData = date("Y-m-d",strtotime ("-1 days",strtotime(date("Y-m-d"))));
            $fridayData = date("Y-m-d");
            $saturdayData = date("Y-m-d",strtotime ("+1 days",strtotime(date("Y-m-d"))));
            break;
        case "토":
            $sundayData = date("Y-m-d",strtotime ("-6 days",strtotime(date("Y-m-d"))));
            $mondayData = date("Y-m-d",strtotime ("-5 days",strtotime(date("Y-m-d"))));
            $tuesdayData = date("Y-m-d",strtotime ("-4 days",strtotime(date("Y-m-d"))));
            $wednesdayData = date("Y-m-d",strtotime ("-3 days",strtotime(date("Y-m-d"))));
            $thursdayData = date("Y-m-d",strtotime ("-2 days",strtotime(date("Y-m-d"))));
            $fridayData = date("Y-m-d",strtotime ("-1 days",strtotime(date("Y-m-d"))));
            $saturdayData = date("Y-m-d");
            break;
    }

    $selectDiaryColorInfoSql = "select color, count(color) from diary where d_date in ('$sundayData','$mondayData', '$tuesdayData', '$wednesdayData','$thursdayData', '$fridayData', '$saturdayData') and email='$email' group by color order by count(color) desc limit 5;";
    $selectDiaryColorInfoResult = mysqli_query($conn, $selectDiaryColorInfoSql);

    if(empty($selectDiaryColorInfoResult)==true) {
        throw new exception('DB Fail - Can Not select Diary', 422);
    }

    $selectDiaryKeywordInfoSql = "select keyword from diary where d_date in ('$sundayData','$mondayData', '$tuesdayData', '$wednesdayData','$thursdayData', '$fridayData', '$saturdayData') and email='$email';";
    $selectDiaryKeywordInfoResult = mysqli_query($conn, $selectDiaryKeywordInfoSql);

    if(empty($selectDiaryKeywordInfoResult)==true) {
        throw new exception('DB Fail - Can Not select Diary', 422);
    }
    else {

        $diaryColorArr=array();
        $diaryColorCountArr=array();
        $diaryKeywordArr=array();
        $keywordWordcloudData=array();

        while ($colorRecord = mysqli_fetch_assoc($selectDiaryColorInfoResult)){
            array_push($diaryColorArr, $colorRecord ['color']);
            array_push($diaryColorCountArr, $colorRecord['count(color)']);
        }

        while ($keywordRecord = mysqli_fetch_assoc($selectDiaryKeywordInfoResult)){
            array_push($diaryKeywordArr, $keywordRecord ['keyword']);
        }
       for($i=0; $i<count($diaryKeywordArr); $i++) {
            array_push($keywordWordcloudData, $diaryKeywordArr[$i].rand(20,100));
        }
        
        mysqli_close($conn);
        $stat = "success";

    }
} catch(exception $e) {
    $stat = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
} 
finally{
    $data = json_encode(['color' => $diaryColorArr, 'colorCount' => $diaryColorCountArr, 'result_code' => $stat, 
    'wordCloudData' => $keywordWordcloudData, 'keywordArr' => $diaryKeywordArr, 'error'=> $error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
