// Profile btn  
const profileButton = document.getElementById("util-btn");
const profileMenu = document.getElementById("prof-Menu");

profileButton.addEventListener("click", toggleMenu.bind(null, profileMenu));
window.addEventListener("click", handleWindowCLick);

function toggleMenu(menu) {
    menu.classList.toggle("hidden");
}

function handleWindowCLick(event) {
    hideMenu(profileMenu, profileButton, event);
}

function hideMenu(menu, button, event) {
    if(!button.contains(event.target) && !menu.contains(event.target)) {
        menu.classList.add("hidden")
    }
}