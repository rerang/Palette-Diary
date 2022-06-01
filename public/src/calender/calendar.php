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

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];

    $checkingEmailExistSql="select * from user where email='$email'";
    $checkingEmailExistResult = mysqli_fetch_assoc(mysqli_query($conn, $checkingEmailExistSql));
    
    $d_date = $json['d_date'];

    $checkSql="select d_date,color from diary where email='$email'and substr(d_date,1,10)='$d_date' order by d_date DESC;";  

    $checkResult = mysqli_query($conn, $checkSql);

    if(!empty($checkResult)) {
        while($checkRow = mysqli_fetch_assoc($checkResult)){
            $dataArr = array();
            $colorArr = array();
            $StrCheckRow=$checkRow['d_date'];
            $DBd_date = substr($StrCheckRow,0,10);
            array_push($dataArr,$DBd_date);
            array_push($colorArr,$checkRow['color']);
            $arrData = json_encode(['d_date' => $dataArr, 'color' => $colorArr]);
            echo $arrData;
            echo "\n";
            }
        mysqli_close($conn);
        $stat="success";
        }
    else{
        throw new exception('d_date가 없습니다.', 422);
    }
}catch(exception $e) {
    $stat = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data = json_encode(['result_code' => $stat, 'error'=> $error ]);
    header('Content-type: application/json'); 
    echo $data;
}

mysqli_close($conn);
?>
