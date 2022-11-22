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

//diary init setting
const writeDiaryYear = document.querySelector("#writeDiaryYear");
const writeDiaryMonth = document.querySelector("#writeDiaryMonth");
const writeDiaryDay = document.querySelector("#writeDiaryDay");

let dateString = "";

const writeDateSetting = () => {
  let date = new Date(localStorage.getItem("writeDiaryDate"));
  writeDiaryYear.innerHTML = date.getFullYear();
  writeDiaryMonth.innerHTML = date.getMonth() + 1;
  writeDiaryDay.innerHTML = date.getDate();
  dateString = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
}



const paintDiary = (diaryData) => {
  const writeDiaryYear = document.querySelector("#writeDiaryYear");
  const writeDiaryMonth = document.querySelector("#writeDiaryMonth");
  const writeDiaryDay = document.querySelector("#writeDiaryDay");
  const writeDiaryColor = document.querySelector("#writeDiaryColor");
  const writeDiaryKeyword = document.querySelector("#writeDiaryKeyword");
  const writeDiaryMainPic = document.querySelector("#writeDiaryMainPic");
  const writeDiaryPicPreview = document.querySelector("#writeDiaryPicPreview");
  const writeDiarySubPic1 = document.querySelector("#writeDiarySubPic1");
  const writeDiarySubPic2 = document.querySelector("#writeDiarySubPic2");
  const writeDiaryBody = document.querySelector("#writeDiaryBody");

  const dateArr = diaryData.date.split("-");
  writeDiaryYear.innerHTML = dateArr[0];
  writeDiaryMonth.innerHTML = dateArr[1];
  writeDiaryDay.innerHTML = dateArr[2];
  dateString = diaryData.date;

  writeDiaryColor.value = diaryData.color;

  writeDiaryKeyword.value = diaryData.keyword;
  if(diaryData.mainPic !== null){
    let imgurl = diaryData.mainPic;
    writeDiaryMainPic.setAttribute("src", imgurl);
    writeDiaryPicPreview.setAttribute("src", imgurl);
  }
  if(diaryData.subPic1 !== null){
    let imgurl = diaryData.subPic1;
    writeDiarySubPic1.setAttribute("src", imgurl);
  }
  if(diaryData.subPic2 !== null){
    let imgurl = diaryData.subPic2;
    writeDiarySubPic2.setAttribute("src", imgurl);
  }

  writeDiaryBody.value = diaryData.diary_body;
}
const getDiaryUrl = "http://125.140.42.36:8082/public/src/diary/getDiary.php";
const getEditDiary = async(code) => {
  try{
    const res = await fetch(getDiaryUrl, {
    method: 'POST',
    mode: 'cors',
    headers: {
    },
    body: JSON.stringify({
        diary_code : code
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
const checkWriteAndEdit = () => {
  if(localStorage.getItem("writeDiaryCode") == null){//write
    writeDateSetting();
  }else{
    getEditDiary(localStorage.getItem("writeDiaryCode"));
  }
}
checkWriteAndEdit()

//write diary - pic
const MainPicInput = document.querySelector("#MainPicInput");
const SubPic1Input = document.querySelector("#SubPic1Input");
const SubPic2Input = document.querySelector("#SubPic2Input");
const writeDiaryMainPic = document.querySelector("#writeDiaryMainPic");
const writeDiarySubPic1 = document.querySelector("#writeDiarySubPic1");
const writeDiarySubPic2 = document.querySelector("#writeDiarySubPic2");
const writeDiaryPicPreview = document.querySelector("#writeDiaryPicPreview");

const updateImg = async(num) => {
  let file = new FormData();
  if(num == 0){
    file.append('file', MainPicInput.files[0]);
  }else if(num == 1){
    file.append('file', SubPic1Input.files[0]);
  }else{
    file.append('file', SubPic2Input.files[0]);
  }
    try{
        const res = await fetch(diaryPicUploadUrl, {
          method: 'POST',
          mode: 'cors',
          headers: {
          },
          body: file,
        })
        const data = res.json();
        data.then(
          dataResult => {
            if(dataResult.result_code == "success"){
              if(num == 0){
                writeDiaryMainPic.setAttribute("src", dataResult.url);
                writeDiaryPicPreview.setAttribute("src", dataResult.url);
              }else if(num == 1){
                writeDiarySubPic1.setAttribute("src", dataResult.url);
              }else{
                writeDiarySubPic2.setAttribute("src", dataResult.url);
              }
            }
          }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }
}
MainPicInput.addEventListener("change", () => {updateImg(0)});
SubPic1Input.addEventListener("change", () => {updateImg(1)});
SubPic2Input.addEventListener("change", () => {updateImg(2)});

//ask happy diary
const askHappyDiaryModalBg = document.querySelector("#askHappyDiaryModalBg");
const saveHappyDiaryUrl = "http://125.140.42.36:8082/public/src/happyBank/saveHappyDiary.php";

const askHappyDiary = (code) => {
  let askHappyDiaryModal = document.createElement("div");
  askHappyDiaryModal.id="askHappyDiaryModal";
  askHappyDiaryModal.innerHTML = `<span>해피 다이어리에 저장 하시겠습니까?</span>
  <div class="askHappyDiaryBtnArea">
  <button class="askHappyDiaryBtn" id="askHappyDiaryTrueBtn">예</button>
  <button class="askHappyDiaryBtn" id="askHappyDiaryFalseBtn">아니오</button>
  </div>`;
  askHappyDiaryModalBg.classList.remove("hidden");
  document.querySelector("body").appendChild(askHappyDiaryModal);
  document.querySelector("#askHappyDiaryTrueBtn").addEventListener("click", ()=>{saveHappyDiary(code)}, true);
  document.querySelector("#askHappyDiaryFalseBtn").addEventListener("click", goCalenderPage, true);
}
const goCalenderPage = (_event) => {
    localStorage.removeItem("writeDiaryDate");
    localStorage.removeItem("writeDiaryCode");
    window.location.href = "http://125.140.42.36:8082/public/src/calender/calender.html";
}
const saveHappyDiary = async(code) => {
try{
  const res = await fetch(saveHappyDiaryUrl, {
  method: 'POST',
  mode: 'cors',
  headers: {
  },
  body: JSON.stringify({
    diary_code : code
  })
  })
  const data = res.json();
  data.then(
      dataResult => {
          if(dataResult.result_code == "success"){ 
            localStorage.removeItem("writeDiaryDate");
            localStorage.removeItem("writeDiaryCode"); 
            location.href = "/public/src/calender/calender.html";
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

//write diary
const email = payload['email'];
const writeDiaryUrl = "http://125.140.42.36:8082/public/src/diary/saveDiary.php";
const diaryPicUploadUrl = "http://125.140.42.36:8082/public/src/diary/uploadDiaryPicture.php";
const writeDiarySaveBtn = document.querySelector("#writeDiarySaveBtn");

const saveDiary = async() => {
    let colorValue = document.querySelector("#writeDiaryColor").value;
    let keywordValue = document.querySelector("#writeDiaryKeyword").value;
    let diaryBodyValue = document.querySelector("#writeDiaryBody").value;
    if(keywordValue[0] !== "#"){
      keywordValue = "#" + keywordValue;
    }
    keywordValue = keywordValue.replace(/(\s*)/g, "");
    let code = localStorage.getItem("writeDiaryCode") == null ? "" : localStorage.getItem("writeDiaryCode");
  try{
    const res = await fetch(writeDiaryUrl, {
    method: 'POST',
    mode: 'cors',
    headers: {
    },
    body: JSON.stringify({
        diary_code : code,
        color : colorValue, 
        keyword : keywordValue,
        d_date : dateString,
        mainPic : writeDiaryMainPic.getAttribute("src"),
        diary_body : diaryBodyValue,
        subPic1 : writeDiarySubPic1.getAttribute("src"),
        subPic2 : writeDiarySubPic2.getAttribute("src")
    })
    })
    const data = res.json();
    data.then(
        dataResult => {
            if(dataResult.result_code == "success"){  
              askHappyDiary(dataResult.diary_code);
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
writeDiarySaveBtn.addEventListener("click", saveDiary);




