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

//happyBank

//display count
const getCountUrl = `http://125.140.42.36:8082/public/src/happyBank/countHappyDiary.php`;
const displayHappyBankCount = document.querySelector("#displayHappyBankCount");
const getHappyBankCount = async() => {
    try{
        const res = await fetch(getCountUrl, {
        method: 'POST',
        mode: 'cors',
        headers: {
        },
        body: JSON.stringify({
        })
        })
        const data = res.json();
        data.then(
            dataResult => {
                if(dataResult.result_code == "success"){
                    displayHappyBankCount.innerHTML = dataResult.count;
                }
            }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }
}
getHappyBankCount();


const happyBankContentsArea = document.querySelector("#happyBankContentsArea");
const displayHappyBankCountContainer = document.querySelector("#displayHappyBankCountContainer");
const modalBg = document.querySelector("#modalBg");


const deleteHappyDiaryUrl = `http://125.140.42.36:8082/public/src/happyBank/deleteHappyDiary.php`;
const openHappyDiary = async() => {
    let diary_code = document.querySelector(".askModal").id;
    removeModal();
    try{
        const res = await fetch(deleteHappyDiaryUrl, {
        method: 'POST',
        mode: 'cors',
        headers: {
        },
        body: JSON.stringify({
            diary_code : diary_code
        })
        })
        const data = res.json();
        data.then(
            dataResult => {
                if(dataResult.result_code == "success"){
                    localStorage.setItem("happy_diary_code", diary_code);
                    window.location.href = "http://125.140.42.36:8082/public/src/happyBank/readHappyDiary.html";
                }
            }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    } 
}

const deleteHappyDiary = async() => {
    let diary_code = document.querySelector(".askModal").id;
    removeModal();
    try{
        const res = await fetch(deleteHappyDiaryUrl, {
        method: 'POST',
        mode: 'cors',
        headers: {
        },
        body: JSON.stringify({
            diary_code : diary_code
        })
        })
        const data = res.json();
        data.then(
            dataResult => {
                if(dataResult.result_code == "success"){
                    location.reload();
                }
            }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }  
}
const removeModal = () => {
    document.querySelector(".askModal").remove();
    modalBg.classList.add("hidden");
}
const askFunc = (diaryCode, type) => {
    let message, func;
    type == "open" ? message = "행복했던 과거를 여시겠습니까?" : message = "저금된 추억을 지우시겠습니까?";

    let askModal = document.createElement("div");
    askModal.classList.add("askModal");
    askModal.id = diaryCode;
    askModal.innerHTML = `<span>`+ message +`</span>
    <div class="askBtnArea">
    <button class="askBtn" id="askModalTrueBtn">예</button>
    <button class="askBtn" id="askModalFalseBtn">아니오</button>
    </div>`;
    modalBg.classList.remove("hidden");
    document.querySelector("body").appendChild(askModal);
    type == "open" 
    ? document.querySelector("#askModalTrueBtn").addEventListener("click", openHappyDiary, true) 
    : document.querySelector("#askModalTrueBtn").addEventListener("click", deleteHappyDiary, true);
    document.querySelector("#askModalFalseBtn").addEventListener("click", removeModal, true);
}
const askOpenHappyDiary = (_event) => {
    if(_event.target.classList.value !== "happyBankDeleteBtn"){
        if(_event.target.classList.value == "happyBankList"){
            let diaryCode = _event.target.id;
            askFunc(diaryCode, "open");
        }
        else{
            let diaryCode = _event.target.parentNode.id;
            askFunc(diaryCode, "open");
        }
    }
}
const askDeleteHappyBankList = (_event) => {
    let diaryCode = _event.currentTarget.parentNode.id;
    askFunc(diaryCode, "delete");
}

const paintHappyBankList = (diaryCodeArr, colorArr, keywordArr) => {
    const displayHappyBankList = document.querySelector("#displayHappyBankList");
    diaryCodeArr.forEach((ele, idx) => {
        let happyBankList = document.createElement("div");
        happyBankList.classList.add("happyBankList");
        happyBankList.id = ele;
        happyBankList.addEventListener("click", askOpenHappyDiary);

        let happyBankColor = document.createElement("div");
        happyBankColor.classList.add("happyBankColor");
        let colorAttr = "background-color:" + colorArr[idx] + ";";
        happyBankColor.setAttribute("style", colorAttr);

        let happyBankKeyword = document.createElement("div");
        happyBankKeyword.classList.add("happyBankKeyword");
        happyBankKeyword.innerHTML = keywordArr[idx];

        let happyBankDeleteBtn = document.createElement("button");
        happyBankDeleteBtn.innerHTML = "X";
        happyBankDeleteBtn.classList.add("happyBankDeleteBtn");
        happyBankDeleteBtn.addEventListener("click", askDeleteHappyBankList);

        happyBankList.append(happyBankColor);
        happyBankList.append(happyBankKeyword);
        happyBankList.append(happyBankDeleteBtn);
        displayHappyBankList.append(happyBankList);
    })
}

const getHappyBankListUrl = `http://125.140.42.36:8082/public/src/happyBank/getHappyDiary.php`;
const slideCountAndDisplayList = async() => {
    displayHappyBankCountContainer.removeEventListener("click", slideCountAndDisplayList);
    happyBankContentsArea.classList.remove("happyBankOnlyCount");
    happyBankContentsArea.classList.add("happyBankWithList");

    let listContainer = document.createElement("div");
    listContainer.classList.add("displayHappyBankListContainer");
    listContainer.id = "displayHappyBankListContainer";
    let displayHappyBankList = document.createElement("div");
    displayHappyBankList.classList.add("displayHappyBankList");
    displayHappyBankList.id = "displayHappyBankList";

    listContainer.append(displayHappyBankList);
    happyBankContentsArea.append(listContainer);

    try{
        const res = await fetch(getHappyBankListUrl, {
        method: 'POST',
        mode: 'cors',
        headers: {
        },
        body: JSON.stringify({
        })
        })
        const data = res.json();
        data.then(
        dataResult => {
                if(dataResult.result_code == "success"){
                    paintHappyBankList(dataResult.diary_code, dataResult.color, dataResult.keyword);
                }
            }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }  
}
displayHappyBankCountContainer.addEventListener("click", slideCountAndDisplayList);
