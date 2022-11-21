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

//get and paint diary
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
  const dateArr = diaryData.date.split("-");
  diaryYear.innerHTML = dateArr[0];
  diaryMonth.innerHTML = dateArr[1];
  diaryDay.innerHTML = dateArr[2];

  const colorAttr = "background-color:"+diaryData.color;
  diaryColor.setAttribute("style", colorAttr);

  diaryKeyword.innerHTML = diaryData.keyword;
  if(diaryData.mainPic !== null){
    let imgurl = diaryData.mainPic == "" ? "../../assets/noPic.png" : diaryData.mainPic;
    diaryMainPic.setAttribute("src", imgurl);
  }
  if(diaryData.subPic1 !== null){
    let imgurl = diaryData.subPic1 == "" ? "../../assets/noPic.png" : diaryData.subPic1;
    diarySubPic1.setAttribute("src", imgurl);
  }
  if(diaryData.subPic2 !== null){
    let imgurl = diaryData.subPic2 == "" ? "../../assets/noPic.png" : diaryData.subPic2;
    diarySubPic2.setAttribute("src", imgurl);
  }

  diaryBodyArea.innerHTML = diaryData.diary_body;
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
        dataResult => {paintDiary({date:dataResult.d_date, color:dataResult.color, keyword:dataResult.keyword, mainPic:dataResult.mainPic, diary_body:dataResult.diary_body, subPic1:dataResult.subPic1, subPic2:dataResult.subPic2});
            
            if(dataResult.result_code == "success"){ 
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

getDiary();

//delete
const diaryModalBg = document.querySelector("#diaryModalBg");
const deleteDiaryUrl = `http://125.140.42.36:8082/public/src/diary/deleteDiary.php`;
const diaryDeleteBtn = document.querySelector("#diaryDeleteBtn");

const askDeleteDiary = (_event) => {
  let askDeleteDiaryModal = document.createElement("div");
  askDeleteDiaryModal.id="askDeleteDiaryModal";
  askDeleteDiaryModal.innerHTML = `<span>일기를 삭제하시겠습니까?</span>
  <div class="askDeleteDiaryBtnArea">
  <button class="askDeleteDiaryBtn" id="askDeleteDiaryTrueBtn">예</button>
  <button class="askDeleteDiaryBtn" id="askDeleteDiaryFalseBtn">아니오</button>
  </div>`;
  diaryModalBg.classList.remove("hidden");
  document.querySelector("body").appendChild(askDeleteDiaryModal);
  document.querySelector("#askDeleteDiaryTrueBtn").addEventListener("click", deleteDiary, true);
  document.querySelector("#askDeleteDiaryFalseBtn").addEventListener("click", removeModal, true);
}
const deleteDiary = async() => {
  removeModal();
  const deleteDiaryCode = readDiaryCode;
  try{
    const res = await fetch(deleteDiaryUrl, {
      method: 'POST',
      mode: 'cors',
      headers: {
      },
      body: JSON.stringify({
        diary_code : deleteDiaryCode
      })
    })
    const data = res.json();
    data.then(
      dataResult => {
        if(dataResult.result_code == "success"){
            goCalenderPage();
        }
        else{
          if(dataResult.error.errorCode == 409){
            alert(dataResult.error.errorMsg);
          }
          else{
            alert("문제가 발생하였습니다. 관리자에게 문의하세요");
          }
        }
      }
    )
  }catch (e) {
      console.log("Fetch Error", e);
  }
}
const removeModal = () => {
  diaryModalBg.classList.add("hidden");
}
const goCalenderPage = (_event) => {
    localStorage.removeItem("readDiaryCode", readDiaryCode);
    window.location.href = "http://125.140.42.36:8082/public/src/calender/calender.html";
}

diaryDeleteBtn.addEventListener("click", askDeleteDiary);
