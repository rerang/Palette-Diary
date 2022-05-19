const insertThemeBtn = document.querySelector("#insertThemeBtn");
const checkValidation = (themeName, backgroundPic, paletteArr) => {
    //paletteArr = [themePalette_btn, statNavTab_selected, border, statNavTab_notSelected, navSelected, modalBackgroundColor]
    if (themeName == ""){
        alert("테마명을 입력해주세요.");
        return false;
      }
      else if (backgroundPic == ""){
        alert("첨부된 사진이 없습니다.");
        return false;
      }
      else if (paletteArr.indexOf("")>=0) {
        alert("색상 정의를 모두 입력하여 주세요.");
        return false;
      }
      else if(paletteArr.map(ele=>ele.match(paletteReg)).length == 0){
        alert("색상 정의 내용이 옳지 않습니다.");
        return false;
      }
      else return themeName, backgroundPic, paletteArr;
}
const insertThemeUrl = `http://125.140.42.36:8082/public/src/admin/adminThemeManagement/insertTheme.php`;
const checkValiAndAddTheme = async(_event) =>{
    _event.preventDefault();
    let paletteArr = [_event.target.parentNode[2].value, _event.target.parentNode[3].value, _event.target.parentNode[4].value,
    _event.target.parentNode[5].value, _event.target.parentNode[6].value, _event.target.parentNode[7].value];
    if(!checkValidation(_event.target.parentNode[0].value, _event.target.parentNode[1].value, paletteArr)){
        return;
    }
    else{
        try{
            const res = await fetch(insertThemeUrl, {
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
insertThemeBtn.addEventListener("click", checkValiAndAddTheme);

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