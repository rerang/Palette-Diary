//intro
let intro = document.querySelector(".intro");
let loginContainer = document.querySelector(".container");
let footer = document.querySelector("footer");

let removeIntro = () => {    
    intro.classList.add("hidden");
}
let introAnimation = () =>{
    window.removeEventListener("scroll", introAnimation);
    intro.classList.add("removeIntro");
    loginContainer.classList.remove("hidden");
    footer.classList.remove("hidden");
    intro.addEventListener("animationend", removeIntro);
}

window.addEventListener("scroll", introAnimation);

//signin
const ip = "125.140.42.36:8082";
const url = `http://${ip}/public/src/signIn.php`;
const emailReg = /^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/;
const passwordReg = /^.*(?=^.{8,20}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/ ;

const signInSubmitBtn = document.querySelector("#signInSubmitBtn");

const errorArea = document.querySelector(".errorArea");
const paintError = (errorMessage) =>{
  errorArea.innerHTML = errorMessage;
}

const signInEmail = document.getElementById("signInEmail");
const signInPassword = document.getElementById("signInPassword");

const checkingValidation = (signInEmailValue, signInPasswordValue) => {
  if (signInEmailValue == ""){
    paintError("이메일과 비밀번호를 모두 입력하여 주세요.");
    return false;
  }
  else if (signInPasswordValue == ""){
    paintError("이메일과 비밀번호를 모두 입력하여 주세요.");
    return false;
  }
  else if(signInEmailValue.match(emailReg) == null){
    paintError("이메일 형식이 올바르지 않습니다.");
  }
  else if(signInPasswordValue.match(passwordReg) == null){
    paintError("비밀번호 형식이 올바르지 않습니다.");
  }
  else return signUpEmailValue, signUpPasswordValue;
}
const signInSubmit = async(_event) => {
  _event.preventDefault();
  const signInEmailValue = signInEmail.value.trim();
  const signInPasswordValue = signInPassword.value.trim();

  if(!checkingValidation(signInEmailValue, signInPasswordValue)){
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
          email	: signInEmailValue,
          password : signInPasswordValue
        })
      })
      const data = res.json();
      if(data.result_code == "success") {
        alert("success");
        return;
      }
    } catch (e) {
      //error handler
    }
/*
    fetch(url, {
      method: "POST",
      mode: "cors",
      headers: {
      },
      body: JSON.stringify({//TODO. trim한 값 넣기
        email	: signInEmail.value,
        password : signInPassword.value
      })
    })
    .then(response => response.json()) //응답 결과를 json으로 파싱
    .then(data => {
      console.log(data);
      if(data.result_code == "success") {
        alert("success");
      }
      else{
        alert(data.errorMsg, data.errorCode);
      }
    })
    .catch(err => { // 오류
      console.log("Fetch Error", err);
    });
    */
  }
}
signInSubmitBtn.addEventListener("click", signInSubmit);