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
      else return true
}
const insertThemeUrl = `http://125.140.42.36:8082/public/src/admin/adminThemeManagement/insertTheme.php`;
const insertThemePicUrl = `http://125.140.42.36:8082/public/src/admin/adminThemeManagement/adminThemeUpload.php`;
const backgroundPic = document.querySelector("#backgroundPic");
const themeManagementFormInputArea = document.querySelector("#themeManagementFormInputArea");
const updateImg = async(_event) => {
    let file = new FormData();
    file.append('file', backgroundPic.files[0]);
    try{
        const res = await fetch(insertThemePicUrl, {
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
                    backgroundPic.setAttribute("src", dataResult.url);
                }
            }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }
}
backgroundPic.addEventListener("change", updateImg);


const checkValiAndAddTheme = async(_event) =>{
    let themeUrl = backgroundPic.getAttribute("src");
    let paletteArr = [_event.target.parentNode[2].value.trim(), _event.target.parentNode[3].value.trim(), _event.target.parentNode[4].value.trim(),
    _event.target.parentNode[5].value.trim(), _event.target.parentNode[6].value.trim(), _event.target.parentNode[7].value.trim()];
    let themeName = document.querySelector("#themeName").value;

    if(!checkValidation(themeName, themeUrl, paletteArr)){
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
                    paletteArrString: String(paletteArr.join("")),
                    theme : themeUrl
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
            //alert("no");
        }
    }
} 
insertThemeBtn.addEventListener("click", checkValiAndAddTheme);