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

    $selectDiaryColorInfoSql = "select color, count(color) from diary where d_date in ('$sundayData','$mondayData', '$tuesdayData', '$wednesdayData','$thursdayData', '$fridayData', '$saturdayData') and email='$email' group by color order by 'count(color)' desc limit 5;";
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
            array_push($keywordWordcloudData, $diaryKeywordArr[i].rand(20,100));
        }
        
        mysqli_close($conn);
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> 7db33c6f8daba39801de77ad4183267a74dc420a

        $keywordString = implode(" ",$diaryKeywordArr); 
        $KeywordDataFile = fopen("KeywordData.txt", "w") or die("Unable to open file!");
        fwrite($KeywordDataFile, $keywordString);
        fclose($KeywordDataFile);

<<<<<<< HEAD
        $pythonExe =shall_exec("wordCloud.py");
        echo $pythonExe;
=======
        $pythonExe = shell_exec("wordCloud.py");
>>>>>>> 7db33c6f8daba39801de77ad4183267a74dc420a

        $filename = "KeywordWordcloud.png";
        $handle = fopen("KeywordWordcloud.png", "r");
        $data = fread($handle, filesize($filename));
        $pvars   = array('image' => base64_encode($data));
        $timeout = 30;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $out = curl_exec($curl);
        curl_close ($curl);

        $pms = json_decode($out,true);
        $imgPath = $pms['data']['link'];

<<<<<<< HEAD
        //unlink("KeywordWordcloud.png");
        //unlink("KeywordData.txt");

=======
>>>>>>> 7e9109e22436fc497c24418c4ae63ee255f320a9
=======
        unlink("KeywordWordcloud.png");
        unlink("KeywordData.txt");

>>>>>>> 7db33c6f8daba39801de77ad4183267a74dc420a
        $stat = "success";
    }
} catch(exception $e) {
    $stat = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
} finally{
    $data = json_encode(['color' => $diaryColorArr, 'colorCount' => $diaryColorCountArr, 'imgPath' => $imgPath, 'result_code' => $stat, 'error'=> $error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
