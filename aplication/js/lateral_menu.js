const menu = document.getElementById("lateral_menu");
const menu_button = document.getElementById("menu_button");
const open_icon = document.createElement('img');
open_icon.src = '../img/svg/open-menu.svg';
const close_icon = document.createElement('img');
close_icon.src = '../img/svg/close-menu.svg';

function open_menu(event) {  // open - close lateral_menu
    event.stopPropagation();  // Detener la propagación del clic
    if (menu.classList.contains("collapsed")) {
        menu.classList.remove("collapsed");
        menu_button.replaceChildren(close_icon);
        document.addEventListener('click', close_menu_on_click_outside); // Agregar evento para cerrar al hacer clic fuera
    } else {
        menu.classList.add("collapsed");
        menu_button.replaceChildren(open_icon);
        document.removeEventListener('click', close_menu_on_click_outside); // Remover evento cuando el menú se cierra
    }
}

function close_menu_on_click_outside(event) {
    if (!menu.contains(event.target) && !menu_button.contains(event.target)) {
        menu.classList.add("collapsed");
        menu_button.replaceChildren(open_icon);
        document.removeEventListener('click', close_menu_on_click_outside); // Remover el evento después de cerrar
    }
}

menu_button.addEventListener('click', open_menu);
