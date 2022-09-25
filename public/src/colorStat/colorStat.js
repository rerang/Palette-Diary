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

//colorStat
const email = payload['email'];
const colorStatStatColorColorImgArea = document.querySelector("#colorStatStatColorImgArea");
const colorStatStatColorKeywordImgArea = document.querySelector("#colorStatStatColorKeywordImgArea");
const getColorStatImgUrl = `http://125.140.42.36:8082/public/src/colorStat/colorStatistics.php`;
const displayProfileImg = async() => {
    try{
        const res = await fetch(getColorStatImgUrl, {
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

