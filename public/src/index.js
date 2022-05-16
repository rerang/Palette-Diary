//onload
const cookieTarget = "token";
let token = "";
document.cookie.split(";").forEach(ele => {
    if(ele.split("=")[0].trim() == cookieTarget){
        token = ele.split("=")[1];
    }
})

window.onload = function(){
  if(token!==""){
    const user_type = JSON.parse(atob(token.split('.')[1]))['user_type'];
    if(user_type == "user"){
      window.location.href = "http://125.140.42.36:8082/public/src/calender/calender.html";
    }
    else{
      window.location.href = "http://125.140.42.36:8082/public/src/admin/admin.html";
    }
  }
}

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
const url = `http://${ip}/public/src/login.php`;
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
  else return signInEmailValue, signInPasswordValue;
}
const signInSubmit = async(_event) => {
  _event.preventDefault();
  const signInEmailValue = signInEmail.value.trim();
  const signInPasswordValue = signInPassword.value.trim();

  if(!checkingValidation(signInEmailValue, signInPasswordValue)){
    return;
  }
  else{
    try{
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
      data.then(
        dataResult => {
          if(dataResult.result_code == "success") {
            document.cookie = 'token=' + dataResult.token+";Path=/;";
            const user_type = JSON.parse(atob(dataResult.token.split('.')[1]))['user_type'];
            if(user_type == "user"){
              localStorage.setItem("theme_code", dataResult.theme_code);
              localStorage.setItem("color_palette", dataResult.color_palette);
              window.location.href = "http://125.140.42.36:8082/public/src/calender/calender.html";
            }
            else{
              window.location.href = "http://125.140.42.36:8082/public/src/admin/admin.html";
            }
          }
          if(dataResult.error != "none"){
            paintError(dataResult.error.errorMsg);
          }
        }
      )
    } catch (e) {
      console.log("Fetch Error", e);
    }
  }
}
signInSubmitBtn.addEventListener("click", signInSubmit);