//onload
const cookieTarget = "token";
let token = "";
document.cookie.split(";").forEach(ele => {
    if(ele.split("=")[0].trim() == cookieTarget){
        token = ele.split("=")[1];
    }
})
const payload = JSON.parse(atob(token.split('.')[1]));
const user_type = payload['user_type'];
window.onload = function(){
  if(token==""){
    window.location.href = "http://125.140.42.36:8082";
  }
  else if(user_type == "admin"){
    window.location.href = "http://125.140.42.36:8082/public/src/admin/admin.html";
  }
}

//theme setting
const getThemeUrl = `http://125.140.42.36:8082/public/src/setting/themeSetting/getTheme.php`;
const themeUpdateUrl = `http://125.140.42.36:8082/public/src/setting/themeSetting/themeUpdate.php`;

//get theme and paint
const themeCodeTemp = document.querySelector("#themeCodeTemp");
themeCodeTemp.setAttribute("value", localStorage.getItem("theme_code"));
const themeDisplayArea = document.getElementById("themeDisplayArea");

const paintTheme = (themeCodeArr, backgroundPicArr) => {
    themeCodeArr.forEach((ele, idx) => {
        let theme = document.createElement("img");
        theme.id = ele;
        theme.setAttribute("src", backgroundPicArr[idx]);
        theme.addEventListener("click", changeThemeCodeTempValue);
        themeDisplayArea.append(theme);
    })
    const userTheme = document.getElementById(themeCodeTemp.value);
    userTheme.classList.add("pickedTheme");
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

//display user theme
const displayCurrentPicked = (prev, curr) => {
    let previousPickedTheme = document.getElementById(prev)
    let currentPickedTheme = document.getElementById(curr);
    previousPickedTheme.classList.remove("pickedTheme");
    currentPickedTheme.classList.add("pickedTheme");
}

//changeTemp
const themeUpdateBtn = document.querySelector("#themeUpdateBtn");

const changeThemeCodeTempValue = (_event) => {
    let selectedThemeCode = _event.target.id;
    displayCurrentPicked(themeCodeTemp.value, selectedThemeCode);
    themeCodeTemp.value = selectedThemeCode;
}

//update theme
const themeUpdate = async() => {
    if(themeCodeTemp.value !== ""){
        try{
            const res = await fetch(themeUpdateUrl, {
                method: 'POST',
                mode: 'cors',
                headers: {
                },
                body: JSON.stringify({
                    theme_code: themeCodeTemp.value
                })
            })
            const data = res.json();
            data.then(
                dataResult => {
                    if(dataResult.result_code == "success"){
                        localStorage.setItem("theme_code", dataResult.themeInfo.theme_code);
                        localStorage.setItem("color_palette", dataResult.themeInfo.color_palette);
                        window.location.href="http://125.140.42.36:8082/public/src/setting/setting.html";
                    }
                }
            )
        }catch (e) {
            console.log("Fetch Error", e);
        }
    }
}
themeUpdateBtn.addEventListener("click", themeUpdate);
