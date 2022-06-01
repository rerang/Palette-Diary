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

try{
    $json = json_decode(file_get_contents('php://input'), TRUE);
    $error = "none";
    $stat = "none";

    $d_date = $json['d_date']; 
    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];

    $checkingEmailExistSql="select * from user where email='$email'";
    $checkingEmailExistResult = mysqli_fetch_assoc(mysqli_query($conn, $checkingEmailExistSql));
    if(empty($checkingEmailExistResult)==true) { 
        throw new exception('login unstable', 402);
    }

    $checkSql="select * from diary where email='$email' and substr(d_date,1,10)='$d_date'";
    $checkResult = mysqli_query($conn, $checkSql);
 
    if(!empty($checkResult)) { 
        while($checkRow = mysqli_fetch_assoc($checkResult)){
            if($d_date == substr($checkRow['d_date'],0,10)){ 
                $diary_codeArr = array();
                $colorArr = array();
                $dataArr = array();
                $mainpicArr = array();
                $keywordArr = array();

                array_push($diary_codeArr,$checkRow['diary_code']);
                array_push($colorArr,$checkRow['color']);
                array_push($dataArr,substr($checkRow['d_date'],0,10));
                array_push($mainpicArr,$checkRow['mainPic']);
                array_push($keywordArr,$checkRow['keyword']);
                $arrData = json_encode(['diary_code' => $diary_codeArr, 'color' => $colorArr, 'd_date' => $dataArr, 
                'mainPic' => $mainpicArr,'keyword'=> $keywordArr]);
                echo $arrData;
                echo "\n";
            }
        }
        mysqli_close($conn);
        $stat="success";
    } else {
        throw new exception('다이어리에 저장된 날짜의 데이터가 없습니다.', 402);
    }
}
catch(exception $e) {
$stat = "error";
$error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
$data = json_encode(['result_code' => $stat, 'error'=> $error ]);
header('Content-type: application/json'); 
echo $data;
}

mysqli_close($conn);
?>
