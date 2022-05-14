//find token value in cookie
const cookieTarget = "token";
let token = "";
document.cookie.split(";").forEach(ele => {
    if(ele.split("=")[0].trim() == cookieTarget){
        token = ele.split("=")[1];
    }
})
const payload = JSON.parse(atob(token.split('.')[1]));

//get token value and get email
const email = payload['email'];

const profileInfoEmail = document.querySelector("#profileInfoEmail");
profileInfoEmail.innerText = email;


const changePasswordBtn = document.querySelector("#changePasswordBtn");
const goChangePasswordPage = () => {
    location.href = "/public/src/setting/changePassword/changePassword.html";
}
changePasswordBtn.addEventListener("click", goChangePasswordPage);

const themeSettingBtn = document.querySelector("#themeSettingBtn");
const goThemeSettingPage = () => {
    location.href = "/public/src/setting/themeSetting/themeSetting.html";
}
themeSettingBtn.addEventListener("click", goThemeSettingPage);

const ip = "125.140.42.36:8082";
const url = `http://${ip}/public/src/setting/setting.php`;
const deleteAccountBtn = document.querySelector("#deleteAccountBtn");
const settingModalBg = document.querySelector("#settingModalBg");
const askDeleteAccount = () => {
    let askDeleteAccountModal = document.createElement("div");
    askDeleteAccountModal.id="askDeleteAccountModal";
    askDeleteAccountModal.innerHTML = `<span>계정을 삭제하시겠습니까?</span>
    <div class="askDeleteAccountBtnArea" style="display:grid; grid-template-columns:1fr 1fr; padding: 1rem; column-gap: 2rem;">
    <button id="askDeleteAccountTrueBtn" style="background-color: #cbd8ff; border-radius: 20px; width: 100px padding: 0.25rem 0;">확인</button>
    <button id="askDeleteAccountFalseBtn" style="background-color: #cbd8ff; border-radius: 20px; width: 100px padding: 0.25rem 0;">취소</button>
    </div>`;
    askDeleteAccountModal.setAttribute("style", "width: 350px; height: 120px; position: fixed; z-index: 50; top: calc(50% - 80px); left: calc(50% - 175px); background-color:#f1f5ff; border-radius: 5px; box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25); border: solid 1px #000; text-align: center; padding: 2rem; font-family: Gowun Dodum;");
    settingModalBg.classList.remove("hidden");
    document.querySelector("body").appendChild(askDeleteAccountModal);
    document.querySelector("#askDeleteAccountTrueBtn").addEventListener("click", deleteAccount, true);
    document.querySelector("#askDeleteAccountFalseBtn").addEventListener("click", removeModal, true);
}
const removeModal = () => {
    askDeleteAccountModal.remove();
    settingModalBg.classList.add("hidden");
}
const deleteTokenCookie = () => {
    let date = new Date();
    date.setDate(date.getDate()-1);
    document.cookie = "token=;Path=/;Expires="+date.toUTCString();
}
const deleteAccount = async() => {
    removeModal();
    try{
        const res = await fetch(url, {
          method: 'POST',
          mode: 'cors',
          headers: {
          },
          body: JSON.stringify({
            email : email
          })
        })
        const data = res.json();
        data.then(
          dataResult => {
            if(dataResult.result_code == "success"){
                deleteTokenCookie();
                location.href = "/public/src/index.html";
            }
          }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }
}
deleteAccountBtn.addEventListener("click", askDeleteAccount, true);

const signOutBtn = document.querySelector("#signOutBtn");
const signOut = () => {
    deleteTokenCookie();
    location.href = "/public/src/index.html";
}
signOutBtn.addEventListener("click", signOut);
