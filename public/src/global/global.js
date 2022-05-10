const goLoginLogo = document.querySelector(".goLoginLogo");
if(goLoginLogo){
    const goLoginPage = () =>{
        location.href="/public/src/index.html";
    }

    goLoginLogo.addEventListener("click", goLoginPage);
}



const goCalenderLogo = document.querySelector(".goCalenderLogo");
if(goCalenderLogo){
    const goCalenderPage = () =>{
        location.href="/public/src/calender/calender.html";
    }

    goCalenderLogo.addEventListener("click", goCalenderPage);
}
 