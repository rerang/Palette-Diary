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

const themeUpdateBtn = document.querySelector("#themeUpdateBtn");
const themeCodeTemp = document.querySelector("#themeCodeTemp");

//테마코드 로컬스토리지에서 가져와서 themeCodeTemp value에 넣기
//테마 요청해서 받아와서 테마 그리기, 그릴때 이벤트 리스너도 같이 만들기
//현재 설정된 테마 표시하기
const getTheme = () => {
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
                    
                }
            }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }
}

const changeThemeCodeTempValue = (_event) => {
    let selectedThemeCode = _event.target.id;
    themeCodeTemp.value = selectedThemeCode;
}

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
                        localStorage.setItem("color_palette", dataResult.themeInfo.theme_code);
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
