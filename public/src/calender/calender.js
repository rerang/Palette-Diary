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

//calender get now month info
const nowFullDate = new Date();
const nowYear = nowFullDate.getFullYear();
const nowMonth = String(nowFullDate.getMonth() + 1).padStart(2, '0'); //0~
//const nowDate = String(nowFullDate.getDate()).padStart(2, '0');
//const nowDay = nowFullDate.getDay(); //day of the week sun(0)~

const calenderYear = document.querySelector("#calenderYear");
const calenderMonth = document.querySelector("#calenderMonth");
const calenderDayArea = document.querySelector("#calenderDayArea");

const getMonthInfoUrl = `http://125.140.42.36:8082/public/src/calender/getMonthInfo.php`;
const getNowMonthInfo = async(year, month) => {
  const startDay = new Date(year, month-1, 1).getDay();
  const lastFullDateOfMonth = new Date(year, month, 0);
  const lastDateOfMonth = lastFullDateOfMonth.getDate();

  console.log(lastFullDateOfMonth, lastDateOfMonth);
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
            calenderDayArea.append(dateArea);
          }
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
          calenderPreviewBtnArea.innerHTML = "";
          calenderPreviewColor.setAttribute("style", "");
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
            calenderPreviewMainPicArea.innerHTML = "일기가 없습니다.";
            let btn = document.createElement("button");
            btn.classList.add("calenderPreviewBtn");
            btn.innerHTML = "일기쓰러가기";
            btn.addEventListener("click", goWriteDiaryPage);
            calenderPreviewBtnArea.append(btn);
          }
          else{
            dateInfoArr = dataResult.dateInfo;
            let displayArr = dateInfoArr[0];
            calenderPreviewColor.setAttribute("style", "background-color:"+displayArr[0]+";");
            if(displayArr[1] !== null){
              calenderPreviewMainPicArea.innerHTML = `<img src="`+displayArr[1]+`" id="calenderPreviewMainPic"></img>`;
            }
            else{
              calenderPreviewMainPicArea.innerHTML = "사진이 없습니다.";
            }
            calenderPreviewKeyword.innerHTML = displayArr[2];
            if(previewFullRow>1){
              calenderPreviewLeftArrow.classList.remove("hidden");
              calenderPreviewRightArrow.classList.remove("hidden");
              calenderPreviewLeftArrow.addEventListener("click", turnEarlierPreview);
              calenderPreviewRightArrow.addEventListener("click", turnLaterPreview);
            }
            
            const closePreviewBtn = document.querySelector("#closePreviewBtn");
            closePreviewBtn.addEventListener("click", closePreview, { once : true});
            let deleteBtn = document.createElement("button");
            deleteBtn.classList.add("calenderPreviewBtn");
            deleteBtn.innerHTML = "삭제하기";
            deleteBtn.addEventListener("click", askDeleteDiary);
            calenderPreviewBtnArea.append(deleteBtn);
            let goReadDiaryBtn = document.createElement("button");
            goReadDiaryBtn.classList.add("calenderPreviewBtn");
            goReadDiaryBtn.innerHTML = "삭제하기";
            goReadDiaryBtn.addEventListener("click", goReadDiaryPage);
            calenderPreviewBtnArea.append(goReadDiaryBtn);
          }
        }
      }
    )
  }catch (e) {
      console.log("Fetch Error", e);
  }
}

const goWriteDiaryPage = () => {

}
const askDeleteDiary = () => {

}
const goReadDiaryPage = () => {

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

  const calenderPreviewColor = document.querySelector("#calenderPreviewColor");
  const calenderPreviewMainPicArea = document.querySelector("#calenderPreviewMainPicArea");
  const calenderPreviewKeyword = document.querySelector("#calenderPreviewKeyword");

  calenderPreviewColor.setAttribute("style", "background-color:" + paintPreviewInfo[0]+";");
  if(paintPreviewInfo[1] !== null){
    calenderPreviewMainPicArea.innerHTML = `<img src="` + paintPreviewInfo[1]+`" id="calenderPreviewMainPic"></img>`;
  }
  else{
    calenderPreviewMainPicArea.innerHTML = "사진이 없습니다.";
  }
  calenderPreviewKeyword.innerHTML = paintPreviewInfo[2];
}
const turnEarlierPreview = () => {
  previewIndex = checkPreviewIndex("earlier", previewIndex);
  let paintPreviewInfo = dateInfoArr[previewIndex];
  
  const calenderPreviewColor = document.querySelector("#calenderPreviewColor");
  const calenderPreviewMainPicArea = document.querySelector("#calenderPreviewMainPicArea");
  const calenderPreviewKeyword = document.querySelector("#calenderPreviewKeyword");

  calenderPreviewColor.setAttribute("style", "background-color:" + paintPreviewInfo[0]+";");
  if(paintPreviewInfo[1] !== null){
    calenderPreviewMainPicArea.innerHTML = `<img src="` + paintPreviewInfo[1]+`" id="calenderPreviewMainPic"></img>`;
  }
  else{
    calenderPreviewMainPicArea.innerHTML = "사진이 없습니다.";
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


const paintCalender = (_event) => {
  console.log(_event);
  let year = Number(calenderYear.innerHTML);
  let month = Number(calenderMonth.innerHTML);
  if(_event.target.id == "calenderLeftArrow"){
    if(month == 0){
      year -= 1;
      month = 12;
    }
    else{
      month--;
    }
  }
  else if(_event.target.id == "calenderRightArrow"){
    if(month == 12){
      year += 1;
      month = 0;
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