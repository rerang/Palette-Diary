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
    $stat = "none";
    $error = "none";

    $json = json_decode(file_get_contents('php://input'), TRUE);
    $deleteThemeCode = $json['theme_code'];

    $checkingThemeUsingUserExistSql = "select * from user where theme_code='$deleteThemeCode';";
    $checkingThemeUsingUserExistResult = mysqli_fetch_assoc(mysqli_query($conn, $checkingThemeUsingUserExistSql));
    
    $userArr=array();

    while ($userRecord = $checkingThemeUsingUserExistResult){
        array_push($userArr, $userRecord['email']);
    }

    if(!empty($userArr)){
        while ($userRecord = $userArr){
            $changeThemeAsDefaultSql = "update user set theme_code='00000000001' where email='$userRecord';";
            $changeThemeAsDefaultResult = mysqli_query($conn, $changeThemeAsDefaultSql);
            if(!$changeThemeAsDefaultResult){
                throw new exception('DB Fail - Can Not Update User', 422);
            }
        }
    }

    $deleteThemeSql = "delete from theme where theme_code='$deleteThemeCode'";
    $deleteThemeResult = mysqli_query($conn, $deleteThemeSql);

    if($deleteThemeResult){
        $stat = "success";
    }
    else{
        throw new exception('DB Fail - Can Not Delete Theme', 422);
    }

    mysqli_close($conn);
}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data =  json_encode(['result_code' => $stat, 'error'=>$error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
