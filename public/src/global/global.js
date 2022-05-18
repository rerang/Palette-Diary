const goLoginLogo = document.querySelector(".goLoginLogo");
if(goLoginLogo){
    const goLoginPage = () => {
        location.href = "/public/src/index.html";
    }

    goLoginLogo.addEventListener("click", goLoginPage);
}

const goCalenderLogo = document.querySelector(".goCalenderLogo");
if(goCalenderLogo){
    const goCalenderPage = () => {
        location.href = "/public/src/calender/calender.html";
    }

    goCalenderLogo.addEventListener("click", goCalenderPage);
}

const goAdminLogo = document.querySelector(".goAdminLogo");
if(goAdminLogo){
    const goAdminPage = () => {
        location.href = "/public/src/admin/admin.html";
    }

    goAdminLogo.addEventListener("click", goAdminPage);
}

const nav_stats = document.querySelector("#nav_stats");
if(nav_stats){
    const goStatsPage = () => {
        location.href = "/public/src/stats/stats.html"
    }

    nav_stats.addEventListener("click", goStatsPage);
}
const nav_happyBank = document.querySelector("#nav_happyBank");
if(nav_happyBank){
    const goHappyBankPage = () => {
        location.href = "/public/src/happyBank/happyBank.html"
    }

    nav_happyBank.addEventListener("click", goHappyBankPage);
}
const nav_setting = document.querySelector("#nav_setting");
if(nav_setting){
    const goSettingPage = () => {
        location.href = "/public/src/setting/setting.html"
    }

    nav_setting.addEventListener("click", goSettingPage);
}
const nav_adminSetting = document.querySelector("#nav_adminSetting");
if(nav_adminSetting){
    const goAdminSettingPage = () => {
        location.href = "/public/src/admin/adminSetting/adminSetting.html"
    }

    nav_adminSetting.addEventListener("click", goAdminSettingPage);
}
const nav_adminThemeManagement = document.querySelector("#nav_adminThemeManagement");
if(nav_adminThemeManagement){
    const goAdminThemeManagementPage = () => {
        location.href = "/public/src/admin/adminThemeManagement/adminThemeManagement.html"
    }

    nav_adminThemeManagement.addEventListener("click", goadminThemeManagementPage);
}