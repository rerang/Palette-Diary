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
const colorStatPresentWeek = document.querySelector("#colorStatPresentWeek");
const colorStatPresentMonth = document.querySelector("#colorStatPresentMonth");
const email = payload['email'];
const colorStatStatColorRankArea = document.querySelector("#colorStatStatColorRankArea");
const colorStatStatKeywordImg = document.querySelector(".colorStatStatKeywordImg");
const getWeekStatUrl = `http://125.140.42.36:8082/public/src/colorStat/weekStatistics.php`;
const getMonthStatUrl = `http://125.140.42.36:8082/public/src/colorStat/monthStatistics.php`;
   

const displayError = () => {
  colorStatStatColorRankArea.innerHTML = "일기를 작성해주세요!";
  colorStatStatKeywordImg.innerHTML = "일기를 작성해주세요!";
}
const displayColor = (color, colorCnt) => {
    const maxColorCnt = Math.max(...colorCnt);
    for(let i = 0; i<color.length; i++){
        let colorStatStatColorRankBarArea = document.createElement("div");
        let colorStatStatColorRankBar = document.createElement("div");
        colorStatStatColorRankBarArea.classList.add("colorStatStatColorRankBarArea");
        colorStatStatColorRankBar.classList.add("colorStatStatColorRankBar");
        colorStatStatColorRankBar.setAttribute("style", "background-color: " + color[i] + ";width: " + colorCnt[i]/maxColorCnt*100 + "%;");
        colorStatStatColorRankBar.innerHTML = color[i];
        colorStatStatColorRankBarArea.appendChild(colorStatStatColorRankBar);
        colorStatStatColorRankArea.appendChild(colorStatStatColorRankBarArea);
    }
}
const displayKeyword = (keywordArr, color) => {
  let keywords = keywordArr.join("").split("#");
  const keywordCnt = {};
  let data = [];
  keywords.forEach((x) => { 
    keywordCnt[x] = (keywordCnt[x] || 0) + 1; 
  });
  Object.keys(keywordCnt).forEach(key => {
    data.push({"x" : key, "value" : keywordCnt[key] * 100});
  })
  
  anychart.onDocumentReady(function () {
    let chart = anychart.tagCloud(data, {
      mode: "byWord", //byChar
      maxItems: 16
    });

    let customColorScale = anychart.scales.linearColor();
    customColorScale.colors(color);
    chart.colorScale(customColorScale);
    chart.colorRange().enabled(false);

    chart.angles([0, 90, 180]);
    chart.textSpacing(3);
    chart.scale(anychart.scales.log());
    chart.minWidth('70%');
    chart.minHeight('70%');
    chart.normal().fontWeight(600);

    chart.tooltip().format("{%yPercentOfTotal}%");

    chart.container("container");
    chart.draw();
  });

}


const displayStat = async(duration) => {
  let url = duration == 'week' ? getWeekStatUrl : getMonthStatUrl;
  colorStatStatColorRankArea.innerHTML = "";
  colorStatStatKeywordImg.innerHTML = "";

    try{
        const res = await fetch(url, {
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
              if(dataResult.color.length !== 0){
                displayColor(dataResult.color, dataResult.colorCount);
                displayKeyword(dataResult.keywordArr, dataResult.color);
              }else{
                displayError();
              }   
            }
            else{
                clearStatStatArea();
            }
          }
        )
    }catch (e) {
        console.log("Fetch Error", e);
    }
}

displayStat('week');

const ChangeColorStatDurationAppearance = () => {
  colorStatPresentWeek.classList.toggle("colorStatDurationSelected");
  colorStatPresentMonth.classList.toggle("colorStatDurationSelected");
}

colorStatPresentWeek.addEventListener("click", () => {
  if(!colorStatPresentWeek.classList.contains("colorStatDurationSelected")){
    ChangeColorStatDurationAppearance();
    displayStat('week');
  }
})
colorStatPresentMonth.addEventListener("click", () => {
  if(!colorStatPresentMonth.classList.contains("colorStatDurationSelected")){
    ChangeColorStatDurationAppearance();
    displayStat('month');
  }
})
