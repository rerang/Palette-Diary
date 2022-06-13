<?php
//to solve cors issue
header("Access-Control-Allow-Origin: ");

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
$ftp_send_file = "./Palette-Diary/diaryPicture/";

function uploadedFile($upfile_path, $fileName) {
    return iconv("utf-8", "CP949", $upfile_path.basename2($fileName));
}
  
function basename2($filename) {
    return preg_replace( '/^.+[\\\\\\/]/', '', $filename);
}

try{
    $json = json_decode(file_get_contents('php://input'), TRUE);
    $error = "none";
    $stat = "none";

    $cookie = apache_request_headers()['Cookie'];
    $email = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', explode("=", $cookie)[1])[1]))), TRUE)['email'];

    $todayDiaryCode = $json['diary_code'];
    $todayDiaryDate = $json['d_date']; // 일기의 날짜(yyyy-mm-dd)형식 반환
    $todayColor = $json['color']; // 오늘의 색
    $todayKeyword = $json['keyword']; // 키워드
    $todayDiaryBody = $json['diary_body']; // 일기 내용

    $explodeEmail=explode("@", $email);
    $Nickname=$explodeEmail[0];
    
    if($_FILES['file']['size'] > 0) {

        $TmpfileName = explode(".",$_FILES["file"]["name"]);
        $fileName = $Nickname."_".$TmpfileName[0];

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

                    $imgurl = "./Palette-Diary/diaryPicture/".$fileName;

                    $conn_id = ftp_connect($ftp_server, $ftp_port);
                    ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
                    ftp_pasv($conn_id, true);
                    //업로드
                    ftp_put($conn_id, $imgurl,$_FILES['file']['tmp_name'], FTP_BINARY);
                    //불러오기
                    $remote_file = $imgurl;
                    $rootDirectory = $_SERVER['DOCUMENT_ROOT']; //C:/Users/User2/Desktop/palettediary/Palette-Diary

                    $uploadFileDirectory = $rootDirectory.'/userProfile/'.$fileName;
                    $createFolder=$rootDirectory.'/userProfile';

                    if (!file_exists($createFolder)) {
                        mkdir($createFolder, 0777, true);
                    }

                    $fp = fopen($uploadFileDirectory, 'w+');
                    $adressArr=array();

                    while (ftp_fget($conn_id, $fp, $remote_file, FTP_BINARY, 0)){
                        array_push($adressArr, $uploadFileDirectory);
                    }

                    $todayMainPicture=$adressArr[0];
                    $todaySubPicture1=$adressArr[1];
                    $todaySubPicture1=$adressArr[2];

                    ftp_close($conn_id);
                    fclose($fp);
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

    
    if(!$todayDiaryCode) { // 다이어리 코드가 없다==새로 쓰는 일기
        $insertDiarySql = "insert into diary(email, d_date, color, mainPic, keyword) values('$email','$todayDiaryDate','$todayColor','$todayMainPicture','$todayKeyword');";
        $insertDiaryResult = mysqli_fetch_assoc(mysqli_query($conn, $insertDiarySql));
        
        if(!$insertDiaryResult) {
            throw new exception('DB Fail - Can Not Insert Diary', 422);
        }

        $insertDiaryCode = $insertDiaryResult['diary_code'];

        $insertDiaryDetailSql = "insert into diary_detail (diary_code, diary_body, subPic1, subPic2) values('$insertDiaryCode', '$todayDiaryBody','$todaySubPicture1','$todaySubPicture2');";
        $insertDiaryDetailResult = mysqli_fetch_assoc(mysqli_query($conn, $insertDiaryDetailSql));

        if($insertDiaryDetailResult) {
            throw new exception('DB Fail - Can Not Insert User', 422);
        }
 
        $stat = "success";
    }

    else {// 기존 일기 수정 시

        $updateDiarySql = "update diary set color ='$todayColor', keyword='$todayKeyword', mainPic='$todayMainPicture' where diary_code='$todayDiaryCode';";
        $updateDiaryResult = mysqli_query($conn, $updateDiarySql);

        if(!$updateDiaryResult) {
            throw new exception('DB Fail - Can Not Update Diary', 423);
        }

        $updateDiaryDetailSql = "update diary_detail set diary_body ='$todayDiaryBody', subPic1='$todaySubPicture1', subPic2='$todaySubPicture2' where diary_code='$todayDiaryCode';";
        $updateDiaryDetailResult = mysqli_query($conn, $updateDiaryDetailSql);

        if(!$updateDiaryDetailResult) {
            throw new exception('DB Fail - Can Not Update Diary', 423);
        }

        $stat = "success";
    }

    mysqli_close($conn);

}catch(exception $e) {
    $stat   = "error";
    $error = ['errorMsg' => $e->getMessage(), 'errorCode' => $e->getCode()];
}finally{
    $data = json_encode(['result_code' => $stat, 'error'=> $error]);
    header('Content-type: application/json'); 
    echo $data;
}
?>
