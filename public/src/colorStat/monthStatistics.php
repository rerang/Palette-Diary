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

/*try {

    $json = json_decode(file_get_contents('php://input'), TRUE);*/
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
        $keywordAnychartData=array();

        while ($colorRecord = mysqli_fetch_assoc($selectDiaryColorInfoResult)){
            array_push($diaryColorArr, $colorRecord ['color']);
            array_push($diaryColorCountArr, $colorRecord['count(color)']);
        }

        while ($keywordRecord = mysqli_fetch_assoc($selectDiaryKeywordInfoResult)){
            if(strpos($keywordRecord ['keyword'],'')!==false) { //키워드에 공백이 존재한다면 공백으로 구분
                $explodeKeyword=explode(' ',$keywordRecord ['keyword']);
                for($i=0; $i<count($explodeKeyword); $i++) {
                    array_push($diaryKeywordArr,$explodeKeyword[$i]);
                }
            }
            else {
            array_push($diaryKeywordArr, $keywordRecord ['keyword']);
            }
        }
       
        for($i=0; $i<count($diaryKeywordArr); $i++) {
            array_push($keywordAnychartData, $diaryKeywordArr[$i].','.rand(20,100));
        }
        
        mysqli_close($conn);
        $stat = "success";

    }
} catch(exception $e) {
    $stat = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
} finally{
    $data = json_encode(['color' => $diaryColorArr, 'colorCount' => $diaryColorCountArr, 'keyword' => $keywordAnychartData ,'result_code' => $stat, 'error'=> $error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
