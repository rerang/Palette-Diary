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

//get diary
const viewDiaryUrl = "http://125.140.42.36:8082/public/src/diary/getDiary.php";
const readDiaryCode = localStorage.getItem("readDiaryCode");
const getDiary = async() => {
  try{
    const res = await fetch(viewDiaryUrl, {
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
              paintDiary({date:dataResult.d_date, color:dataResult.color, keyword:dataResult.keyword, mainPic:dataResult.mainPic, diary_body:dataResult.diary_body});
            }
        }
    )
  }catch (e) {
      console.log("Fetch Error", e);
  } 
}

const diaryYear = document.querySelector("#diaryYear");
const diaryMonth = document.querySelector("#diaryMonth");
const diaryDay = document.querySelector("#diaryDay");
const diaryColor = document.querySelector("#diaryColor");
const diaryKeyword = document.querySelector("#diaryKeyword");
const diaryMainPic = document.querySelector("#diaryMainPic");
const diarySubPic1 = document.querySelector("#diarySubPic1");
const diarySubPic2 = document.querySelector("#diarySubPic2");
const diaryBodyArea = document.querySelector("#diaryBodyArea");

const paintDiary = (diaryData) => {
  const onlyDate = diaryData['date'].split(" ")[0];
  const onlyDateArr = onlyDate.split("-");
  diaryYear.innerHTML = onlyDateArr[0];
  diaryMonth.innerHTML = onlyDateArr[1];
  diaryDay.innerHTML = onlyDateArr[2];

  const colorAttr = "background-color:"+diaryData['color']+";";
  diaryColor.setAttribute("style", colorAttr);

  diaryKeyword.innerHTML = diaryData['keyword'];
  if(diaryData['mainPic'] !== null){
    diaryMainPic.setAttribute("src", diaryData['mainPic']);
  }
  if(diaryData['subPic1'] !== null){
    diarySubPic1.setAttribute("src", diaryData['subPic1']);
  }
  if(diaryData['subPic2'] !== null){
    diarySubPic2.setAttribute("src", diaryData['subPic2']);
  }

  diaryBodyArea.innerHTML = diaryData['diary_body'];
}

getDiary();