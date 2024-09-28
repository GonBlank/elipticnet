const menu = document.getElementById("lateral_menu");
const menu_button = document.getElementById("menu_button");
const open_icon = document.createElement('img');
open_icon.src = 'img/svg/open-menu.svg';
const close_icon = document.createElement('img');
close_icon.src = 'img/svg/close-menu.svg';


function open_menu() {         //open - close lateral_menu
    if (menu.classList.contains("collapsed")) {
        menu.classList.remove("collapsed")
        menu_button.replaceChildren(close_icon)
    } else {
        menu.classList.add("collapsed")
        menu_button.replaceChildren(open_icon)
    }
}
