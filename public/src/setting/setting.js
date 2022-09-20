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

if(user_type == "admin"){
  window.location.href = "http://125.140.42.36:8082/public/src/admin/admin.html";
}

//setting
const deleteUrl = `http://125.140.42.36:8082/public/src/global/deleteUser.php`;
const getProfileImgUrl = `http://125.140.42.36:8082/public/src/setting/getProfileImg.php`;
const uploadProfileUrl = `http://125.140.42.36:8082/public/src/setting/uploadProfile.php`;//changeProfileImg  uploadProfile

//setting - display profile
const email = payload['email'];
const profileInfoEmail = document.querySelector("#profileInfoEmail");
const profileImg = document.querySelector("#profileImg");
profileInfoEmail.innerText = email;
const displayProfileImg = async() => {
    try{
        const res = await fetch(getProfileImgUrl, {
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
                console.log(dataResult.imgPath);
                const path = dataResult.imgPath;
                profileImg.setAttribute("src", path);
            }
          }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }
}
displayProfileImg();

//setting - change profile img
const profileImgFile = document.querySelector("#profileImgFile");
const updateProfileImg = async() => {
  let file = new FormData();
  file.append('file', profileImgFile.files[0]);
  console.log(profileImgFile.files[0]);
  console.log(file);
    try{
        const res = await fetch(uploadProfileUrl, {
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
              const path = dataResult.imgPath;
              window.location.reload();
            }
          }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }
}
profileImgFile.addEventListener("change", updateProfileImg);

//setting - go change password
const changePasswordBtn = document.querySelector("#changePasswordBtn");
const goChangePasswordPage = () => {
    location.href = "/public/src/setting/changePassword/changePassword.html";
}
changePasswordBtn.addEventListener("click", goChangePasswordPage);

//setting - go theme setting
const themeSettingBtn = document.querySelector("#themeSettingBtn");
const goThemeSettingPage = () => {
    location.href = "/public/src/setting/themeSetting/themeSetting.html";
}
themeSettingBtn.addEventListener("click", goThemeSettingPage);


//setting - delete account & signout
const deleteTokenCookieLocalStorage = () => {
    let date = new Date();
    date.setDate(date.getDate()-1);
    document.cookie = "token=;Path=/;Expires="+date.toUTCString();
    localStorage.removeItem("theme_code");
    localStorage.removeItem("color_palette");
}

//setting - delete account
const deleteAccountBtn = document.querySelector("#deleteAccountBtn");
const settingModalBg = document.querySelector("#settingModalBg");
const askDeleteAccount = () => {
    let askDeleteAccountModal = document.createElement("div");
    askDeleteAccountModal.id="askDeleteAccountModal";
    askDeleteAccountModal.innerHTML = `<span>계정을 삭제하시겠습니까?</span>
    <div class="askDeleteAccountBtnArea">
    <button class="askDeleteAccountBtn" id="askDeleteAccountTrueBtn">예</button>
    <button class="askDeleteAccountBtn" id="askDeleteAccountFalseBtn">아니오</button>
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
                deleteTokenCookieLocalStorage();
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
    deleteTokenCookieLocalStorage();
    location.href = "/public/src/index.html";
}
signOutBtn.addEventListener("click", signOut);
