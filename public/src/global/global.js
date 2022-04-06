let goLoginLogo = document.querySelector(".goLoginLogo");

let goLoginPage = () =>{
    console.log("hi");
    location.href="/public/src/index.html";
}

goLoginLogo.addEventListener("click", goLoginPage);


