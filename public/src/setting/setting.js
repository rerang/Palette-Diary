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

const deleteAccountBtn = document.querySelector("#deleteAccountBtn");
const askDeleteAccount = () => {
    let askDeleteAccountModal = document.createElement("div");
    askDeleteAccountModal.id="askDeleteAccountModal";
    askDeleteAccountModal.innerHTML = `<span>계정을 삭제하시겠습니까?</span>
    <div class="askDeleteAccountBtnArea" style="display:grid; grid-template-columns:1fr 1fr; padding: 1rem; column-gap: 2rem;">
    <button id="askDeleteAccountTrueBtn" style="background-color: #cbd8ff; border-radius: 20px; width: 100px padding: 0.25rem 0;">확인</button>
    <button id="askDeleteAccountFalseBtn" style="background-color: #cbd8ff; border-radius: 20px; width: 100px padding: 0.25rem 0;">취소</button>
    </div>`;
    askDeleteAccountModal.setAttribute("style", "width: 350px; height: 120px; position: fixed; top: calc(50% - 80px); left: calc(50% - 175px); background-color:#f1f5ff; border-radius: 5px; box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25); border: solid 1px #000; text-align: center; padding: 2rem; font-family: Gowun Dodum;");
    document.querySelector("body").appendChild(askDeleteAccountModal);
    document.querySelector("#askDeleteAccountTrueBtn").addEventListener("click", deleteAccount, TRUE);
    document.querySelector("#askDeleteAccountFalseBtn").addEventListener("click", removeModal, TRUE);
}
const removeModal = () => {
    //모달 없애기
}
const deleteAccount = () => {
    removeModal();
    //계정없애기
    //쿠키지우기
    location.href = "/public/src/index.html";
}
deleteAccountBtn.addEventListener("click", askDeleteAccount, true);

const signOutBtn = document.querySelector("#signOutBtn");
const signOut = () => {
    //쿠키 지우기
    location.href = "/public/src/index.html";
}
signOutBtn.addEventListener("click", signOut);
