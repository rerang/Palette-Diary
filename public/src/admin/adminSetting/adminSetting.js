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

//setting
const deleteUrl = `http://125.140.42.36:8082/public/src/global/deleteUser.php`;

//setting - display profile
const email = payload['email'];
const profileInfoEmail = document.querySelector("#profileInfoEmail");
profileInfoEmail.innerText = email;

//setting - go change password
const adminChangePasswordBtn = document.querySelector("#adminChangePasswordBtn");
const goAdminChangePasswordPage = () => {
    location.href = "/public/src/admin/adminChangePassword/adminChangePassword.html";
}
adminChangePasswordBtn.addEventListener("click", goAdminChangePasswordPage);

//setting - delete account & signout
const deleteTokenCookie = () => {
    let date = new Date();
    date.setDate(date.getDate()-1);
    document.cookie = "token=;Path=/;Expires="+date.toUTCString();
}

//setting - delete account
const deleteAccountBtn = document.querySelector("#deleteAccountBtn");
const settingModalBg = document.querySelector("#settingModalBg");
const askDeleteAccount = () => {
    let askDeleteAccountModal = document.createElement("div");
    askDeleteAccountModal.id="askDeleteAccountModal";
    askDeleteAccountModal.innerHTML = `<span>계정을 삭제하시겠습니까?</span>
    <div class="askDeleteAccountBtnArea">
    <button id="askDeleteAccountTrueBtn" class="askDeleteAccountBtn">예</button>
    <button id="askDeleteAccountFalseBtn" class="askDeleteAccountBtn">아니오</button>
    </div>`;
    settingModalBg.classList.remove("hidden");
    document.querySelector("body").appendChild(askDeleteAccountModal);
    document.querySelector("#askDeleteAccountTrueBtn").addEventListener("click", deleteAccount, true);
    document.querySelector("#askDeleteAccountFalseBtn").addEventListener("click", removeModal, true);
}
const removeModal = () => {
    askDeleteAccountModal.remove();
    settingModalBg.classList.add("hidden");
}
const deleteAccount = async() => {
    removeModal();
    try{
        const res = await fetch(deleteUrl, {
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

//setting - signout
const signOutBtn = document.querySelector("#signOutBtn");
const signOut = () => {
    deleteTokenCookie();
    location.href = "/public/src/index.html";
}
signOutBtn.addEventListener("click", signOut);
