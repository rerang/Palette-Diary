//onload
const cookieTarget = "token";
let token = "";
document.cookie.split(";").forEach(ele => {
    if(ele.split("=")[0].trim() == cookieTarget){
        token = ele.split("=")[1];
    }
})

if(token==""){
  window.location.href = "http://125.140.42.36:8082";
}
const payload = JSON.parse(atob(token.split('.')[1]));
const user_type = payload['user_type'];

if(user_type == "admin"){
  window.location.href = "http://125.140.42.36:8082/public/src/admin/admin.html";
}
const expired = () => {
    localStorage.clear();
    window.location.href = "http://125.140.42.36:8082/";
}

//write diary
const writeDiaryUrl = "http://125.140.42.36:8082/public/src/diary/saveDiary.php";
const getDiary = async() => {
  try{
    const res = await fetch(writeDiaryUrl, {
    method: 'POST',
    mode: 'cors',
    headers: {
    },
    body: JSON.stringify({
        diary_code : readDiaryCode
    })
    })
    const data = res.json();
    data.then(
        dataResult => {
            if(dataResult.result_code == "success"){  
              paintDiary({date:dataResult.d_date, color:dataResult.color, keyword:dataResult.keyword, mainPic:dataResult.mainPic, diary_body:dataResult.diary_body, subPic1:dataResult.subPic1, subPic2:dataResult.subPic2});
            }
            else if(dataResult.error.errorCode == 423){
              expired();
            }
        }
    )
  }catch (e) {
      console.log("Fetch Error", e);
  } 
}