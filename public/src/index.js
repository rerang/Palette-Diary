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
