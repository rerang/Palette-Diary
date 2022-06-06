<?php
//to solve cors issue
header("Access-Control-Allow-Origin: *");

//db connect
$host = "localhost";
$s_username = "db";
$s_password = "dbpassword";
$dbname = "palette_diary";
$conn = mysqli_connect($host, $s_username, $s_password, $dbname);

//FTP connect
$ftp_server = "125.140.42.36";
$ftp_port = 21;
$ftp_user_name = "paletteDiary";
$ftp_user_pass = "paletteDiary";
$ftp_send_file = "./Palette-Diary/userProfile/";

function uploadedFile($ftp_send_file, $fileName) {
    return iconv("utf-8", "CP949", $ftp_send_file.basename2($fileName));
}
  
function basename2($filename) {
    return preg_replace( '/^.+[\\\\\\/]/', '', $filename);
}
  
try{
    echo $_SERVER['DOCUMENT_ROOT'];
    
    $json = json_decode(file_get_contents('php://input'), TRUE);
    $error = "none";
    $stat = "none";

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];
    
    $explodeEmail=explode("@", $email);
    $Nickname=$explodeEmail[0];
    
    if($_FILES['file']['size'] > 0) { // 업로드 파일여부 확인

        $TmpfileName = explode(".",$_FILES["file"]["name"]); // 첨부하는 파일에서 이름만 떼어와서
        $fileName = $Nickname."_".$TmpfileName[0]; // 해당 user의 email 붙여 구분

        $uploadFile = uploadedFile($ftp_send_file, $fileName);
        
        $fileTypeExt = explode("/", $_FILES['file']['type']);
        $fileType = $fileTypeExt[0]; //image
        $fileExt = $fileTypeExt[1]; //png, jpg 등

        $fileName = $fileName.".".$fileExt;
       
        $extStatus = false;

        switch($fileExt) {
	        case 'jpeg':
	        case 'jpg':
	        case 'gif':
	        case 'bmp':
	        case 'png':
		        $extStatus = true;
		        break;
	        default:
                throw new exception('image type error', 400);
		        break;
        }   

        if($fileType == 'image') {	
	        if($extStatus) {

                    $imgurl = "./Palette-Diary/userProfile/".$fileName;

                    $conn_id = ftp_connect($ftp_server, $ftp_port);
                    ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
                    ftp_pasv($conn_id, true);
                    // 파일 업로드
                    ftp_put($conn_id, $imgurl,$_FILES['file']['tmp_name'], FTP_BINARY);
                    // 파일 가져오기

                    $remote_file = $imgurl;
                    $rootDirectory = $_SERVER['DOCUMENT_ROOT']; //C:/Users/User2/Desktop/palettediary/Palette-Diary

                    $uploadFileDirectory = $rootDirectory.'/userProfile/'.$fileName;

                    if (!file_exists($uploadFileDirectory)) {
                        mkdir($uploadFileDirectory, 0777, true);
                    }

                    $fp = fopen($uploadFileDirectory, 'w+');
                    ftp_fget($conn_id, $fp, $remote_file, FTP_BINARY, 0);
                   
                    ftp_close($conn_id);
                    fclose($fp);

                    $updateImageSql = "update user set profile_pic='$uploadFileDirectory' where email='$email';";
                    $updateImageResult = mysqli_query($conn, $updateImageSql);
                    mysqli_close($conn);
    
                    if(!$updateImageResult) {
                        throw new exception('DB Fail - Can Not Update User', 422);
                    }
                    else{
                        $stat = "success";
                    }
	        }
	        else {
		        throw new exception('image type error', 400);
	        }
        }
        else {
	        throw new exception('image type error', 400);
        }
    }
    else {
        throw new exception('cant image upload', 409);
    }

}catch(exception $e) {
    $stat = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data = json_encode(['result_code' => $stat, 'error'=> $error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
