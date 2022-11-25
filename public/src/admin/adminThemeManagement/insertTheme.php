<?php
//to solve cors issue
header("Access-Control-Allow-Origin: ");

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

    $theme_name = $json['theme_name'];
    $paletteArrString = $json['paletteArrString'];

    if($_FILES['file']['size'] > 0) { 
        $img = $_FILES['img'];

        if($img['name']=='') {throw new exception('image not exist', 412);}
        else {

            $filename = $img['tmp_name'];
            $handle = fopen($filename, "r");
            $data = fread($handle, filesize($filename));
            $pvars   = array('image' => base64_encode($data));
            $timeout = 30;

            $curl    = curl_init();
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
            $backgroundImgUrl = $pms['data']['link'];

            if($backgroundImgUrl="") {
                $insertThemeSql = "insert into theme(theme_name, background_pic, color_palette) values('$theme_name', '$backgroundImgUrl', '$paletteArrString');";
                $insertThemeResult = mysqli_query($conn, $insertThemeSql);
                mysqli_close($conn);

                if(!$insertThemeResult) {
                    throw new exception('DB Fail - Can Not Insert Theme', 422);
                }
                else {
                    $stat = "success";
                }
            }
            else {
                throw new exception('image upload error', 400);
            }
        }
    }
    
    else {
        throw new exception('image upload error', 400);
    }
} catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
} finally{
    $data =  json_encode(['result_code' => $stat, 'error'=>$error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
