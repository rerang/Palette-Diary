


const writeDiaryYear = document.querySelector("#writeDiaryYear");
const writeDiaryMonth = document.querySelector("#writeDiaryMonth");
const writeDiaryDay = document.querySelector("#writeDiaryDay");

const writeDiaryDate = localStorage.getItem("writeDiaryDate");
writeDiaryYear.innerHTML = writeDiaryDate.split("-")[0];
writeDiaryMonth.innerHTML = String(writeDiaryDate.split("-")[1]).padStart(2, '0');
writeDiaryDay.innerHTML = String(writeDiaryDate.split("-")[2]).padStart(2, '0');
