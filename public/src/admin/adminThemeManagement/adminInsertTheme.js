const insertThemeBtn = document.querySelector("#insertThemeBtn");
const paletteReg = /^#([0-9a-f]{3}){1,2}$/ig;
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
      else if(paletteArr.map(ele=>ele.match(paletteReg)).indexOf(null)>=0){
        alert("색상 정의 내용이 옳지 않습니다.");
        return false;
      }
      else return themeName, backgroundPic, paletteArr;
}
const insertThemeUrl = `http://125.140.42.36:8082/public/src/admin/adminThemeManagement/insertTheme.php`;
const checkValiAndAddTheme = async(_event) =>{
    _event.preventDefault();
    let themeName = _event.target.parentNode[0].value.trim();
    let themeFile = new FormData();
    themeFile.append('file', _event.target.parentNode[1].files[0]);
    let paletteArr = [_event.target.parentNode[2].value.trim(), _event.target.parentNode[3].value.trim(), _event.target.parentNode[4].value.trim(),
    _event.target.parentNode[5].value.trim(), _event.target.parentNode[6].value.trim(), _event.target.parentNode[7].value.trim()];
    console.log(paletteArr.join(""));
    if(!checkValidation(themeName, themeFile, paletteArr)){
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
                    theme_name: themeName,
                    paletteArrString: paletteArr.join("")
                    //,file도 같이 보내기.
                })
            })
            const data = res.json();
            data.then(
                dataResult => {
                    if(dataResult.result_code == "success"){
                        window.location.href="http://125.140.42.36:8082/public/src/admin/adminThemeManagement/adminThemeManagement.html";
                    }
                }
            )
        }catch (e) {
            console.log("Fetch Error", e);
        }
    }
} 
insertThemeBtn.addEventListener("click", checkValiAndAddTheme);