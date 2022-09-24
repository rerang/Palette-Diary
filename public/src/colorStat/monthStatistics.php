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

    $yearData = substr(date("Ymd"),0,4); //ex)2022
    $monthData = substr(date("Ymd"),4,2); //ex)09

    $selectDiaryColorInfoSql = "select color, count(color) from diary where month(d_date)='$monthData' and year(d_date)='$yearData' and email='$email' group by color order by 'count(color)' desc limit 5;";
    $selectDiaryColorInfoResult = mysqli_query($conn, $selectDiaryColorInfoSql);

    if(empty($selectDiaryColorInfoResult)==true) {
        throw new exception('DB Fail - Can Not select Diary', 422);
    }

    $selectDiaryKeywordInfoSql = "select keyword from diary where month(d_date)='$monthData' and year(d_date)='$yearData' and email='$email';";
    $selectDiaryKeywordInfoResult = mysqli_query($conn, $selectDiaryKeywordInfoSql);

    if(empty($selectDiaryKeywordInfoResult)==true) {
        throw new exception('DB Fail - Can Not select Diary', 422);
    }
    else {

        $diaryColorArr=array();
        $diaryColorCountArr=array();
        $diaryKeywordArr=array();

        while ($colorRecord = mysqli_fetch_assoc($selectDiaryColorInfoResult)){
            array_push($diaryColorArr, $colorRecord ['color']);
            array_push($diaryColorCountArr, $colorRecord['count(color)']);
        }

        while ($keywordRecord = mysqli_fetch_assoc($selectDiaryKeywordInfoResult)){
            array_push($diaryKeywordArr, $keywordRecord ['keyword']);
        }

        mysqli_close($conn);

        $keywordString = implode(" ",$diaryKeywordArr); 
        $KeywordDataFile = fopen("KeywordData.txt", "w") or die("Unable to open file!");
        fwrite($KeywordDataFile, $keywordString);
        fclose($KeywordDataFile);

        $pythonExe = shell_exec("wordCloud.py");

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

        unlink("KeywordWordcloud.png");
        unlink("KeywordData.txt");

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
