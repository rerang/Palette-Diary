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

//go adminSetting
const selectBigBtn_adminSetting = document.querySelector("#selectBigBtn_adminSetting");
const goAdminSettingPage = () => {
    location.href = "/public/src/admin/adminSetting/adminSetting.html"
}
selectBigBtn_adminSetting.addEventListener("click", goAdminSettingPage);

//go adminThemeManagement
const selectBigBtn_adminThemeManagement = document.querySelector("#selectBigBtn_adminThemeManagement");
const goAdminThemeManagementPage = () => {
    location.href = "/public/src/setting/themeSetting/themeSetting.html";
}
selectBigBtn_adminThemeManagement.addEventListener("click", goAdminThemeManagementPage);
