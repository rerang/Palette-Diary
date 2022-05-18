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

//admin Change password
const url = `http://125.140.42.36:8082/public/src/global/changePassword.php`;
const passwordReg = /^.*(?=^.{8,20}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/ ;

const changePasswordTrueBtn = document.querySelector("#changePasswordTrueBtn");
const changePasswordFalseBtn = document.querySelector("#changePasswordFalseBtn");

const errorArea = document.querySelector(".errorArea");
const paintError = (errorMessage) =>{
  errorArea.innerHTML = errorMessage;
}

const password = document.getElementById("password");
const changeNewPassword = document.getElementById("changeNewPassword");
const changeNewPasswordConfirm = document.getElementById("changeNewPasswordConfirm");

const checkingValidation = (passwordValue, changeNewPasswordValue, changeNewPasswordConfirmValue) => {
  if (passwordValue == ""){
    paintError("현재 비밀번호 입력하여 주세요.");
    return false;
  }
  else if (changeNewPasswordValue == ""){
    paintError("새 비밀번호를 입력하여 주세요.");
    return false;
  }
  else if (changeNewPasswordConfirmValue == "") {
    paintError("새 비밀번호 확인을 입력하여 주세요.");
    return false;
  }
  else if(passwordValue.match(passwordReg) == null){
    paintError("비밀번호 형식이 올바르지 않습니다.");
  }
  else if(changeNewPasswordValue.match(passwordReg) == null){
    paintError("새 비밀번호 형식이 올바르지 않습니다.");
  }
  else if(changeNewPasswordConfirmValue.match(passwordReg) == null){
    paintError("새 비밀번호 확인 형식이 올바르지 않습니다.");
  }
  else if(changeNewPasswordValue !== changeNewPasswordConfirmValue){
    paintError("새 비밀번호와 새 비밀번호 확인 값이 다릅니다.");
  }
  else return passwordValue, changeNewPasswordValue, changeNewPasswordConfirmValue;
} 
const changePasswordRequest = async(_event) => {
  _event.preventDefault();
  const passwordValue = password.value.trim();
  const changeNewPasswordValue = changeNewPassword.value.trim();
  const changeNewPasswordConfirmValue = changeNewPasswordConfirm.value.trim();

  if(!checkingValidation(passwordValue, changeNewPasswordValue, changeNewPasswordConfirmValue)){
    return;
  }
  else{
    try {
      const res = await fetch(url, {
        method: 'POST',
        mode: 'cors',
        headers: {
        },
        body: JSON.stringify({
          password	: passwordValue,
          changePassword : changeNewPasswordValue
        })
      })
      const data = res.json();
      data.then(
        dataResult => {
          if(dataResult.result_code == "success") {
            goAdminSettingPage();
          }
          if(dataResult.error != "none"){
            paintError(dataResult.error.errorMsg);
          }
        }
      );
    } catch (e) {
      console.log("Fetch Error", e);
    }
  }
}
let goAdminSettingPage = () => {
  window.location.href="http://125.140.42.36:8082/public/src/admin/adminSetting/adminSetting.html";
}
changePasswordTrueBtn.addEventListener("click", changePasswordRequest);
changePasswordFalseBtn.addEventListener("click", goAdminSettingPage);