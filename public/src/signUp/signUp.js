const ip = "125.140.42.36:8081";
const url = `http://${ip}/public/src/signUp/signUp.php`

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
  // else if(signUpEmailValue !="정규식"){
  //   paintError("이메일 형식이 올바르지 않습니다.");
  // }
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
        email	: signUpEmail.value,
        password : signUpPassword.value
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
signUpSubmitBtn.addEventListener("click", signUpSubmit);



