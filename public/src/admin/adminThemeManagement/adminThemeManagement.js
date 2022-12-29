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

if(user_type == "user"){
    window.location.href = "http://125.140.42.36:8082/public/src/calender/calender.html";
}

//theme management
const getThemeUrl = `http://125.140.42.36:8082/public/src/admin/adminThemeManagement/adminGetTheme.php`;
const deleteThemeUrl = `http://125.140.42.36:8082/public/src/admin/adminThemeManagement/deleteTheme.php`;

//get Theme and paint
const themeCodeTemp = document.querySelector("#themeCodeTemp");
const themeDisplayArea = document.getElementById("themeDisplayArea");

const paintTheme = (themeCodeArr, backgroundPicArr) => {
    themeCodeArr.forEach((ele, idx) => {
        let theme = document.createElement("img");
        theme.id = ele;
        theme.setAttribute("src", backgroundPicArr[idx]);
        theme.addEventListener("click", changeThemeCodeTempValue);
        themeDisplayArea.append(theme);
    })
}

const getTheme = async() => {
    try{
        const res = await fetch(getThemeUrl, {
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
                    paintTheme(dataResult.theme_code, dataResult.background_pic);
                }
            }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }
}
getTheme();

//display picked theme
const displayCurrentPicked = (prev, curr) => {
    if(prev == ""){
        let currentPickedTheme = document.getElementById(curr);
        currentPickedTheme.classList.add("pickedTheme");
    }
    else{
        let previousPickedTheme = document.getElementById(prev);
        let currentPickedTheme = document.getElementById(curr);
        previousPickedTheme.classList.remove("pickedTheme");
        currentPickedTheme.classList.add("pickedTheme");
    }
}

//changeTemp
const themeUpdateBtn = document.querySelector("#themeUpdateBtn");

const changeThemeCodeTempValue = (_event) => {
    let selectedThemeCode = _event.target.id;
    displayCurrentPicked(themeCodeTemp.value, selectedThemeCode);
    themeCodeTemp.value = selectedThemeCode;
}

//delete theme
const deleteThemeBtn = document.querySelector("#deleteThemeBtn");
const settingModalBg = document.querySelector("#settingModalBg");

const removeModal = () => {
    askDeleteThemeModal.remove();
    settingModalBg.classList.add("hidden");
}
const deleteTheme = async() => {
    removeModal();
    try{
        const res = await fetch(deleteThemeUrl, {
            method: 'POST',
            mode: 'cors',
            headers: {
            },
            body: JSON.stringify({
                theme_code: themeCodeTemp.value
            })
        });
        const data = res.json();
        data.then(
            dataResult => {
                if(dataResult.result_code == "success"){
                    window.location.reload();
                }
                else{
                    alert(dataResult.error.errorMsg + "(" + dataResult.error.errorCode + ")");
                }
            }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }
}
const askDeleteTheme = () => {
    let askDeleteThemeModal = document.createElement("div");
    askDeleteThemeModal.id="askDeleteThemeModal";
    askDeleteThemeModal.innerHTML = `<span>테마를 삭제하시겠습니까?</span>
    <div class="askDeleteThemeBtnArea">
    <button id="askDeleteThemeTrueBtn" class="askDeleteThemeBtn">예</button>
    <button id="askDeleteThemeFalseBtn" class="askDeleteThemeBtn">아니오</button>
    </div>`;
    settingModalBg.classList.remove("hidden");
    document.querySelector("body").appendChild(askDeleteThemeModal);
    document.querySelector("#askDeleteThemeTrueBtn").addEventListener("click", deleteTheme, true);
    document.querySelector("#askDeleteThemeFalseBtn").addEventListener("click", removeModal, true);
}
const checkValiAndAskDeleteTheme = () => {
    if(themeCodeTemp.value == ""){
        alert("선택된 테마가 없습니다.");
    }
    else if(themeCodeTemp.value == "00000000001"){
        alert("기본 테마는 삭제할 수 없습니다.");
    }
    else{
        askDeleteTheme();
    }
}
deleteThemeBtn.addEventListener("click", checkValiAndAskDeleteTheme);

//go insert theme page
const insertThemeBtn = document.querySelector("#insertThemeBtn");
const goInsertThemePage = () => {
    window.location.href = "http://125.140.42.36:8082/public/src/admin/adminThemeManagement/adminInsertTheme.html";
}
insertThemeBtn.addEventListener("click", goInsertThemePage);