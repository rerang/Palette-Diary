const ip = "125.140.42.36:8082";
const url = `http://${ip}/public/src/signUp/signUp.php`;
const emailReg = /^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/;
const passwordReg = /^.*(?=^.{8,20}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/ ;

const signUpSubmitBtn = document.querySelector("#signUpSubmitBtn");

const errorArea = document.querySelector(".errorArea");
const paintError = (errorMessage) =>{
  errorArea.innerHTML = errorMessage;
}

const signUpEmail = document.getElementById("signUpEmail");
const signUpPassword = document.getElementById("signUpPassword");
const signUpPasswordConfirm = document.getElementById("signUpPasswordConfirm");

const checkingValidation = (signUpEmailValue, signUpPasswordValue, signUpPasswordConfirmValue) => {
  if (signUpEmailValue == ""){
    paintError("이메일과 비밀번호를 모두 입력하여 주세요.");
    return false;
  }
  else if (signUpPasswordValue == ""){
    paintError("이메일과 비밀번호를 모두 입력하여 주세요.");
    return false;
  }
  else if (signUpPasswordConfirmValue == "") {
    paintError("이메일과 비밀번호를 모두 입력하여 주세요.");
    return false;
  }
  else if(signUpEmailValue.match(emailReg) == null){
    paintError("이메일 형식이 올바르지 않습니다.");
  }
  else if(signUpPasswordValue.match(passwordReg) == null){
    paintError("비밀번호 형식이 올바르지 않습니다.");
  }
  else if(signUpPasswordValue != signUpPasswordConfirmValue){
    paintError("비밀번호와 비밀번호 확인 값이 다릅니다.");
  }
  else return signUpEmailValue, signUpPasswordValue, signUpPasswordConfirmValue;
}
const signUpSubmit = async(_event) => {
  _event.preventDefault();
  const signUpEmailValue = signUpEmail.value.trim();
  const signUpPasswordValue = signUpPassword.value.trim();
  const signUpPasswordConfirmValue = signUpPasswordConfirm.value.trim();

  if(!checkingValidation(signUpEmailValue, signUpPasswordValue, signUpPasswordConfirmValue)){
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
          email	: signUpEmailValue,
          password : signUpPasswordValue
        })
      })
      const data = res.json();
      if(data.result_code == "success") {
        window.location.href = "http://125.140.42.36:8082/public/src/index.html";
      }
    } catch (e) {
      console.log("Fetch Error", err);
    }
  }
}
signUpSubmitBtn.addEventListener("click", signUpSubmit);



