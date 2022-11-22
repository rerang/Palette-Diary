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


//go other pages
const goWriteDiaryPage = (_event, date) => {
  console.log(date);
  if(date !== undefined){
    localStorage.setItem("writeDiaryDate", date);
  }else{
    localStorage.setItem("writeDiaryDate", _event.target.id);
  }
  
  window.location.href = "http://125.140.42.36:8082/public/src/diary/writeDiary.html";
}

const goReadDiaryPage = (_event) => {
  localStorage.setItem("readDiaryCode", _event.target.parentElement.parentElement.parentElement.id);
  window.location.href = "http://125.140.42.36:8082/public/src/diary/readDiary.html";
}


//calender get now month info
const nowFullDate = new Date();
const nowYear = nowFullDate.getFullYear();
const nowMonth = String(nowFullDate.getMonth() + 1).padStart(2, '0');

const calenderYear = document.querySelector("#calenderYear");
const calenderMonth = document.querySelector("#calenderMonth");
const calenderDayArea = document.querySelector("#calenderDayArea");

const getMonthInfoUrl = `http://125.140.42.36:8082/public/src/calender/getMonthInfo.php`;
const getNowMonthInfo = async(year, month) => {
  const startDay = new Date(year, month-1, 1).getDay();
  const lastFullDateOfMonth = new Date(year, month, 0);
  const lastDateOfMonth = lastFullDateOfMonth.getDate();

  try{
    const res = await fetch(getMonthInfoUrl, {
      method: 'POST',
      mode: 'cors',
      headers: {
      },
      body: JSON.stringify({
        yearMonth : year + "-" + month
      })
    })
    const data = res.json();
    data.then(
      dataResult => {
        if(dataResult.result_code == "success"){
          calenderYear.innerHTML = year;
          calenderMonth.innerHTML = month;
          calenderDayArea.innerHTML = "";
          for(let i = 0; i<startDay; i++){
            let dateArea = document.createElement("div");
            calenderDayArea.append(dateArea);
          }
          for(let i = 0; i<lastDateOfMonth; i++){
            let d_dateExistIndex = dataResult.dateArr.lastIndexOf(year+"-"+month+"-"+String(i+1).padStart(2, '0'));
            let dateArea = document.createElement("div");
            dateArea.classList.add("dateArea");
            if(d_dateExistIndex>=0){
              let colorDiv = document. createElement("div");
              colorDiv.classList.add("color");
              colorDiv.setAttribute("style", "background-color:"+dataResult.colorArr[d_dateExistIndex]+";");
              colorDiv.innerHTML = i+1;
              dateArea.append(colorDiv);
            }
            else{
              let colorDiv = document. createElement("div");
              colorDiv.classList.add("nonecolor");
              colorDiv.setAttribute("style", "background-color:#ebe9ef;");
              colorDiv.innerHTML = i+1;
              dateArea.append(colorDiv);
            }
            dateArea.id = year+"-"+month+"-"+(i+1);
            dateArea.addEventListener("click", viewCalenderPreview);
            dateArea.addEventListener('dblclick', (event) => {goWriteDiaryPage(event, year+"-"+month+"-"+(i+1))});
            calenderDayArea.append(dateArea);
          }
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
getNowMonthInfo(nowYear, nowMonth);

//preview
const getPreviewUrl = `http://125.140.42.36:8082/public/src/calender/getPreview.php`;
const calenderPreview = document.querySelector("#calenderPreview");
let previewIndex = 0;
let previewFullRow = 1;
let dateInfoArr = [];

const viewCalenderPreview = async(_event) => {
  let date = "none";
  if(_event.target.classList.value == "dateArea"){
    date = _event.target.id;
  }
  else{
    date = _event.target.parentNode.id;
  }
  try{
    const res = await fetch(getPreviewUrl, {
      method: 'POST',
      mode: 'cors',
      headers: {
      },
      body: JSON.stringify({
        date : date
      })
    })
    const data = res.json();
    data.then(
      dataResult => {
        if(dataResult.result_code == "success"){
          if(calenderPreview.classList.contains("hidden")){
            calenderPreview.classList.remove("hidden");
          }
          const calenderPreviewYear = document.querySelector("#calenderPreviewYear");
          const calenderPreviewMonth = document.querySelector("#calenderPreviewMonth");
          const calenderPreviewDate = document.querySelector("#calenderPreviewDate");
          const calenderPreviewColor = document.querySelector("#calenderPreviewColor");
          const calenderPreviewMainPicArea = document.querySelector("#calenderPreviewMainPicArea");
          const calenderPreviewKeyword = document.querySelector("#calenderPreviewKeyword");
          const calenderPreviewLeftArrow = document.querySelector("#calenderPreviewLeftArrow");
          const calenderPreviewRightArrow = document.querySelector("#calenderPreviewRightArrow");
          const calenderPreviewBtnArea = document.querySelector("#calenderPreviewBtnArea");

          calenderPreviewYear.innerHTML = date.split("-")[0];
          calenderPreviewMonth.innerHTML = date.split("-")[1];
          calenderPreviewDate.innerHTML = date.split("-")[2];

          calenderPreviewColor.innerHTML = "";
          calenderPreviewKeyword.innerHTML = "";
          calenderPreviewMainPicArea.innerHTML = "";
          calenderPreviewBtnArea.innerHTML = "";
          calenderPreviewColor.setAttribute("style", "");
          calenderPreview.id = "";

          if(!calenderPreviewLeftArrow.classList.contains("hidden")){
            calenderPreviewLeftArrow.classList.add("hidden");
          }
          if(!calenderPreviewRightArrow.classList.contains("hidden")){
            calenderPreviewRightArrow.classList.add("hidden");
          }

          previewFullRow = dataResult.dateInfo.length;
          previewIndex = 0; 

          if(dataResult.dateInfo == "none"){
            calenderPreviewColor.innerHTML = "?";
            calenderPreviewMainPicArea.innerHTML = `<img src="../../img/noDiary.png" id="calenderPreviewMainPic"></img>`;
            let btn = document.createElement("button");
            btn.classList.add("calenderPreviewBtn");
            btn.id = date;
            btn.innerHTML = "일기쓰러가기";
            btn.addEventListener("click", goWriteDiaryPage);
            calenderPreviewBtnArea.append(btn);
          }
          else{
            dateInfoArr = dataResult.dateInfo;
            let displayArr = dateInfoArr[0];
            calenderPreview.id = displayArr[3];
            calenderPreviewColor.setAttribute("style", "background-color:"+displayArr[0]+";");
            if(displayArr[1] !== null){
              calenderPreviewMainPicArea.innerHTML = `<img src="`+displayArr[1]+`" id="calenderPreviewMainPic"></img>`;
            }
            else{
              calenderPreviewMainPicArea.innerHTML = `<img src="../../img/noDisplayPic.png" id="calenderPreviewMainPic"></img>`;
            }
            calenderPreviewKeyword.innerHTML = displayArr[2];
            if(previewFullRow>1){
              calenderPreviewLeftArrow.classList.remove("hidden");
              calenderPreviewRightArrow.classList.remove("hidden");
              calenderPreviewLeftArrow.addEventListener("click", turnEarlierPreview);
              calenderPreviewRightArrow.addEventListener("click", turnLaterPreview);
            }
            let deleteBtn = document.createElement("button");
            deleteBtn.classList.add("calenderPreviewBtn");
            deleteBtn.innerHTML = "삭제하기";
            deleteBtn.addEventListener("click", askDeleteDiary);
            calenderPreviewBtnArea.append(deleteBtn);
            let goReadDiaryBtn = document.createElement("button");
            goReadDiaryBtn.classList.add("calenderPreviewBtn");
            goReadDiaryBtn.innerHTML = "자세히보기";
            goReadDiaryBtn.addEventListener("click", goReadDiaryPage);
            calenderPreviewBtnArea.append(goReadDiaryBtn);
          }
          const closePreviewBtn = document.querySelector("#closePreviewBtn");
            closePreviewBtn.addEventListener("click", closePreview);
            
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


const checkPreviewIndex = (type, idx) => {
  if(type == "earlier"){
    if(idx == 0){
      return previewFullRow - 1;
    }
    else{
      return --idx;
    }
  }
  else{
    if(idx == previewFullRow-1){
      return 0;
    }
    else{
      return ++idx;
    }
  }
}
const turnLaterPreview = () => {
  previewIndex = checkPreviewIndex("later", previewIndex);
  let paintPreviewInfo = dateInfoArr[previewIndex];
  calenderPreview.id = paintPreviewInfo[3];

  const calenderPreviewColor = document.querySelector("#calenderPreviewColor");
  const calenderPreviewMainPicArea = document.querySelector("#calenderPreviewMainPicArea");
  const calenderPreviewKeyword = document.querySelector("#calenderPreviewKeyword");

  calenderPreviewColor.setAttribute("style", "background-color:" + paintPreviewInfo[0]+";");
  if(paintPreviewInfo[1] !== null){
    calenderPreviewMainPicArea.innerHTML = `<img src="` + paintPreviewInfo[1]+`" id="calenderPreviewMainPic"></img>`;
  }
  else{
    calenderPreviewMainPicArea.innerHTML = `<img src="../../img/noDisplayPic.png" id="calenderPreviewMainPic"></img>`;
  }
  calenderPreviewKeyword.innerHTML = paintPreviewInfo[2];
}
const turnEarlierPreview = () => {
  previewIndex = checkPreviewIndex("earlier", previewIndex);
  let paintPreviewInfo = dateInfoArr[previewIndex];
  calenderPreview.id = paintPreviewInfo[3];
  
  const calenderPreviewColor = document.querySelector("#calenderPreviewColor");
  const calenderPreviewMainPicArea = document.querySelector("#calenderPreviewMainPicArea");
  const calenderPreviewKeyword = document.querySelector("#calenderPreviewKeyword");

  calenderPreviewColor.setAttribute("style", "background-color:" + paintPreviewInfo[0]+";");
  if(paintPreviewInfo[1] !== null){
    calenderPreviewMainPicArea.innerHTML = `<img src="` + paintPreviewInfo[1]+`" id="calenderPreviewMainPic"></img>`;
  }
  else{
    calenderPreviewMainPicArea.innerHTML = `<img src="../../img/noDisplayPic.png" id="calenderPreviewMainPic"></img>`;
  }
  calenderPreviewKeyword.innerHTML = paintPreviewInfo[2];
}

const closePreview = () => {
  const calenderPreviewColor = document.querySelector("#calenderPreviewColor");
  calenderPreviewColor.innerHTML = "";
  if(previewFullRow>1){
    const calenderPreviewLeftArrow = document.querySelector("#calenderPreviewLeftArrow");
    const calenderPreviewRightArrow = document.querySelector("#calenderPreviewRightArrow");
    calenderPreviewLeftArrow.classList.add("hidden");
    calenderPreviewRightArrow.classList.add("hidden");
    calenderPreviewLeftArrow.removeEventListener("click", turnEarlierPreview);
    calenderPreviewRightArrow.removeEventListener("click", turnLaterPreview);
  }
  calenderPreview.classList.add("hidden");
}





//delete diary
const calenderModalBg = document.querySelector("#calenderModalBg");
const deleteDiaryUrl = `http://125.140.42.36:8082/public/src/diary/deleteDiary.php`;
const askDeleteDiary = (_event) => {
  let askDeleteDiaryModal = document.createElement("div");
  askDeleteDiaryModal.id="askDeleteDiaryModal";
  askDeleteDiaryModal.innerHTML = `<span>일기를 삭제하시겠습니까?</span>
  <div class="askDeleteDiaryBtnArea">
  <button class="askDeleteDiaryBtn" id="askDeleteDiaryTrueBtn">예</button>
  <button class="askDeleteDiaryBtn" id="askDeleteDiaryFalseBtn">아니오</button>
  </div>`;
  calenderModalBg.classList.remove("hidden");
  document.querySelector("body").appendChild(askDeleteDiaryModal);
  document.querySelector("#askDeleteDiaryTrueBtn").addEventListener("click", deleteDiary, true);
  document.querySelector("#askDeleteDiaryFalseBtn").addEventListener("click", removeModal, true);
}
const deleteDiary = async() => {
  removeModal();
  const deleteDiaryCode = calenderPreview.id;
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
          window.location.reload();
        }
        else if(dataResult.error.errorCode == 423){
          expired();
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
  askDeleteDiaryModal.remove();
  calenderModalBg.classList.add("hidden");
}


//paint calendar
const paintCalender = (_event) => {
  let year = Number(calenderYear.innerHTML);
  let month = Number(calenderMonth.innerHTML);
  console.log("paintCal", calenderMonth.innerHTML, month);
  if(_event.target.id == "calenderLeftArrow"){
    if(Number(month) == 1){
      year -= 1;
      month = 12;
    }
    else{
      month--;
    }
  }
  else if(_event.target.id == "calenderRightArrow"){
    if(Number(month) == 12){
      year += 1;
      month = 1;
    }
    else{
      month++;
    }
  }
  getNowMonthInfo(year, String(month).padStart(2, '0'));
}
const calenderLeftArrow = document.querySelector("#calenderLeftArrow");
const calenderRightArrow = document.querySelector("#calenderRightArrow");
calenderLeftArrow.addEventListener("click", paintCalender);
calenderRightArrow.addEventListener("click", paintCalender);