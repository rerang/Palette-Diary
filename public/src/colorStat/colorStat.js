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
const colorStatStatColorRankArea = document.querySelector("#colorStatStatColorRankArea");
const colorStatStatKeywordImgArea = document.querySelector("#colorStatStatKeywordImgArea");
const getWeekStatUrl = `http://125.140.42.36:8082/public/src/colorStat/weekStatistics.php`;
const getMonthStatUrl = `http://125.140.42.36:8082/public/src/colorStat/monthStatistics.php`;
   
const displayColor = (color, colorCnt) => {
    const maxColorCnt = Math.max(...colorCnt);

    for(let i = 0; i<color.length; i++){
        let colorStatStatColorRankBarArea = document.createElement("div");
        let colorStatStatColorRankBar = document.createElement("div");
        colorStatStatColorRankBarArea.classList.add("colorStatStatColorRankBarArea");
        colorStatStatColorRankBar.classList.add("colorStatStatColorRankBar");
        colorStatStatColorRankBar.setAttribute("width", colorCnt[i]/maxColorCnt*100);
        colorStatStatColorRankBar.setAttribute("background-color", color[i]);
        colorStatStatColorRankBar.innerHTML(color[i]);
        colorStatStatColorRankBarArea.appendChild(colorStatStatColorRankBar);
        colorStatStatColorRankArea.appendChild(colorStatStatColorRankBarArea);
    }
}
const displayKeyword = (imgPath) => {
    let colorStatStatKeywordImg = document.createElement("img");
    colorStatStatKeywordImg.setAttribute("src", imgPath);
    colorStatStatKeywordImg.classList.add("colorStatStatKeywordImg");
    colorStatStatKeywordImgArea.appendChild(colorStatStatKeywordImg);
}
//at first load, show week stat
const displayStat = async() => {
    try{
        const res = await fetch(getMonthStatUrl, {
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
                console.log(dataResult.color, dataResult.colorCount, dataResult.imgPath);
                displayColor(dataResult.color, dataResult.colorCount);
                displayKeyword(dataResult.imgPath);
            }
          }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }
}
displayStat();

