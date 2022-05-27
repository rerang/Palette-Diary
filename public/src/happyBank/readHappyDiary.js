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

//get diary
const viewHappyDiaryUrl = "http://125.140.42.36:8082/public/src/happyBank/viewHappyDiary.php";
const happy_diary_code = localStorage.getItem("happy_diary_code");
const getHappyBankDiary = async() => {
  try{
    const res = await fetch(viewHappyDiaryUrl, {
    method: 'POST',
    mode: 'cors',
    headers: {
    },
    body: JSON.stringify({
        diary_code : happy_diary_code
    })
    })
    const data = res.json();
    data.then(
        dataResult => {
            if(dataResult.result_code == "success"){
              console.log(dataResult);
                paintHappyDiary({date:dataResult.d_date, color:dataResult.color, keyword:dataResult.keyword, mainPic:dataResult.mainPic, diary_body:dataResult.diary_body});
                localStorage.removeItem("happy_diary_code", dataResult.diary_code);
            }
        }
    )
  }catch (e) {
      console.log("Fetch Error", e);
  } 
}

const happyBankDiaryYear = document.querySelector("#happyBankDiaryYear");
const happyBankDiaryMonth = document.querySelector("#happyBankDiaryMonth");
const happyBankDiaryDay = document.querySelector("#happyBankDiaryDay");
const happyBankDiaryColor = document.querySelector("#happyBankDiaryColor");
const happyBankDiaryKeyword = document.querySelector("#happyBankDiaryKeyword");
const happyBankDiaryMainPic = document.querySelector("#happyBankDiaryMainPic");
const happyBankDiaryBodyArea = document.querySelector("#happyBankDiaryBodyArea");

const paintHappyDiary = (happyDiaryData) => {
  const onlyDate = happyDiaryData['date'].split(" ")[0];
  const onlyDateArr = onlyDate.split("-");
  happyBankDiaryYear.innerHTML = onlyDateArr[0];
  happyBankDiaryMonth.innerHTML = onlyDateArr[1];
  happyBankDiaryDay.innerHTML = onlyDateArr[2];

  const colorAttr = "background-color:"+happyDiaryData['color']+";";
  happyBankDiaryColor.setAttribute("style", colorAttr);

  happyBankDiaryKeyword.innerHTML = happyDiaryData['keyword'];
  if(happyDiaryData['mainPic'] !== null){
    happyBankDiaryMainPic.setAttribute("src", happyDiaryData['mainPic']);
  }

  happyBankDiaryBodyArea.innerHTML = happyDiaryData['diary_body'];
}

getHappyBankDiary();

//close happy diary
const happyBankDiaryCloseBtn = document.querySelector("#happyBankDiaryCloseBtn");
const goHappyDiaryPage = () => {
    window.location.href = "http://125.140.42.36:8082/public/src/happyBank/happyBank.html";
}
happyBankDiaryCloseBtn.addEventListener("click", goHappyDiaryPage);